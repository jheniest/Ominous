<?php

namespace App\View\Composers;

use App\Models\Video;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;

class HeadlinesComposer
{
    /**
     * Category display names
     */
    private $categoryNames = [
        'guerra' => 'Guerra',
        'terrorismo' => 'Terrorismo',
        'chacina' => 'Chacina',
        'massacre' => 'Massacre',
        'suicidio' => 'Suicídio',
        'tribunal-do-crime' => 'Tribunal do Crime',
    ];

    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $headlines = Cache::remember('auth_headlines', 300, function () {
            return Video::select('title', 'category')
                ->where('status', 'approved')
                ->latest()
                ->limit(20)
                ->get()
                ->map(function ($video) {
                    return [
                        'title' => $video->title,
                        'category' => $this->categoryNames[$video->category] ?? 'Notícia',
                    ];
                })
                ->toArray();
        });

        // If no headlines exist, provide sample data
        if (empty($headlines)) {
            $headlines = [
                ['title' => 'Bem-vindo ao Atrocidades', 'category' => 'Sistema'],
                ['title' => 'Portal de notícias exclusivo', 'category' => 'Info'],
                ['title' => 'Acesso por convite', 'category' => 'Sistema'],
            ];
        }

        $view->with('headlines', $headlines);
    }
}
