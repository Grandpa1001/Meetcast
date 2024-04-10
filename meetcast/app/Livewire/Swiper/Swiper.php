<?php

namespace App\Livewire\Swiper;

use App\Models\Swipe;
use App\Models\SwipeMatch;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;

class Swiper extends Component
{


    #[On('swipedright')]
    public function swipedRight(User $user)
    {
        //make user user is authenticated
        abort_unless(auth()->check(),401);

         #create Swipe Right
         $this->createSwipe($user,'right');

    }

    #[On('swipedleft')]
    public function swipedLeft(User $user)
    {
        //make user user is authenticated
        abort_unless(auth()->check(),401);

         #create Swipe Right
         $this->createSwipe($user,'left');
    }

    #[On('swipedup')]
    public function swipedUp(User $user)
    {
        //make user user is authenticated
        abort_unless(auth()->check(),401);
        
         #create Swipe Right
         $this->createSwipe($user,'up');
    }


    protected function createSwipe($user,$type){

        #return null if auth user has already swiped with  $user
        if (auth()->user()->hasSwiped($user)) {
            return null;
        }

        #create Swipe
        $swipe =  Swipe::create([
            'user_id'=>auth()->id(),
            'swiped_user_id'=>$user->id,
            'type'=>$type,
        ]);

        #before creating match we want to make sure auth user swiped Right or  Up
        if ($type=='up'||$type=='right') {
            # code...
        
        #creating Match
        $authUserId = auth()->id();
        $swipedUserId = $user->id;

        #Now Also check if swiped user  has swipe match with authenticated user.
        $matchingSwipe =  Swipe::where('user_id', $swipedUserId)
                            ->where('swiped_user_id', $authUserId)
                            ->whereIn('type',['up','right'])
                            ->first();

    
        #If true, create a SwipeMatch
        if ($matchingSwipe) {
            SwipeMatch::create([
                'swipe_id_1' => $swipe->id,
                'swipe_id_2' => $matchingSwipe->id,
            ]);


        //Show match found alert
        $this->dispatch('match-found');
        }

    }

    }
    

    public function render()
    {

       // dd(auth()->user()->matches()->get());

       // dd(SwipeMatch::first()->swipe2);
        $users=User::limit(10)->whereNotSwiped()->where('id','<>',auth()->id())->get();
        return view('livewire.swiper.swiper',['users'=>$users]);
    }
}
