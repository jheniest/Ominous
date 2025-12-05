<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    /**
     * Gera o sitemap principal (índice)
     */
    public function index()
    {
        $content = Cache::remember('sitemap_index', 3600, function () {
            $sitemaps = [
                ['loc' => url('/sitemap-pages.xml'), 'lastmod' => now()->toAtomString()],
                ['loc' => url('/sitemap-news.xml'), 'lastmod' => Video::where('is_approved', true)->latest()->first()?->updated_at?->toAtomString() ?? now()->toAtomString()],
                ['loc' => url('/sitemap-categories.xml'), 'lastmod' => now()->toAtomString()],
            ];

            $xml = '<?xml version="1.0" encoding="UTF-8"?>';
            $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
            
            foreach ($sitemaps as $sitemap) {
                $xml .= '<sitemap>';
                $xml .= '<loc>' . $sitemap['loc'] . '</loc>';
                $xml .= '<lastmod>' . $sitemap['lastmod'] . '</lastmod>';
                $xml .= '</sitemap>';
            }
            
            $xml .= '</sitemapindex>';
            
            return $xml;
        });

        return response($content, 200)->header('Content-Type', 'application/xml');
    }

    /**
     * Sitemap de páginas estáticas
     */
    public function pages()
    {
        $content = Cache::remember('sitemap_pages', 3600, function () {
            $pages = [
                ['loc' => url('/'), 'priority' => '1.0', 'changefreq' => 'hourly'],
                ['loc' => route('news.index'), 'priority' => '0.9', 'changefreq' => 'hourly'],
                ['loc' => route('news.updating'), 'priority' => '0.9', 'changefreq' => 'always'],
            ];

            return $this->generateUrlset($pages);
        });

        return response($content, 200)->header('Content-Type', 'application/xml');
    }

    /**
     * Sitemap de notícias (Google News compatível)
     */
    public function news()
    {
        $content = Cache::remember('sitemap_news', 1800, function () {
            // Últimas 1000 notícias aprovadas
            $news = Video::where('is_approved', true)
                ->whereNull('deleted_at')
                ->orderBy('created_at', 'desc')
                ->take(1000)
                ->get();

            $xml = '<?xml version="1.0" encoding="UTF-8"?>';
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';
            
            foreach ($news as $article) {
                // Não incluir mídia de conteúdo sensível no sitemap
                $includeThumbnail = !$article->is_sensitive;
                
                $xml .= '<url>';
                $xml .= '<loc>' . route('news.show', $article->slug) . '</loc>';
                $xml .= '<lastmod>' . $article->updated_at->toAtomString() . '</lastmod>';
                $xml .= '<changefreq>' . ($article->is_updating ? 'always' : 'weekly') . '</changefreq>';
                $xml .= '<priority>' . ($article->is_updating ? '0.9' : '0.7') . '</priority>';
                
                // Google News extension
                $xml .= '<news:news>';
                $xml .= '<news:publication>';
                $xml .= '<news:name>Atrocidades</news:name>';
                $xml .= '<news:language>pt</news:language>';
                $xml .= '</news:publication>';
                $xml .= '<news:publication_date>' . $article->created_at->toAtomString() . '</news:publication_date>';
                $xml .= '<news:title>' . htmlspecialchars(\App\Helpers\SeoHelper::sanitizeForSeo($article->title), ENT_XML1) . '</news:title>';
                $xml .= '</news:news>';
                
                // Imagem apenas para conteúdo não sensível
                if ($includeThumbnail && $article->thumbnail_url) {
                    $xml .= '<image:image>';
                    $xml .= '<image:loc>' . htmlspecialchars($article->thumbnail_url, ENT_XML1) . '</image:loc>';
                    $xml .= '<image:title>' . htmlspecialchars(\App\Helpers\SeoHelper::sanitizeForSeo($article->title), ENT_XML1) . '</image:title>';
                    $xml .= '</image:image>';
                }
                
                $xml .= '</url>';
            }
            
            $xml .= '</urlset>';
            
            return $xml;
        });

        return response($content, 200)->header('Content-Type', 'application/xml');
    }

    /**
     * Sitemap de categorias
     */
    public function categories()
    {
        $content = Cache::remember('sitemap_categories', 3600, function () {
            $categories = [
                'guerra', 'terrorismo', 'chacina', 'massacre', 'suicidio', 'tribunal-do-crime'
            ];

            $urls = [];
            foreach ($categories as $category) {
                $urls[] = [
                    'loc' => route('news.category', $category),
                    'priority' => '0.8',
                    'changefreq' => 'daily',
                ];
            }

            return $this->generateUrlset($urls);
        });

        return response($content, 200)->header('Content-Type', 'application/xml');
    }

    /**
     * Gera XML urlset padrão
     */
    protected function generateUrlset(array $urls): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        foreach ($urls as $url) {
            $xml .= '<url>';
            $xml .= '<loc>' . $url['loc'] . '</loc>';
            if (isset($url['lastmod'])) {
                $xml .= '<lastmod>' . $url['lastmod'] . '</lastmod>';
            }
            $xml .= '<changefreq>' . ($url['changefreq'] ?? 'daily') . '</changefreq>';
            $xml .= '<priority>' . ($url['priority'] ?? '0.5') . '</priority>';
            $xml .= '</url>';
        }
        
        $xml .= '</urlset>';
        
        return $xml;
    }
}
