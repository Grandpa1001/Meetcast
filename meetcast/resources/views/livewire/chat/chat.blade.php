<div
x-data="{
  height:0,
  conversationElement: document.getElementById('conversation'),
}"


x-init="
    height=conversationElement.scrollHeight;
    $nextTick(()=> conversationElement.scrollTop=height);

    Echo.private('users.{{auth()->id()}}')
    .notification((notification)=>{
        if(
            notification['type']== 'App\\Notifications\\MessageSentNotification' &&
            notification['conversation_id']== {{$conversation->id}})
        {

            $wire.listenBroadcastedMessage(notification);
            console.log(notification.type);

        }
    });

 "

@scroll-bottom.window="
$nextTick(()=>{ 
  {{--overflow-y: hidden; is used to hide the vertical scrollbar initially. --}}
  conversationElement.style.overflowY='hidden';

  {{-- scroll the element down --}}
  conversationElement.scrollTop= conversationElement.scrollHeight;

  {{-- After updating the chat height, overflowY is set back to 'auto', 
                 which allows the browser to determine whether to display the scrollbar 
                 based on the content height.  --}}
  conversationElement.style.overflowY='auto';

} );

"
class="flex h-screen overflow-hidden">

    <main class="w-full grow border flex flex-col relative ">

        {{-- Header --}}
        <header class="flex items-center gap-2.5 p-2 border">

        
            {{-- return button --}}
            {{-- Chevron left Icon from --}}
            <a  wire:navigate href="{{route('chat.index')}}" class="sm:hidden">
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-left  h-6 w-6 stroke-2" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0"/>
                      </svg>
 
                </span>
            </a>


            <x-avatar />
            <h5 class="font-bold text-gray-500 truncate">
                {{$receiver->name}}
            </h5>

            <div class="ml-auto flex items-center gap-2 px-2">

                {{-- Dots icon from BS --}}
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-three-dots text-gray-500 w-7 h-7" viewBox="0 0 16 16">
                        <path
                            d="M3 9.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3m5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3m5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3" />
                    </svg>

                </span>

                {{-- x-octagon-fill icon BS --}}
                {{-- Cancel and return to home --}}
                <a wire:navigate href="{{route('app')}}">
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-x-octagon-fill text-gray-500 w-7 h-7" viewBox="0 0 16 16">
                            <path
                                d="M11.46.146A.5.5 0 0 0 11.107 0H4.893a.5.5 0 0 0-.353.146L.146 4.54A.5.5 0 0 0 0 4.893v6.214a.5.5 0 0 0 .146.353l4.394 4.394a.5.5 0 0 0 .353.146h6.214a.5.5 0 0 0 .353-.146l4.394-4.394a.5.5 0 0 0 .146-.353V4.893a.5.5 0 0 0-.146-.353zm-6.106 4.5L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 1 1 .708-.708" />
                        </svg>
                    </span>
                </a>

            </div>


        </header>

        {{-- Body --}}
        <section

        @scroll="
         scrollTop= $el.scrollTop;
         console.log(scrollTop);
         {{-- Note scrollbar/thumb is 0 at the op --}}
         if(scrollTop<=0){
            @this.dispatch('loadMore');
         }
         "


        @update-height.window="
        $nextTick(()=>{

            newHeight=$el.scrollHeight;

            oldHeight= height;

            $el.scrollTop=newHeight-oldHeight;

            height=newHeight;



        });
    
    "
        
        id="conversation" class="flex flex-col   gap-2  overflow-auto h-full  p-2.5  overflow-y-auto flex-grow  overflow-x-hidden w-full my-auto ">

            @foreach ($loadedMessages as $message)
            @php
                $belongsToAuth=  $message->sender_id==auth()->id();
            @endphp
                
                <div 
                wire:ignore
                @class([
                    'max-w-[85%] md:max-w-[78%] flex  w-auto  gap-2 relative mt-2',
                    'ml-auto'=>$belongsToAuth// SET true if belongs to auth
                     ])>
                    {{-- Avatar --}}
                    <div @class([
                        'shrink-0 mt-auto',
                         'invisible'=>$belongsToAuth //SET true if belongs to auth
                        ])>
                        <x-avatar class="h-7 w-7  " src="https://source.unsplash.com/1600x900/?face" />
                    </div>

                    {{-- message body --}}
                    <div @class(['flex  flex-wrap text-[15px] border border-gray-200/40 rounded-xl p-2.5 flex flex-col text-black bg-[#f6f6f8fb]',
                                 'bg-blue-500 text-white'=> $belongsToAuth,//SET true if belongs to auth
                                ])
                                >

                        <p class="  whitespace-normal truncate text-sm md:text-base  tracking-wide lg:tracking-normal ">
                            {{$message->body}}
                        </p>
                   
                    </div>

                </div>
            @endforeach

        </section>

        {{-- Footer --}}
        <footer class=" sticky bottom-0 py-2 inset-x-0  p-2 ">

                <form 
                    x-data="{body:@entangle('body')}" 
                    method="POST"
                    @submit.prevent="$wire.sendMessage()"
                    autocapitalize="off">
                    @csrf
                    {{-- we put hidden inpu to prevent authcomplete --}}
                    <input type="hidden" autocomplete="false" style="display:none">

                    <div class="grid grid-cols-12 gap-3 items-center">
                        {{-- spotify icon BS --}}
                        <span class="col-span-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-spotify w-8 h-8 text-gray-500" viewBox="0 0 16 16">
                                <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0m3.669 11.538a.5.5 0 0 1-.686.165c-1.879-1.147-4.243-1.407-7.028-.77a.499.499 0 0 1-.222-.973c3.048-.696 5.662-.397 7.77.892a.5.5 0 0 1 .166.686m.979-2.178a.624.624 0 0 1-.858.205c-2.15-1.321-5.428-1.704-7.972-.932a.625.625 0 0 1-.362-1.194c2.905-.881 6.517-.454 8.986 1.063a.624.624 0 0 1 .206.858m.084-2.268C10.154 5.56 5.9 5.419 3.438 6.166a.748.748 0 1 1-.434-1.432c2.825-.857 7.523-.692 10.492 1.07a.747.747 0 1 1-.764 1.288"/>
                              </svg>
                        </span>
                        <input x-model="body" type="text" autocomplete="off" autofocus
                            placeholder="write your message here" maxlength="1700"
                            class="col-span-9 bg-gray-100 border-0 outline-0 focus:border-0 focus:ring-0 hover:ring-0 rounded-lg  focus:outline-none">

                        <button x-bind:disabled="!body?.trim()" class="col-span-2" type='submit'>Send</button>

                    </div>

                </form>
        </footer>

    </main>

    {{-- Aside User frofile --}}
    <aside class=" w-[52%] hidden sm:flex border ">
        <div style="content: contain"
        class=" inset-0 overflow-y-auto overflow-hidden overscroll-contain  w-full bg-white space-y-4">

        @php
        $slides=[
        'https://source.unsplash.com/500x500?sexy-woman-'.rand(2,10),
        'https://source.unsplash.com/500x500?sexy-woman-'.rand(2,10),
        'https://source.unsplash.com/500x500?sexy-woman-'.rand(2,10)
        ];

        @endphp
        {{-- Carousel --}}

        <section x-data="{ activeSlide: 1, slides: @js($slides) }" class="relative h-96  ">

          <!-- Slides: Using template to scroll throuh images -->
          <template x-for="(image, index) in slides" :key="index">
            {{-- in here no need to ad draggble just add pointer events to none --}}
            <img x-show="activeSlide === index + 1" :src="image" alt="Slide Image"
              class=" absolute  inset-0 w-full h-full object-cover">
          </template>

          <!--Image Pagination -->
          <div :class="{'hidden':slides.length==1}"
            class="absolute top-1  inset-x-0 z-10  w-full flex items-center justify-center ">
            <template x-for="(image, index) in slides" :key="index">
              <button class="flex-1 w-4 h-2 mx-1   rounded-full overflow-hidden "
                :class="{ 'bg-white': activeSlide === index + 1, 'bg-gray-500': activeSlide !== index + 1 }"
                x-on:click="activeSlide = index + 1"></button>
            </template>
          </div>

          <!-- Prev  -->
          <button :class="{'hidden':slides.length==1}" class="absolute left-2 top-1/2 my-auto"
            x-on:click="activeSlide = activeSlide === 1 ? slides.length : activeSlide - 1">
            {{-- Chevron-left from heroIcons --}}
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3"
              stroke="currentColor" class="w-6 h-6 text-white">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
            </svg>
          </button>

          <!--Next Arrows -->
          <button :class="{'hidden':slides.length==1}" class="absolute right-2 top-1/2 my-auto"
            x-on:click="activeSlide = activeSlide === slides.length ? 1 : activeSlide + 1">
            {{-- Chevron-right from heroIcons --}}
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3"
              stroke="currentColor" class="w-6 h-6 text-white">
              <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
          </button>

        </section>

        {{-- Profile information --}}
        <section class="grid gap-4 p-3 ">

          <div class="flex items-center text-3xl gap-3 text-wrap">
            <h3 class="font-bold "> {{$receiver->name}} </h3>
            <span class=" font-semibold text-gray-800">
              {{$receiver->age}}
            </span>
          </div>

          {{--about --}}
          <ul class="">
            <li class="items-center text-gray-600 text-lg ">
              {{$receiver->profession}}
            </li>
            <li class="items-center text-gray-600 text-lg ">
              {{$receiver->height?$receiver->height.' cm':''}}
            </li>

            <li class="items-center text-gray-600 text-lg ">
              {{$receiver->city?'Lives in '.$receiver->city:''}}
            </li>
          </ul>


          <hr class="-mx-2.5">

          {{-- Bio --}}

          <p class="text-gray-600 ">
            {{$receiver->about}}

          </p>

          {{-- Relationship goals --}}
          <div class="rounded-xl bg-green-200 h-24 px-4 py-2 max-w-fit flex gap-4 items-center">

            <div class=" text-3xl"> 👋 </div>
            <div class=" grid w-4/5  ">
              <span class="font-bold text-sm text-green-800">Looking for</span>

              <span class="text-lg text-green-800 capitalize">{{str_replace('_',' ',$receiver->relationship_goals?->value)}}
              </span>
            </div>

          </div>

          {{-- More informaion --}}
          <section class="divide-y  space-y-2">

            @if ($receiver->languages)

            <div class="space-y-3 py-2">

              <h3 class="font-bold text-xl">Languages i know</h3>

              <ul class="flex flex-wrap gap-3 ">
                @foreach ($receiver->languages as $language)
                <li class="border border-gray-500 rounded-2xl text-sm px-2.5 p-1.5 capitalize">{{$language->name}}</li>
                @endforeach
              </ul>

            </div>

            @endif


            @if ($receiver->basics)

            <div class="space-y-3 py-2">

              <h3 class="font-bold text-xl">Basics</h3>

              <ul class="flex flex-wrap gap-3 ">
                @foreach ($receiver->basics as $basic)
                <li class="border border-gray-500 rounded-2xl text-sm px-2.5 p-1.5">{{$basic->name}}</li>
                @endforeach
              </ul>

            </div>

            @endif

            {{-- Same concept for the ones below Student can create more if wishes to --}}

            {{-- LifeStyle --}}
            <div class="space-y-3 py-2">
              <h3 class="font-bold text-xl">Lifestyle</h3>

              <ul class="flex flex-wrap gap-3 ">
                <li class="border border-gray-500 rounded-2xl text-sm px-2.5 p-1.5">Non smoker</li>
                <li class="border border-gray-500 rounded-2xl text-sm px-2.5 p-1.5">Dont have but love</li>
                <li class="border border-gray-500 rounded-2xl text-sm px-2.5 p-1.5">Not for me</li>
                <li class="border border-gray-500 rounded-2xl text-sm px-2.5 p-1.5">Gym</li>
              </ul>

            </div>


            {{-- Passions --}}
            <div class="space-y-3 py-2">
              <h3 class="font-bold text-xl">Passions</h3>

              <ul class="flex flex-wrap gap-3 ">
                <li class="border border-gray-500 rounded-2xl text-sm px-2.5 p-1.5">Freelance</li>
                <li class="border border-gray-500 rounded-2xl text-sm px-2.5 p-1.5">Politics</li>
                <li class="border border-gray-500 rounded-2xl text-sm px-2.5 p-1.5">pimms</li>
                <li class="border border-gray-500 rounded-2xl text-sm px-2.5 p-1.5">Cafe Hoping</li>
              </ul>

            </div>

          </section>



        </section>

      </div>

    </aside>


</div>