<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace Modules\RealEstate\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $seederPath = __DIR__;
        $seederFiles = File::allFiles($seederPath);

        foreach ($seederFiles as $file) {
            $class = 'Modules\\RealEstate\\Database\\Seeders\\'.$file->getFilenameWithoutExtension();

            if (
                class_exists($class) &&
                $class !== self::class &&
                is_subclass_of($class, Seeder::class)
            ) {
                $this->call($class);
            }
        }
    }
}
