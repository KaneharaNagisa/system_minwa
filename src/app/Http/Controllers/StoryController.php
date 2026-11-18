<?php

namespace App\Http\Controllers;

use App\Models\Municipality;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoryController extends Controller
{
    public function index(Request $request): View
    {
        $municipalityId = $request->string('municipality')->toString();
        $tagId = $request->string('tag')->toString();
        $keyword = $request->string('q')->trim()->toString();

        $stories = Story::query()
            ->with(['municipality', 'tags'])
            ->when($municipalityId !== '', fn ($query) => $query->where('municipality_id', $municipalityId))
            ->when($tagId !== '', fn ($query) => $query->whereHas('tags', fn ($tagQuery) => $tagQuery->where('tags.id', $tagId)))
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($searchQuery) use ($keyword) {
                    $searchQuery
                        ->where('title', 'like', "%{$keyword}%")
                        ->orWhere('summary', 'like', "%{$keyword}%")
                        ->orWhere('body_markdown', 'like', "%{$keyword}%");
                });
            })
            ->latest('updated_at')
            ->paginate(15)
            ->withQueryString();

        $municipalities = Municipality::query()
            ->withCount('stories')
            ->orderBy('name')
            ->get();

        $tags = \App\Models\Tag::query()->orderBy('name')->get();

        return view('welcome', [
            'stories' => $stories,
            'municipalities' => $municipalities,
            'tags' => $tags,
            'selectedMunicipality' => $municipalityId,
            'selectedTag' => $tagId,
            'keyword' => $keyword,
        ]);
    }

    public function show(Story $story): View
    {
        $story->load(['municipality', 'tags']);

        $relatedStories = Story::query()
            ->with(['municipality', 'tags'])
            ->whereKeyNot($story->id)
            ->where(function ($query) use ($story) {
                $query->where('municipality_id', $story->municipality_id);

                if ($story->tags->isNotEmpty()) {
                    $query->orWhereHas('tags', function ($tagQuery) use ($story) {
                        $tagQuery->whereIn('tags.id', $story->tags->pluck('id'));
                    });
                }
            })
            ->latest('updated_at')
            ->limit(3)
            ->get();

        return view('story', [
            'story' => $story,
            'relatedStories' => $relatedStories,
        ]);
    }
}
