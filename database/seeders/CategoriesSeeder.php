<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            [
                'category_name' => 'Supercar',
                'description' => 'Mobil dengan performa tinggi, desain aerodinamis, dan teknologi canggih yang menawarkan pengalaman berkendara yang luar biasa.',
            ],
            [
                'category_name' => 'Hypercar',
                'description' => 'Mobil dengan performa sangat tinggi, desain yang inovatif, dan teknologi canggih yang memungkinkan kecepatan dan akselerasi luar biasa.',
            ],
            [
                'category_name' => 'SUV',
                'description' => 'Mobil dengan kapasitas muatan yang besar, kemampuan off-road yang baik, dan kenyamanan yang superior.',
            ],
            [
                'category_name' => 'Sedan',
                'description' => 'Mobil dengan desain yang elegan, kenyamanan yang baik, dan performa yang seimbang untuk penggunaan sehari-hari.',
            ]
        ]);
    }
}