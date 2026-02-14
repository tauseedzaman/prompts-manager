<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'sort_order',
        'is_active',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function prompts()
    {
        return $this->hasMany(Prompt::class);
    }
}
