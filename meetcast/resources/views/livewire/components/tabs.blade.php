<div>
    {{-- A good traveler has no fixed plans and is not intent upon arriving. --}}
    <section
    x-data="{tab:'1'}"
    @match-found.window="$wire.$refresh()"
    class="mb-auto overflow-y-auto overflow-x-scroll relative">

       <header class="flex items-center gap-5 mb-2 p-4 sticky top-0 bg-white z-10">

           <button @click="tab='1'" :class="{ 'border-b-2 border-violet-500': tab=='1' }" class="font-bold text-sm px-2 pb-1.5">
               Matches
               @if(auth()->user()->matches()->count() >0)
               <span class="rounded-full tx-xs  p-1 px-2 font-bold text-white bg-meetcast">
                   {{auth()->user()->matches()->count()}}
               </span>
               @endif
           </button>

           <button @click="tab='2'" :class="{ 'border-b-2 border-violet-500': tab=='2' }" class="font-bold text-sm px-2 pb-1.5">
               Messages
               <span class="rounded-full tx-xs  p-1 px-2 font-bold text-white bg-meetcast">
                   1
               </span>
           </button>

       </header>
       <main>
           {{-- matches --}}
           <aside class="px-2" x-show="tab=='1'">
               <div class="grid grid-cols-3 gap-2">


                      @foreach ($matches as $i=> $match) 
                   
                   <div wire:click="createConversation('{{$match->id}}')" class="relative cursor-pointer">
                       {{-- dot --}}
                       <span class="-top-6 -right-5 absolute">
                           <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dot text-violet-500 w-12 h-12" viewBox="0 0 16 16">
                               <path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3"/>
                             </svg>
                       </span>
                       <img src="https://source.unsplash.com/200x200?face-woman-{{$i}}" alt="image" class="h-36 rounded-lg object-cover">
                      
                       {{-- name --}}
                       <h5 class="absolute rounded-lg bottom-2 bo left-2 text-white font-bold text-xs">
                           {{$match->swipe1->user_id==auth()->id()?$match->swipe2->user->name:$match->swipe1->user->name}}
                       </h5>
                   </div>
                   @endforeach
               </div>
           </aside>
           {{-- messages --}}
           <aside x-cloak class="px-2 " x-show="tab=='2'">
               <ul>
                   @for ($i =0; $i < 2; $i++)
                   <li>
                       <a
                        @class(['flex gap-4 items-center p-2','border-r-4 border-violet-500 bg-white py-3'=>$i==3?true:false])
                        href="#">
                        
                        <div class="relative">
                           <span class="inset-y-0 my-auto absolute -right-7">
                               <svg 
                                   @class([
                                       'w-14 h-14 stroke-[0.3px] stroke-white',
                                       'hidden'=>$i==3?false:true,
                                       'text-violet-500' =>true
                                   ])
                                xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dot" viewBox="0 0 16 16">
                                   <path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3"/>
                                 </svg>
                           </span>
                           <x-avatar class="h-14 w-14" src="https://source.unsplash.com/200x200?face-woman-{{$i}}" />
                        </div>

                        <div class="overflow-hidden">
                           <h6 class="font-bold truncate">{{fake()->name}}</h6>
                           <p class="text-gray-600 truncate">{{fake()->text}}</p>
                        </div>
                       </a>
                   </li>
                   @endfor
               </ul>
           </aside>
       </main>

   </section>
</div>
