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
        // Violência Extrema
        'guerra' => ['name' => 'Guerra', 'color' => '#dc2626', 'icon' => '⚔️'],
        'terrorismo' => ['name' => 'Terrorismo', 'color' => '#ea580c', 'icon' => '💣'],
        'chacina' => ['name' => 'Chacina', 'color' => '#d97706', 'icon' => '🔪'],
        'massacre' => ['name' => 'Massacre', 'color' => '#ca8a04', 'icon' => '💀'],
        'suicidio' => ['name' => 'Suicídio', 'color' => '#65a30d', 'icon' => '⚠️'],
        'tribunal-do-crime' => ['name' => 'Tribunal do Crime', 'color' => '#0891b2', 'icon' => '⚖️'],
        
        // Crimes Violentos
        'homicidio' => ['name' => 'Homicídio', 'color' => '#be123c', 'icon' => '🩸'],
        'assalto' => ['name' => 'Assalto', 'color' => '#9333ea', 'icon' => '🔫'],
        'sequestro' => ['name' => 'Sequestro', 'color' => '#7c3aed', 'icon' => '🚐'],
        'tiroteio' => ['name' => 'Tiroteio', 'color' => '#c026d3', 'icon' => '💥'],
        
        // Acidentes & Tragédias
        'acidentes' => ['name' => 'Acidentes', 'color' => '#0284c7', 'icon' => '🚗'],
        'desastres' => ['name' => 'Desastres', 'color' => '#0369a1', 'icon' => '🌊'],
        
        // Policial & Segurança
        'operacao-policial' => ['name' => 'Operação Policial', 'color' => '#0d9488', 'icon' => '🚔'],
        'faccoes' => ['name' => 'Facções', 'color' => '#059669', 'icon' => '💀'],
        
        // Internacional
        'conflitos' => ['name' => 'Conflitos', 'color' => '#d946ef', 'icon' => '🔥'],
        'execucoes' => ['name' => 'Execuções', 'color' => '#f43f5e', 'icon' => '☠️'],
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
