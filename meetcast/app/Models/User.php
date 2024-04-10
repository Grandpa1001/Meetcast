<?php

namespace App\Models;

use App\Enums\CastGoalsEnum;
use App\Enums\BasicGroupEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // protected $fillable = [
    //     'name',
    //     'email',
    //     'password',
    // ];

    protected $guarded=[];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'cast_goals' => CastGoalsEnum::class,
        ];
    }

protected static function boot(){
    parent::boot();  //przydatna

    static::created(function($user){

        $basics=Basic::all();

        //if like nft
        $basic=$basics->where('group', BasicGroupEnum::nft)->first();
        $user->basics()->attach($basic);

        //community
        $basic=$basics->where('group', BasicGroupEnum::community)->first();
        $user->basics()->attach($basic);


    });
}


function basics()  {
    return $this->belongsToMany(Basic::class,'basic_user');
    
}


}
