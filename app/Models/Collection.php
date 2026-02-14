<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function prompts()
    {
        return $this->belongsToMany(Prompt::class)
                    ->withPivot('sort_order')
                    ->orderByPivot('sort_order');
    }
}
