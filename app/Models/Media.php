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
    ];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    /**
     * Get the URL to access the file.
     */
    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }
}
