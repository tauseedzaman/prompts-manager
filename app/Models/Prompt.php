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
        'workspace_id',
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
        'usage_count',
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

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function versions()
    {
        return $this->hasMany(PromptVersion::class)->latest();
    }

    public function ratings()
    {
        return $this->hasMany(PromptRating::class);
    }

    public function forks()
    {
        return $this->hasMany(Prompt::class, 'forked_from_id');
    }

    public function originalPrompt()
    {
        return $this->belongsTo(Prompt::class, 'forked_from_id');
    }

    public function scopePublic($query)
    {
        return $query->where('visibility', 'public')->where('status', 'published');
    }

    public function scopeMostUsed($query)
    {
        return $query->orderBy('usage_count', 'desc');
    }

    public function getAverageRatingAttribute()
    {
        return round($this->ratings()->avg('rating'), 1) ?: 0;
    }
}
