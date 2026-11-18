<?php

namespace App\Http\Controllers;

use App\Models\Municipality;
use App\Models\Story;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(Request $request): View
    {
        if (! Auth::check()) {
            return view('admin.login');
        }

        $stories = Story::query()
            ->with(['municipality', 'tags'])
            ->latest('updated_at')
            ->paginate(15);

        return view('admin.dashboard', [
            'stories' => $stories,
            'storyCount' => Story::count(),
            'municipalityCount' => Municipality::count(),
            'tagCount' => Tag::count(),
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = Str::transliterate(Str::lower($credentials['email']).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()->withErrors([
                'email' => "{$seconds}秒後に再試行してください。",
            ]);
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, 60);

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'メールアドレスまたはパスワードが正しくありません。']);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.dashboard');
    }
}
