<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Tag extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'color',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function prompts()
    {
        return $this->belongsToMany(Prompt::class);
    }
}
