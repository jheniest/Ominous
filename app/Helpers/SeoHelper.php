<?php

namespace App\Helpers;

class SeoHelper
{
    /**
     * Palavras sensíveis que podem causar shadowban ou restrições
     * Serão substituídas por versões mais amigáveis para SEO
     */
    protected static array $sensitiveWords = [
        // Violência extrema
        'gore' => 'conteúdo explícito',
        'morte' => 'falecimento',
        'morto' => 'vítima fatal',
        'mortos' => 'vítimas fatais',
        'morreu' => 'veio a óbito',
        'morreram' => 'vieram a óbito',
        'assassinato' => 'homicídio',
        'assassinado' => 'vítima de homicídio',
        'matou' => 'tirou a vida',
        'matar' => 'tirar a vida',
        'massacre' => 'tragédia coletiva',
        'chacina' => 'crime múltiplo',
        'decapitado' => 'vítima de violência extrema',
        'decapitação' => 'violência extrema',
        'esquartejado' => 'corpo encontrado',
        'mutilado' => 'ferido gravemente',
        'execução' => 'crime violento',
        'executado' => 'vítima de crime',
        'sangue' => 'ferimentos',
        'sangrento' => 'violento',
        
        // Suicídio (muito sensível para SEO)
        'suicídio' => 'óbito',
        'suicidio' => 'óbito',
        'suicida' => 'pessoa em crise',
        'se matou' => 'veio a óbito',
        'tirou a própria vida' => 'faleceu',
        
        // Terrorismo
        'terrorismo' => 'ataque',
        'terrorista' => 'extremista',
        'bomba' => 'explosivo',
        'explosão' => 'incidente',
        
        // Crimes
        'estupro' => 'violência sexual',
        'estuprador' => 'agressor sexual',
        'pedófilo' => 'criminoso sexual',
        'pedofilia' => 'crime contra menor',
        
        // Drogas (se aplicável)
        'traficante' => 'criminoso',
        'tráfico' => 'crime organizado',
    ];

    /**
     * Categorias com nomes amigáveis para SEO
     */
    protected static array $categoryNames = [
        'guerra' => 'Conflitos Internacionais',
        'terrorismo' => 'Segurança Global',
        'chacina' => 'Crimes Violentos',
        'massacre' => 'Tragédias',
        'suicidio' => 'Saúde Mental',
        'tribunal-do-crime' => 'Justiça Criminal',
    ];

    /**
     * Sanitiza texto para SEO (meta descriptions, títulos, etc.)
     * Remove/substitui palavras que podem causar shadowban
     */
    public static function sanitizeForSeo(?string $text): string
    {
        if (!$text) {
            return '';
        }

        $result = $text;
        
        foreach (self::$sensitiveWords as $sensitive => $safe) {
            $result = preg_replace('/\b' . preg_quote($sensitive, '/') . '\b/iu', $safe, $result);
        }

        return $result;
    }

    /**
     * Gera meta description otimizada (máx 160 caracteres)
     */
    public static function generateMetaDescription(?string $content, int $maxLength = 155): string
    {
        if (!$content) {
            return 'Acompanhe as últimas notícias e acontecimentos. Informação verificada e atualizada em tempo real.';
        }

        // Remove HTML
        $text = strip_tags($content);
        
        // Sanitiza para SEO
        $text = self::sanitizeForSeo($text);
        
        // Limita tamanho
        if (strlen($text) > $maxLength) {
            $text = substr($text, 0, $maxLength - 3) . '...';
        }

        return $text;
    }

    /**
     * Gera título otimizado para SEO
     */
    public static function generateTitle(string $title, ?string $suffix = null): string
    {
        $sanitized = self::sanitizeForSeo($title);
        
        // Limita título (Google exibe ~60 caracteres)
        if (strlen($sanitized) > 55) {
            $sanitized = substr($sanitized, 0, 52) . '...';
        }

        if ($suffix) {
            return $sanitized . ' - ' . $suffix;
        }

        return $sanitized;
    }

    /**
     * Retorna nome amigável da categoria para SEO
     */
    public static function getCategoryName(string $category): string
    {
        return self::$categoryNames[$category] ?? ucfirst($category);
    }

    /**
     * Gera keywords relevantes baseadas no conteúdo
     */
    public static function generateKeywords(string $title, ?string $description = null, ?string $category = null): string
    {
        $keywords = ['notícias', 'atualidades', 'brasil'];
        
        if ($category) {
            $keywords[] = self::getCategoryName($category);
        }

        // Extrai palavras-chave do título (não sensíveis)
        $words = preg_split('/\s+/', strtolower($title));
        $stopWords = ['de', 'da', 'do', 'das', 'dos', 'em', 'no', 'na', 'nos', 'nas', 'o', 'a', 'os', 'as', 'um', 'uma', 'e', 'é', 'que', 'para', 'por', 'com', 'se', 'foi', 'são'];
        
        foreach ($words as $word) {
            $word = trim($word, '.,!?;:');
            if (strlen($word) > 3 && !in_array($word, $stopWords)) {
                // Não adiciona palavras sensíveis às keywords
                $isSensitive = false;
                foreach (array_keys(self::$sensitiveWords) as $sensitive) {
                    if (stripos($word, $sensitive) !== false) {
                        $isSensitive = true;
                        break;
                    }
                }
                if (!$isSensitive) {
                    $keywords[] = $word;
                }
            }
        }

        return implode(', ', array_unique(array_slice($keywords, 0, 10)));
    }

    /**
     * Verifica se conteúdo é sensível e deve ter indexação limitada
     */
    public static function isSensitiveContent(?string $content, ?string $category = null): bool
    {
        // Categorias sempre sensíveis
        $sensitiveCategories = ['suicidio', 'chacina', 'massacre'];
        
        if ($category && in_array($category, $sensitiveCategories)) {
            return true;
        }

        if (!$content) {
            return false;
        }

        // Verifica palavras muito sensíveis no conteúdo
        $verySensitive = ['gore', 'decapitado', 'decapitação', 'esquartejado', 'mutilado', 'suicídio', 'suicida'];
        $lowerContent = strtolower($content);
        
        foreach ($verySensitive as $word) {
            if (stripos($lowerContent, $word) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retorna meta robots apropriado baseado na sensibilidade
     */
    public static function getMetaRobots(bool $isSensitive = false, bool $hasMedia = true): string
    {
        if ($isSensitive) {
            // Indexa o texto mas não as imagens/vídeos
            return 'index, follow, noimageindex';
        }

        if ($hasMedia) {
            // Conteúdo normal com mídia - indexa tudo menos vídeos no Google Video
            return 'index, follow, max-video-preview:0';
        }

        return 'index, follow';
    }

    /**
     * Gera Schema.org JSON-LD para artigo de notícia
     */
    public static function generateArticleSchema(array $article): string
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'NewsArticle',
            'headline' => self::sanitizeForSeo($article['title']),
            'description' => self::generateMetaDescription($article['description'] ?? $article['title']),
            'image' => $article['image'] ?? '',
            'datePublished' => $article['published_at'] ?? now()->toIso8601String(),
            'dateModified' => $article['updated_at'] ?? $article['published_at'] ?? now()->toIso8601String(),
            'author' => [
                '@type' => 'Organization',
                'name' => 'Atrocidades',
                'url' => config('app.url'),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'Atrocidades',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('images/logo.png'),
                ],
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $article['url'] ?? url()->current(),
            ],
        ];

        // Adiciona categoria se disponível
        if (!empty($article['category'])) {
            $schema['articleSection'] = self::getCategoryName($article['category']);
        }

        // Adiciona contagem de comentários se disponível
        if (isset($article['comments_count'])) {
            $schema['commentCount'] = $article['comments_count'];
        }

        // Adiciona interações
        if (isset($article['views_count'])) {
            $schema['interactionStatistic'] = [
                '@type' => 'InteractionCounter',
                'interactionType' => 'https://schema.org/ReadAction',
                'userInteractionCount' => $article['views_count'],
            ];
        }

        return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
    }

    /**
     * Gera Schema.org para a organização/site
     */
    public static function generateOrganizationSchema(): string
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'NewsMediaOrganization',
            'name' => 'Atrocidades',
            'url' => config('app.url'),
            'logo' => asset('images/logo.png'),
            'description' => 'Portal de notícias com cobertura de eventos mundiais e acontecimentos relevantes.',
            'sameAs' => [
                // Adicionar redes sociais quando tiver
            ],
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'contactType' => 'customer service',
                'availableLanguage' => 'Portuguese',
            ],
        ];

        return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
    }

    /**
     * Gera Schema.org BreadcrumbList
     */
    public static function generateBreadcrumbSchema(array $items): string
    {
        $listItems = [];
        foreach ($items as $index => $item) {
            $listItems[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item['name'],
                'item' => $item['url'],
            ];
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $listItems,
        ];

        return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
    }
}
