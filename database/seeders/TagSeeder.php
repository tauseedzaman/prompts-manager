<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::first();
        if (! $user) {
            return;
        }

        $tags = [
            ['name' => 'chatgpt', 'color' => '#10b981'],
            ['name' => 'openai', 'color' => '#3b82f6'],
            ['name' => 'marketing', 'color' => '#ec4899'],
            ['name' => 'coding', 'color' => '#06b6d4'],
            ['name' => 'research', 'color' => '#475569'],
        ];

        foreach ($tags as $tag) {
            \App\Models\Tag::firstOrCreate(
                ['user_id' => $user->id, 'name' => $tag['name']],
                [
                    'slug' => \Illuminate\Support\Str::slug($tag['name']),
                    'color' => $tag['color'],
                ]
            );
        }
    }
}
