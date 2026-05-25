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
                'category_name' => 'Sayuran',
                'description' => 'Bahan pangan nabati yang berasal dari berbagai bagian tumbuhan (seperti daun, batang, bunga, umbi, atau biji).',
            ],
            [
                'category_name' => 'Lauk Pauk',
                'description' => 'Berbagai jenis hidangan pendamping makanan pokok (seperti nasi) yang berfungsi sebagai penambah cita rasa, variasi, serta pemenuhan gizi seimbang.',
            ],
            [
                'category_name' => 'Makanan Pokok',
                'description' => 'Makanan yang dikonsumsi secara rutin sebagai menu utama dan menjadi sumber karbohidrat untuk memenuhi sebagian besar kebutuhan energi harian tubuh.',
            ],
            [
                'category_name' => 'Bumbu Dapur',
                'description' => 'Berbagai jenis bahan tanaman, rempah, atau hasil olahan yang digunakan untuk menyedapkan dan membangkitkan selera makan.',
            ]
        ]);
    }
}