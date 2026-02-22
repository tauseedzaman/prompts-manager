<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PromptSeeder extends Seeder
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

        $generalCat = \App\Models\Category::where('name', 'General')->first();
        $marketingCat = \App\Models\Category::where('name', 'Marketing')->first();
        $devCat = \App\Models\Category::where('name', 'Development')->first();

        $chatgptTag = \App\Models\Tag::where('name', 'chatgpt')->first();
        $codingTag = \App\Models\Tag::where('name', 'coding')->first();

        $prompts = [
            [
                'title' => 'Blog Post Outline Generator',
                'description' => 'Generates a structured outline for any blog post topic.',
                'prompt_text' => 'Act as a professional content strategist. Create a detailed blog post outline for the following topic: {{topic}}. Include an introduction, 5 main sections with sub-points, and a conclusion.',
                'category_id' => $marketingCat->id ?? $generalCat->id,
            ],
            [
                'title' => 'Code Refactoring Assistant',
                'description' => 'Helpful for improving code readability and performance.',
                'prompt_text' => 'Act as a senior software engineer. Refactor the following code to make it more efficient and readable, following clean code principles: \n\n{{code}}',
                'category_id' => $devCat->id ?? $generalCat->id,
            ],
            [
                'title' => 'Social Media Hooks',
                'description' => 'Viral hook ideas for X and LinkedIn.',
                'prompt_text' => 'I am writing a post about {{topic}}. Give me 5 viral hook ideas that will grab attention and encourage engagement on LinkedIn.',
                'category_id' => $marketingCat->id ?? $generalCat->id,
            ],
        ];

        foreach ($prompts as $p) {
            $prompt = \App\Models\Prompt::create(array_merge($p, [
                'user_id' => $user->id,
                'slug' => \Illuminate\Support\Str::slug($p['title']),
                'visibility' => 'private',
                'status' => 'published',
            ]));

            if ($chatgptTag) {
                $prompt->tags()->attach($chatgptTag->id);
            }
            if ($p['title'] === 'Code Refactoring Assistant' && $codingTag) {
                $prompt->tags()->attach($codingTag->id);
            }
        }
    }
}
