<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Swipe extends Model
{
    use HasFactory;

    /* user who made the swipe */
    function user() : BelongsTo {
        return $this->belongsTo(User::class);
    }

    /* user who was swiped */
    function swipedUser() : BelongsTo {
        return $this->belongsTo(User::class, 'swiped_user_id');
    }

    /* check super like */

    function isSuperLike() : bool {
        return $this->type=='up';
    }

}
