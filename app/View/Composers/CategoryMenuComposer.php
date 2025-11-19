<?php

namespace App\View\Composers;

use App\Models\Video;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CategoryMenuComposer
{
    /**
     * Category configurations with names and colors
     */
    private $categories = [
        'guerra' => ['name' => 'Guerra', 'color' => '#dc2626'],
        'terrorismo' => ['name' => 'Terrorismo', 'color' => '#ea580c'],
        'chacina' => ['name' => 'Chacina', 'color' => '#d97706'],
        'massacre' => ['name' => 'Massacre', 'color' => '#ca8a04'],
        'suicidio' => ['name' => 'Suicídio', 'color' => '#65a30d'],
        'tribunal-do-crime' => ['name' => 'Tribunal do Crime', 'color' => '#0891b2'],
    ];

    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $categoryStats = Cache::remember('category_stats_today', 300, function () {
            // Get counts for each category from today's approved videos
            $stats = Video::select('category', DB::raw('count(*) as count'))
                ->where('status', 'approved')
                ->whereDate('created_at', today())
                ->groupBy('category')
                ->get()
                ->keyBy('category');

            // Build ALL menu items (sempre visível)
            $menuItems = collect();
            
            foreach ($this->categories as $key => $config) {
                $count = $stats->get($key)?->count ?? 0;
                
                $menuItems->push([
                    'category' => $key,
                    'name' => $config['name'],
                    'color' => $config['color'],
                    'count' => $count,
                ]);
            }

            // Ordenar: categorias com posts primeiro (por contagem desc), depois outras
            return $menuItems->sortByDesc('count')->values();
        });

        $view->with('categoryMenu', $categoryStats);
    }
}
