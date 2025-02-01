<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Media extends Model
{
    protected $fillable = [
        'uuid',
        'name',
        'path',
        'visibility',
        'converted_for_streaming_at',
    ];




    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }
}
