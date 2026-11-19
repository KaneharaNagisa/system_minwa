<?php

namespace App\Http\Controllers;

use App\Models\Municipality;
use App\Models\Story;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoryController extends Controller
{
    public function index(Request $request): View
    {
        $municipalityId = $request->string('municipality')->toString();
        $tagId = $request->string('tag')->toString();
        $keyword = $request->string('q')->trim()->toString();
        $mapAreas = json_decode(
            file_get_contents(resource_path('data/nagano-municipalities.json')),
            true,
            flags: JSON_THROW_ON_ERROR,
        );
        $mapNamesByCode = array_column($mapAreas, 'name', 'code');
        $mapMunicipalityCode = null;

        if (preg_match('/^area-(\d{5})$/D', $municipalityId, $matches) === 1) {
            $mapMunicipalityCode = $matches[1];
        }

        $stories = Story::query()
            ->with(['municipality', 'tags'])
            ->when(
                $mapMunicipalityCode !== null,
                fn ($query) => $query->whereHas(
                    'municipality',
                    fn ($municipalityQuery) => $municipalityQuery->where(
                        'name',
                        $mapNamesByCode[$mapMunicipalityCode] ?? '',
                    ),
                ),
                fn ($query) => $query->when(
                    $municipalityId !== '',
                    fn ($municipalityQuery) => $municipalityQuery->where('municipality_id', $municipalityId),
                ),
            )
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
        $prefectureWideMunicipality = $municipalities
            ->firstWhere('name', '長野県全域');
        $municipalitiesByName = $municipalities->keyBy('name');
        $mapAreas = array_map(function (array $mapArea) use ($municipalitiesByName): array {
            $municipality = $municipalitiesByName->get($mapArea['name']);

            return [
                ...$mapArea,
                'municipality_id' => $municipality?->id,
                'stories_count' => $municipality?->stories_count ?? 0,
                'selection_value' => $municipality?->id ?? 'area-'.$mapArea['code'],
            ];
        }, $mapAreas);
        usort($mapAreas, fn (array $first, array $second): int => strcmp($first['name'], $second['name']));

        $tags = Tag::query()->orderBy('name')->get();

        return view('welcome', [
            'stories' => $stories,
            'registeredStoryCount' => Story::count(),
            'municipalities' => $municipalities,
            'prefectureWideMunicipality' => $prefectureWideMunicipality,
            'mapAreas' => $mapAreas,
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
