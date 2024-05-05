<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use App\Models\Message;
use Livewire\Component;

class Chat extends Component
{
    public $chat;
    public $conversation;
    public $receiver;


    public $body;


    function sendMessage()  {
      abort_unless(auth()->check(),401);

      $this->validate(['body'=>'required|string']);

      $createdMessage= Message::create([
          'conversation_id'=>$this->conversation->id,
          'sender_id'=>auth()->id(),
          'receiver_id'=>$this->receiver->id,
          'body'=>$this->body
      ]);

      $this->reset('body');


      #update the conversation model - for sorting in chatlist
      $this->conversation->updated_at=now();
      $this->conversation->save();


      
  }

  public function mount()
  {
    #make sure user is authenticated
    abort_unless(auth()->check(),401);

    #get conversation
    $this->conversation= Conversation::findOrFail($this->chat);

    #check if user belongs to conversation
    $belongsToConversation= auth()->user()->conversations()
            ->where('id', $this->conversation->id)
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
