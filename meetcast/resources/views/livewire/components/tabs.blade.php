<div>
    <section class=" mb-4 overflow-y-auto h-full overflow-x-scroll relative"
     x-data="{ tab: {{request()->routeIs('chat.index')||request()->routeIs('chat')?'2':'1'}} }"
     @match-found.window="$wire.$refresh()"
     x-init="
     
     Echo.private('users.{{auth()->id()}}')
     .notification((notification)=>{
         if(notification['type']== 'App\\Notifications\\MessageSentNotification')
         {
 
            $wire.$refresh()
 
         }
     });
     "
     >
     {{-- @dd(auth()->user()->unreadMessagesCount()) --}}
        <header class=" flex  items-center gap-5 mb-2 p-4 sticky top-0 bg-white  z-10">
            <button @click="tab = '1'" :class="{ 'border-b-2 border-red-500': tab == '1'}"
                class=" font-bold text-sm px-2 pb-1.5">
                
                 Matches

                @if (auth()->user()->matches()->count()>0)
                    <span class="rounded-full text-xs p-1 px-2 font-bold text-white bg-tinder ">
                        {{auth()->user()->matches()->count()}}
                    </span>
                @endif
            
            </button>
            <button @click="tab = '2'" :class="{ 'border-b-2 border-red-500': tab == '2'}"
                class=" font-bold text-sm px-2 pb-1.5">
                Messages
                @if (auth()->user()->unReadMessagesCount()>0)
                <span class="rounded-full text-xs p-1 px-2 font-bold text-white bg-tinder ">
                    {{auth()->user()->unReadMessagesCount()}}
                </span>
                @endif
             
            </button>
        </header>

        <main class="h-full">
            <aside class="px-2" x-show="tab == '1'" x-cloak>
                {{-- Add new componenta matches --}}
                <div class="grid grid-cols-3 gap-2">
      
                    @foreach ($matches as $i=> $match)
                        
                    {{-- Add class: cursor-pointer  --}}
                        <div  wire:click="createConversation('{{$match->id}}')" class="relative cursor-pointer">
                            {{-- Dots BI --}}
                            <span class="-top-6 -right-5 absolute">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-dot text-red-500 w-12 h-12" viewBox="0 0 16 16">
                                    <path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3" />
                                </svg>
                            </span>
                            <img class="h-36 rounded-lg object-cover"
                                src="https://source.unsplash.com/200x200?face-woman-{{$i}}" alt="match">
                            <h5 class="absolute bottom-2 left-2 text-white font-bold text-[10px]  bg-black/60 p-2 rounded-lg">
                                {{$match->swipe1->user_id==auth()->id()?$match->swipe2->user->name:$match->swipe1->user->name}}
                            </h5>
                       </div>
                      @endforeach
                    
            </div>
            </aside>
            <aside x-show="tab == '2'" x-cloak>

                <ul class=" ">

                    @foreach ( $conversations as $i=> $conversation)

                      @php
                          $lastMessage = $conversation->messages()?->latest()->first();
                      @endphp
                        
                    
                       <li>
                        <a  @class(['flex gap-4 items-center p-2','border-r-4 border-red-500 bg-white py-3'=> $selectedConversationId == $conversation->id])
                            wire:navigate
                            href="{{route('chat',$conversation->id)}}">
                            <div class="relative">
                                {{-- Dots BI --}}
                                <span class="inset-y-0 my-auto -right-7 absolute">
                                    <svg @class([ 'bi bi-dot w-14 h-14 stroke-[0.3px] stroke-white '
                                        , 'hidden'=>$i==3?false:true,
                                        'text-green-500'=>fake()->randomElement([true,false]), {{-- Active last
                                        24 hours--}}
                                        'text-red-500'=>fake()->randomElement([true,false]), {{-- New match/
                                        unread messages--}}
                                        ])
                                        xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor"
                                        viewBox="0 0 16 16">
                                        <path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3" />
                                    </svg>
                                </span>

                                <x-avatar class="w-14 h-14"
                                    src="https://source.unsplash.com/200x200?face-girl-{{$i}}" />

                            </div>
                            <div class="overflow-hidden">
                                <h6 class="font-bold truncate"> {{$conversation->getReceiver()->name}}</h6>
                                <p 
                                @class([
                                    'truncate flex gap-2 items-center',
                                    'font-semibold text-black' => !$lastMessage?->isRead() && $lastMessage?->sender_id != auth()->id(),
                                    'font-normal text-gray-600' => $lastMessage?->isRead() && $lastMessage?->sender_id != auth()->id(),
                                    'font-normal text-gray-600' => $lastMessage?->isRead() && $lastMessage?->sender_id == auth()->id(),
                                ])> 

                                {{-- show if message belongs to authenticated user --}}
                                @if ($lastMessage?->sender_id == auth()->id())
                                {{-- arrow-uturn-left :heroicons --}}
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 ">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                                    </svg>
                                </span>
                                @endif
                                
                                {{$conversation->messages()?->latest()->first()?->body}} </p>
                            </div>
                        </a>

                        </li>
                        @endforeach

                </ul>

            </aside>
        </main>
    </section>
</div>
