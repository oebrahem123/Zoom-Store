<?php

namespace Database\Seeders;

use App\Models\product;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\categories;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
         $categories=[
            ['id'=>1,'name'=>'ساعات','description'=>'ساعات تجمع بين الأناقة والدقة','imagepath'=>'assets/img/bm1.jpg'],
            ['id'=>2,'name'=>'إلكترونيات','description'=>'تكنولوجيا تلبي كل احتياجاتك','imagepath'=>'assets/img/modern-stationary-collection-arrangement.jpg'],
            ['id'=>3,'name'=>'كاميرات','description'=>'كاميرات إلكترونية','imagepath'=>'assets/img/paul-gaudriault-cwy9yVBBPxg-unsplash.jpg'],
            ['id'=>4,'name'=>'مكياج','description'=>'لمسة سحرية لجمالك','imagepath'=>'assets/img/s1.png'],
            ['id'=>5,'name'=>'مأكولات','description'=>'طعامك المفضل في مكان واحد','imagepath'=>'assets/img/top-view-table-full-food.jpg'],
            ['id'=>6,'name'=>'شنط','description'=>'حقيبة تكمّل أناقتك','imagepath'=>'assets\img\vecteezy_composition-of-shopping-day-concept-with-shopping-bags_31616964.jpg'],
         ];
            DB::table('categories')->insertOrIgnore($categories);
for($i = 1; $i<= 25 ;$i++){
    Product::create([
        'name'=>'product' . $i,
        'description'=> 'this is product number' .$i,
        'price'=>rand(10,100),
        'quantity'=>rand(1,50),
        'imagepath'=>'assets/img/pic09.jpg',
        'category_id'=>rand(1,6),
    ]);
};


    }
}
