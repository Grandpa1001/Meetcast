<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;
    
    protected $fillable=[
        'receiver_id',
        'sender_id',
        'match_id'
    ];


    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /* a conversation belongs to a single match. */

    public function match()
    {
        return $this->belongsTo(SwipeMatch::class,);
    }

    public function getReceiver()
    {
        if ($this->sender_id === auth()->id()) {

            return User::firstWhere('id',$this->receiver_id);

        } else {

            return User::firstWhere('id',$this->sender_id);
        }
    }

}
