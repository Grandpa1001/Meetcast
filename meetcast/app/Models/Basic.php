<?php

namespace App\Models;

use App\Enums\BasicGroupEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Basic extends Model
{
    use HasFactory;

    protected $guarded=[];

    protected $casts=[
        'group'=>BasicGroupEnum::class
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'basic_user');
    }

}
