<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Prompt extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'prompt_text',
        'description',
        'language',
        'tone',
        'usage_type',
        'is_template',
        'variables_schema',
        'example_input',
        'example_output',
        'source',
        'visibility',
        'is_favorite',
        'status',
    ];

    protected $casts = [
        'is_template' => 'boolean',
        'is_favorite' => 'boolean',
        'variables_schema' => 'array',
        'example_input' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }


}
