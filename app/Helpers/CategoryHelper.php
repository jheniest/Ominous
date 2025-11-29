<?php

namespace App\Helpers;

class CategoryHelper
{
    /**
     * Mapeamento de categorias para nomes de exibição
     */
    private static $categoryMap = [
        'guerra' => 'Guerra',
        'terrorismo' => 'Terrorismo',
        'chacina' => 'Chacina',
        'massacre' => 'Massacre',
        'suicidio' => 'Suicídio',
        'tribunal-do-crime' => 'Tribunal do Crime',
        'acidente' => 'Acidente',
        'crime' => 'Crime',
        'violencia' => 'Violência',
        'outros' => 'Outros',
    ];

    /**
     * Cores das categorias
     */
    private static $categoryColors = [
        'guerra' => 'bg-red-600',
        'terrorismo' => 'bg-orange-600',
        'chacina' => 'bg-amber-600',
        'massacre' => 'bg-yellow-600',
        'suicidio' => 'bg-lime-600',
        'tribunal-do-crime' => 'bg-cyan-600',
        'acidente' => 'bg-violet-600',
        'crime' => 'bg-pink-600',
        'violencia' => 'bg-rose-600',
        'outros' => 'bg-gray-600',
    ];

    /**
     * Obter nome formatado da categoria (slug -> Nome Bonito)
     */
    public static function format(?string $category): string
    {
        if (empty($category)) {
            return 'Sem Categoria';
        }

        $key = strtolower($category);
        
        if (isset(self::$categoryMap[$key])) {
            return self::$categoryMap[$key];
        }

        // Fallback: converter slug para formato legível
        // "tribunal-do-crime" -> "Tribunal Do Crime"
        return ucwords(str_replace('-', ' ', $category));
    }

    /**
     * Alias para compatibilidade
     */
    public static function getPortugueseName(string $category): string
    {
        return self::format($category);
    }

    /**
     * Obter classe CSS de cor da categoria
     */
    public static function colorClass(?string $category): string
    {
        if (empty($category)) {
            return 'bg-gray-600';
        }

        $key = strtolower($category);
        return self::$categoryColors[$key] ?? 'bg-gray-600';
    }

    /**
     * Obter todas as categorias em formato [key => nome]
     */
    public static function getAllCategories(): array
    {
        return self::$categoryMap;
    }
}
