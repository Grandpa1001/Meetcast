<?php

namespace App\Livewire\Chat;

use App\Livewire\Components\Tabs;
use Livewire\Attributes\On;
use App\Models\Conversation;
use App\Models\Message;
use Livewire\Component;

class Chat extends Component
{
    public $chat;
    public $conversation;
    public $receiver;


    public $body;

    public $loadedMessages;
    public $paginate_var=15;


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

      #dispatch event to scroll chat to bottom
      $this->dispatch('scroll-bottom');

      #push the message
      $this->loadedMessages->push($createdMessage);

      #update the conversation model - for sorting in chatlist
      $this->conversation->updated_at=now();
      $this->conversation->save();

      #dispatch event
      $this->dispatch('new-message-created');

      
  }

  #[On('loadMore')]
  function loadMore() {
      #increment
      $this->paginate_var +=10;

      #call the loadMessages()
      $this->loadMessages();

      #dispatch event
      $this->dispatch('update-height');

  }

  /* Method to load messages  */
  function loadMessages()  {

    #get count
    $count= Message::where('conversation_id',$this->conversation->id)->count();

    #skip and query

    $this->loadedMessages= Message::where('conversation_id',$this->conversation->id)
                           ->skip($count- $this->paginate_var)
                           ->take($this->paginate_var)
                           ->get();

     return $this->loadedMessages;


}

  public function mount() {
    #make sure user is authenticated
    abort_unless(auth()->check(),401);

    #get conversation
    $this->conversation= Conversation::findOrFail($this->chat);

    #check if user belongs to conversation
    $belongsToConversation= auth()->user()->conversations()
            ->where('id', $this->conversation->id)
            ->exists();
    abort_unless($belongsToConversation,403);

    #mark messages as read
    Message::where('conversation_id', $this->conversation->id)
                    ->where('receiver_id',auth()->id())
                    ->whereNull('read_at')
                    ->update(['read_at'=>now()]);

    #set receiver
    $this->receiver= $this->conversation->getReceiver();

    $this->loadMessages();
  }
    
    public function render()
    {
        return view('livewire.chat.chat');
    }
}
