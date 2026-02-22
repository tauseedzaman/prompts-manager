<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::first();
        if (!$user) return;

        $categories = [
            ['name' => 'General', 'color' => '#64748b', 'icon' => 'fas fa-info-circle'],
            ['name' => 'Marketing', 'color' => '#3b82f6', 'icon' => 'fas fa-chart-line'],
            ['name' => 'Development', 'color' => '#10b981', 'icon' => 'fas fa-code'],
            ['name' => 'Social Media', 'color' => '#f59e0b', 'icon' => 'fas fa-share-alt'],
            ['name' => 'Creative Writing', 'color' => '#ec4899', 'icon' => 'fas fa-pen-nib'],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::firstOrCreate(
                ['user_id' => $user->id, 'name' => $category['name']],
                [
                    'slug' => \Illuminate\Support\Str::slug($category['name']),
                    'color' => $category['color'],
                    'icon' => $category['icon'],
                    'is_active' => true,
                    'sort_order' => 0
                ]
            );
        }
    }
}
