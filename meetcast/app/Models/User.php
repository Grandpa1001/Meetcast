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
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'cast_goals' => CastGoalsEnum::class,
    ];


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


    public function chains()
    {

    return  $this->belongsToMany(Chains::class,'chains_user');
        
    }


    public function languages()
    {

    return  $this->belongsToMany(Language::class,'language_user');
        
    }


    /**
     * SWIPE 
     * */
    /* user has many swipes */
    public function swipes()
    {
        return $this->hasMany(Swipe::class, 'user_id');
    }

    /* Allows you to check if a user has swiped with another user */
    public function hasSwiped(User $user, $type = null):bool
    {
        $query = $this->swipes() ->where('swiped_user_id', $user->id);

        if ($type !== null) {
            $query->where('type', $type);
        }
        return $query->exists();
    }

    /** Scope to exclude users who have already been swiped by the authenticated user. */
    public function scopeWhereNotSwiped($query)
    {
        // Exclude users whose IDs are in the result of the subquery
        return $query->whereNotIn('id', function ($subquery) {
             // Select the swiped_user_id from the swipes table where user_id is the authenticated user's ID
               $subquery->select('swiped_user_id')
                ->from('swipes')
                ->where('user_id', auth()->id());
        });
    }

    /* MATCH */

    public function matches() {
        return $this->hasManyThrough(
            SwipeMatch::class,
            Swipe::class,
            'user_id',
            'swipe_id_1',
            'id',
            'id'
        )->orWhereHas('swipe2',function($query){
            $query->where('user_id',$this->id);
        });
    }

    /*users can have many convesations */

    public function conversations() {
        return $this->hasMany(Conversation::class,'sender_id')->orWhere('receiver_id',$this->id);
    }


}
