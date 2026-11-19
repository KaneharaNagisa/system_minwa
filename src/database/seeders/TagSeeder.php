<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['まんが日本昔ばなし', '民話の部屋'] as $name) {
            Tag::firstOrCreate(['name' => $name]);
        }
    }
}
