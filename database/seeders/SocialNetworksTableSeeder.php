<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SocialNetworksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('social_networks')->insert([
            [
                'name' => 'vk',
                'base_url' => 'https://vk.com',
            ],
            [
                'name' => 'telegram',
                'base_url' => 'https://telegram.org',
            ],
            [
                'name' => 'facebook',
                'base_url' => 'https://facebook.com',
            ],
            [
                'name' => 'twitter',
                'base_url' => 'https://twitter.com',
            ],
            [
                'name' => 'instagram',
                'base_url' => 'https://instagram.com',
            ],
        ]);
    }
}
