<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Collection extends Model
{
    use HasUuids;
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
