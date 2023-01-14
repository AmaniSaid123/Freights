<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Translatable extends Model
{
    protected $table = 'translatable';

    protected $fillable = [
        'title',
        'perex',
        'published_at',
    
    ];
    
    
    protected $dates = [
        'published_at',
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/translatables/'.$this->getKey());
    }
}
