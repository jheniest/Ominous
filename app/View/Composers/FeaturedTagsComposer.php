<?php

namespace App\View\Composers;

use App\Models\Tag;
use App\Models\Video;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;

class FeaturedTagsComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $featuredTags = Cache::remember('featured_tags_with_counts', 300, function () {
            $tags = Tag::featured()
                ->withCount(['videos' => function ($query) {
                    $query->where('videos.status', 'approved')
                          ->whereDate('videos.created_at', today());
                }])
                ->get()
                ->filter(function ($tag) {
                    return $tag->videos_count > 0;
                })
                ->map(function ($tag) {
                    return [
                        'name' => $tag->name,
                        'slug' => $tag->slug,
                        'color' => $tag->color,
                        'count' => $tag->videos_count,
                    ];
                });
            
            return $tags;
        });

        $view->with('featuredTags', $featuredTags);
    }
}
