<?php

namespace App\Livewire\Chat;

use App\Livewire\Components\Tabs;
use App\Models\Conversation;
use App\Models\Message;
use App\Notifications\MessageSentNotification;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Support\Facades\Broadcast;
use App\Livewire\Notifications\TestEvent;
use Illuminate\Support\Facades\Log;

class Chat extends Component
{
    public $chat;
    public $conversation;
    public $receiver;

    public $loadedMessages;
    public $paginate_var= 10;


    public $body;


    public function listenBroadcastedMessage($event)
    {
        $this->dispatch('scroll-bottom');

        $newMessage = Message::find($event['message_id']);


        #push message
        $this->loadedMessages->push($newMessage);

        #mark as read
        $newMessage->read_at = now();
        $newMessage->save();

        #brodcast new message created -By other user ofcourse
        $this->dispatch('new-message-created');


    }


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

      #scroll to bottom
      $this->dispatch('scroll-bottom');


      #push the message
      $this->loadedMessages->push($createdMessage);

      #update the conversation model - for sorting in chatlist
      $this->conversation->updated_at=now();
      $this->conversation->save();

      #dispatch event 'new-message-created' after updating conversation 
      $this->dispatch('new-message-created');

      #broadcast new message 
      $this->receiver->notify(new MessageSentNotification(
        auth()->user(),
        $createdMessage,
        $this->conversation
    ));

    
      
  }




  #[On('loadMore')]
  function loadMore()
  {

      //dd('reached');

      #increment
      $this->paginate_var += 10;

      #call loadMessage
      $this->loadMessages();

      #dispatch event- update height
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

     #mark messages belonging to receiver as read
     Message::where('conversation_id',$this->conversation->id)
            ->where('receiver_id',auth()->id())
            ->whereNull('read_at')
            ->update(['read_at'=>now()]);

    #set receiver
    $this->receiver= $this->conversation->getReceiver();

    #Call load messages
    $this->loadMessages();


  }
    
    public function render()
    {
        return view('livewire.chat.chat');
    }
}
