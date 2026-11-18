<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $story->title }} | 長野県民話アーカイブ</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-washi text-ink">
    <header class="bg-ink px-4 py-4 text-white">
        <div class="mx-auto flex max-w-4xl items-center justify-between">
            <a href="{{ route('stories.index') }}" class="text-sm text-amber-100">← 民話一覧へ</a>
            <span class="text-xs text-stone-300">{{ $story->municipality?->name }}</span>
        </div>
    </header>
    <main class="mx-auto max-w-4xl px-4 py-8">
        <article class="paper-panel p-6 md:p-10">
            <div class="mb-6 flex flex-wrap items-start justify-between gap-4 border-b border-gold/20 pb-5">
                <div>
                    <span class="inline-block border border-gold/40 bg-ink px-2.5 py-0.5 text-xs text-amber-100">{{ $story->municipality?->name }}</span>
                    <h1 class="mt-3 text-2xl font-bold tracking-wider md:text-4xl">{{ $story->title }}</h1>
                    @if ($story->title_kana)<p class="mt-2 text-sm text-muted">{{ $story->title_kana }}</p>@endif
                </div>
                <div class="flex items-center gap-2 text-xs">
                    <button type="button" data-font-size="small" class="font-control">小</button>
                    <button type="button" data-font-size="medium" class="font-control active">中</button>
                    <button type="button" data-font-size="large" class="font-control">大</button>
                </div>
            </div>

            @if ($story->main_image_path)
                <img src="{{ asset('storage/' . $story->main_image_path) }}" alt="{{ $story->title }}" class="mb-6 max-h-[420px] w-full object-cover">
            @endif

            <div id="story-body" class="story-body text-medium">{!! Str::markdown($story->body_markdown) !!}</div>

            @if ($story->spot_name)
                <aside class="mt-8 border-t border-gold/20 pt-5 text-sm">
                    <h2 class="mb-2 font-bold">ゆかりの地</h2>
                    <p>{{ $story->spot_name }}</p>
                    @if ($story->spot_address)<p class="text-muted">{{ $story->spot_address }}</p>@endif
                    @if ($story->spot_map_url)<a href="{{ $story->spot_map_url }}" target="_blank" rel="noopener" class="mt-2 inline-block text-amber-900 underline">Googleマップで見る</a>@endif
                </aside>
            @endif

            @if ($story->source)
                <p class="mt-6 border-t border-gold/20 pt-4 text-xs text-muted">出典・伝承：{{ $story->source }}</p>
            @endif
        </article>

        @if ($relatedStories->isNotEmpty())
            <section class="mt-10">
                <h2 class="mb-5 border-b border-gold/20 pb-2 text-xl font-bold">関連する民話</h2>
                <div class="grid gap-4 md:grid-cols-3">
                    @foreach ($relatedStories as $related)
                        <a href="{{ route('stories.show', $related) }}" class="paper-card">
                            <span class="text-xs text-muted">{{ $related->municipality?->name }}</span>
                            <h3 class="mt-2 font-bold">{{ $related->title }}</h3>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </main>
</body>
</html>
