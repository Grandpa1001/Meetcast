<?php

namespace App\Livewire\Chat;

use Livewire\Component;
use App\Models\Conversation;

class Chat extends Component
{

    public $chat;
    public $conversation;
    public $receiver;


    function mount() {
        #check auth
        abort_unless(auth()->check(),401);

        #get conversation
        $this->conversation=Conversation::findOrFail($this->chat);

        #belongs to conversation
        $belongsToConversation = auth()->user()->conversations()
                                                ->where('id',$this->conversation->id)
                                                ->exists();
        abort_unless($belongsToConversation,403);
        
        #set receiver
        $this->receiver= $this->conversation->getReceiver();
    }

    public function render()
    {
        return view('livewire.chat.chat');
    }
}
