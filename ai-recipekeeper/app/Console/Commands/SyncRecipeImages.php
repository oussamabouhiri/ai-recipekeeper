<?php

namespace App\Console\Commands;

use App\Models\Recette;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SyncRecipeImages extends Command
{
    protected $signature = 'recipe:sync-images {--clear : Reset image_path to NULL for all recipes}';

    protected $description = 'Copy recipe images to public/images/recipes and update image_path';

    public function handle(): int
    {
        if ($this->option('clear')) {
            Recette::query()->update(['image_path' => null]);

            $this->info('Reset image_path to NULL for all recipes.');

            return self::SUCCESS;
        }

        $sourceDir = base_path('Images/imagesRecipe');
        $targetDir = public_path('images/recipes');

        if (! is_dir($sourceDir)) {
            $this->error("Source directory not found: {$sourceDir}");

            return self::FAILURE;
        }

        File::ensureDirectoryExists($targetDir);

        // Remove known orphan test images that don't correspond to any recipe
        $orphanNames = ['dfasfds', 'hello-recipe-test', 'hello-world', 'traditional-moroccan-goat-tajine-sossi'];
        foreach ($orphanNames as $name) {
            foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
                $orphanPath = $targetDir.'/'.$name.'.'.$ext;
                if (File::exists($orphanPath)) {
                    File::delete($orphanPath);
                    $this->info("Removed orphan image: {$name}.{$ext}");
                }
            }
        }

        $sources = collect(File::files($sourceDir))
            ->keyBy(fn (\SplFileInfo $file) => Str::slug(pathinfo($file->getFilename(), PATHINFO_FILENAME)));

        $copied = 0;
        $updated = 0;

        foreach (Recette::query()->cursor() as $recipe) {
            $source = $sources->get(Str::slug($recipe->title));

            if ($source === null) {
                continue;
            }

            $extension = strtolower(pathinfo($source->getFilename(), PATHINFO_EXTENSION));
            $relative = 'images/recipes/'.Str::slug($recipe->title).".{$extension}";
            $target = public_path($relative);

            if (! File::exists($target)) {
                File::copy($source->getPathname(), $target);
                $copied++;
            }

            if ($recipe->image_path !== $relative) {
                $recipe->update(['image_path' => $relative]);
                $updated++;
            }
        }

        $this->info("Copied {$copied} image(s), updated image_path for {$updated} recipe(s).");

        return self::SUCCESS;
    }
}
