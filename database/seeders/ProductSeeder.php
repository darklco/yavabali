<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua kategori
        $allCategories = Category::all();
        
        if ($allCategories->count() == 0) {
            $this->command->error('Tidak ada kategori ditemukan! Harap jalankan CategorySeeder terlebih dahulu.');
            return;
        }

        // Buat mapping kategori
        $categories = [];
        foreach ($allCategories as $cat) {
            $title = $cat->title;
            
            // Periksa apakah title adalah string (JSON) dan decode jika perlu
            if (is_string($title)) {
                $titleArray = json_decode($title, true);
                // Pastikan kunci 'en' ada
                if (isset($titleArray['en'])) {
                    $categories[$titleArray['en']] = $cat;
                }
            } 
            // Jika title sudah berbentuk array
            elseif (is_array($title) && isset($title['en'])) {
                $categories[$title['en']] = $cat;
            }
        }

        $products = [
            [
                'title' => [
                    'en' => 'Honey Granola',
                    'id' => 'Granola Madu'
                ],
                'description' => [
                    'en' => 'Crunchy granola with natural honey',
                    'id' => 'Granola renyah dengan madu alami'
                ],
                'category_en' => 'Granola'
            ],
            [
                'title' => [
                    'en' => 'Chocolate Bar',
                    'id' => 'Bar Coklat'
                ],
                'description' => [
                    'en' => 'Delicious chocolate protein bar',
                    'id' => 'Bar protein rasa coklat lezat'
                ],
                'category_en' => 'Bar'
            ],
            [
                'title' => [
                    'en' => 'Salted Cashews',
                    'id' => 'Kacang Mede Asin'
                ],
                'description' => [
                    'en' => 'Roasted cashews lightly salted',
                    'id' => 'Kacang mede panggang dengan sedikit garam'
                ],
                'category_en' => 'Cashews'
            ],
        ];

        $productsAdded = 0;

        foreach ($products as $product) {
            // Pastikan category_en tidak null
            if (!isset($product['category_en'])) {
                continue;
            }

            $category = $categories[$product['category_en']] ?? null;

            if ($category) {
                // Buat array data dasar
                $insertData = [
                    'id' => (string) Str::uuid(),
                    'title' => json_encode($product['title']),
                    'description' => json_encode($product['description']),
                    'image' => null,
                    'category_id' => $category->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];

                try {
                    DB::table('products')->insert($insertData);
                    $productsAdded++;
                } catch (\Exception $e) {
                    // Tambahkan log atau pesan error jika diperlukan
                }
            }
        }
    }
}