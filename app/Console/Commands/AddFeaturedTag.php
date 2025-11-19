<?php

namespace App\Console\Commands;

use App\Models\Tag;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class AddFeaturedTag extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tag:feature 
                            {name : The name of the tag}
                            {--color= : Hex color for the tag (e.g., #dc2626)}
                            {--remove : Remove tag from featured menu}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add or remove a tag from the featured menu';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $remove = $this->option('remove');

        if ($remove) {
            return $this->removeFeaturedTag($name);
        }

        $color = $this->option('color') ?? $this->ask('Enter hex color (e.g., #dc2626)', '#dc2626');
        
        // Get next display order
        $nextOrder = Tag::featured()->max('display_order') + 1;

        $tag = Tag::updateOrCreate(
            ['name' => $name],
            [
                'slug' => \Illuminate\Support\Str::slug($name),
                'color' => $color,
                'is_featured' => true,
                'display_order' => $nextOrder,
            ]
        );

        Cache::forget('featured_tags_with_counts');

        $this->info("✓ Tag '{$name}' added to featured menu!");
        $this->line("  Color: {$color}");
        $this->line("  Order: {$nextOrder}");

        return Command::SUCCESS;
    }

    private function removeFeaturedTag(string $name)
    {
        $tag = Tag::where('name', $name)->first();

        if (!$tag) {
            $this->error("Tag '{$name}' not found!");
            return Command::FAILURE;
        }

        $tag->update(['is_featured' => false]);
        Cache::forget('featured_tags_with_counts');

        $this->info("✓ Tag '{$name}' removed from featured menu!");
        return Command::SUCCESS;
    }
}

