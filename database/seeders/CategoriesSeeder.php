<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'title' => [
                    'en' => 'All Products',
                    'id' => 'Semua Produk'
                ],
            ],
            [
                'title' => [
                    'en' => 'Granola',
                    'id' => 'Granola'
                ],
            ],
            [
                'title' => [
                    'en' => 'Biscuits',
                    'id' => 'Biskuit'
                ],
            ],
            [
                'title' => [
                    'en' => 'Bar',
                    'id' => 'Bar'
                ],
            ],
            [
                'title' => [
                    'en' => 'Popcorn',
                    'id' => 'Popcorn'
                ],
            ],
            [
                'title' => [
                    'en' => 'Puffs',
                    'id' => 'Puffs'
                ],
            ],
            [
                'title' => [
                    'en' => 'Cashews',
                    'id' => 'Kacang Mede'
                ],
            ],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->insert([
                'id' => (string) Str::uuid(), // Pastikan UUID dikonversi ke string
                'title' => json_encode($category['title'], JSON_UNESCAPED_UNICODE), // Tambahkan JSON_UNESCAPED_UNICODE
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}