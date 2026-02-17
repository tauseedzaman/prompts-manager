<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PromptVersion extends Model
{
    use HasUuids;

    protected $fillable = [
        'prompt_id',
        'user_id',
        'title',
        'prompt_text',
        'description',
        'change_summary',
    ];

    protected $casts = [
        'change_summary' => 'array',
    ];

    public function prompt()
    {
        return $this->belongsTo(Prompt::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
