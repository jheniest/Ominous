<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FeaturedTagsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $featuredTags = [
            ['name' => 'Guerra', 'color' => '#dc2626', 'display_order' => 1],
            ['name' => 'Terrorismo', 'color' => '#ea580c', 'display_order' => 2],
            ['name' => 'Chacina', 'color' => '#d97706', 'display_order' => 3],
            ['name' => 'Massacre', 'color' => '#ca8a04', 'display_order' => 4],
            ['name' => 'Suícidio', 'color' => '#65a30d', 'display_order' => 5],
            ['name' => 'Tribunal do Crime', 'color' => '#0891b2', 'display_order' => 6],
        ];

        foreach ($featuredTags as $tagData) {
            Tag::updateOrCreate(
                ['name' => $tagData['name']],
                [
                    'slug' => \Illuminate\Support\Str::slug($tagData['name']),
                    'color' => $tagData['color'],
                    'is_featured' => true,
                    'display_order' => $tagData['display_order'],
                ]
            );
        }
    }
}

