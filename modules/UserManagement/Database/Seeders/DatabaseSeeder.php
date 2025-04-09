<?php

namespace Modules\UserManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use ReflectionClass;

class DatabaseSeeder extends Seeder
{
  public function run(): void
  {
    $seederPath = __DIR__;
    $seederFiles = File::allFiles($seederPath);

    foreach ($seederFiles as $file) {
      $class = 'Modules\\UserManagement\\Database\\Seeders\\' . $file->getFilenameWithoutExtension();

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
