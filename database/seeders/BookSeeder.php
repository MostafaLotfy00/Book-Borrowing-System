<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book; // Import the Book model

class BookSeeder extends Seeder
{
    public function run()
    {
        // Create 20 books
        \App\Models\Book::factory(20)->create();
    }
}