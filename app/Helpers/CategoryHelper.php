<?php

namespace App\Helpers;

class CategoryHelper
{
    /**
     * Mapeamento de categorias (agora apenas para capitalização)
     */
    private static $categoryMap = [
        'guerra' => 'Guerra',
        'terrorismo' => 'Terrorismo',
        'chacina' => 'Chacina',
        'massacre' => 'Massacre',
        'suicidio' => 'Suicídio',
        'tribunal-do-crime' => 'Tribunal do Crime',
    ];

    /**
     * Obter nome formatado da categoria
     */
    public static function getPortugueseName(string $category): string
    {
        return self::$categoryMap[$category] ?? ucfirst($category);
    }

    /**
     * Obter todas as categorias em formato [key => nome]
     */
    public static function getAllCategories(): array
    {
        return self::$categoryMap;
    }
}
