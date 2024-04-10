<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Swipe extends Model
{
    use HasFactory;

    protected $guarded=[];

    /* representing the user who made the swipe. */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /* representing the user who was swiped. */
    public function swipedUser()
    {
        return $this->belongsTo(User::class, 'swiped_user_id');
    }
   
    /* Check if swipe is super like  */
    function  isSuperLike() : bool {

     return $this->type =='up';
        
    }

    public function match() {
        return $this->hasOne(Swipe::class, 'swipe_id_1')->orWhere('swipe_id_2',$this->getKey());
    }


}
