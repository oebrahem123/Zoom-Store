<?php

namespace Database\Seeders;

use App\Services\ProductTemplateService;
use Illuminate\Database\Seeder;

class ProductTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $service = new ProductTemplateService();
        $created = $service->seedFromConfig();

        $this->command->info("{$created} product templates seeded from config.");
    }
}
