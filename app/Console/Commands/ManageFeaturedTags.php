<?php

namespace App\Console\Commands;

use App\Models\Tag;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ManageFeaturedTags extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tag:list {--reorder : Reorder featured tags}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List and manage featured tags in the menu';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('reorder')) {
            return $this->reorderTags();
        }

        $tags = Tag::featured()->get();

        if ($tags->isEmpty()) {
            $this->warn('No featured tags found.');
            return Command::SUCCESS;
        }

        $this->info('Featured Tags:');
        $this->newLine();

        $headers = ['Order', 'Name', 'Color', 'Slug'];
        $rows = $tags->map(function ($tag) {
            return [
                $tag->display_order,
                $tag->name,
                $tag->color,
                $tag->slug,
            ];
        });

        $this->table($headers, $rows);

        return Command::SUCCESS;
    }

    private function reorderTags()
    {
        $tags = Tag::featured()->get();

        if ($tags->isEmpty()) {
            $this->warn('No featured tags to reorder.');
            return Command::SUCCESS;
        }

        $this->info('Current order:');
        foreach ($tags as $index => $tag) {
            $this->line(($index + 1) . ". {$tag->name}");
        }

        $this->newLine();
        $this->info('Enter new order (comma-separated names):');
        $order = $this->ask('Example: Guerra,Terrorismo,Chacina');

        $names = array_map('trim', explode(',', $order));
        
        foreach ($names as $index => $name) {
            $tag = $tags->firstWhere('name', $name);
            if ($tag) {
                $tag->update(['display_order' => $index + 1]);
            }
        }

        Cache::forget('featured_tags_with_counts');

        $this->info('✓ Tags reordered successfully!');
        return Command::SUCCESS;
    }
}

