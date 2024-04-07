<div id="tinder" class="m-auto md:p-10 w-full h-full relative">

    {{-- Swipe cards --}}
    <div class="relative h-full md:h-[600px] w-full md:w-96 m-auto">
  
    @for ($i = 0; $i < 50; $i++)
        {{-- Swipe card --}}
      <div 
  
      @swipedright.window="console.log('right')"
      @swipedleft.window="console.log('left')"
      @swipedup.window="console.log('up')"
  
   
          x-data="{
            isSwiping: false,
            swipingLeft: false, 
            swipingRight: false,
            swipingUp: false,
  
            swipeRight:function(){
              moveOutWidth = document.body.clientWidth * 1.5;
            $el.style.transform = 'translate(' + moveOutWidth + 'px, -100px) rotate(-30deg)';
  
            setTimeout(() => {
              $el.remove();
            }, 300);
  
            {{-- Dispatch event --}}
            $dispatch('swipedright');
  
  
            },
  
            swipeUp:function(){
              moveOutWidth = document.body.clientWidth * 1.5;
  
              {{-- Add negative translate --}}
              $el.style.transform = 'translate(0px, ' + -moveOutWidth + 'px) rotate(-20deg)';
  
              setTimeout(() => {
                $el.remove();
  
              }, 300);
  
              {{-- Dispatch event --}}
              $dispatch('swipedup');
  
            },
  
            swipeLeft:function(){
              moveOutWidth = document.body.clientWidth * 1.5;
  
              {{-- Add negative translate --}}
              $el.style.transform = 'translate(-' + moveOutWidth + 'px, -100px) rotate(-30deg)';
  
              setTimeout(() => {
                $el.remove();
  
              }, 300);
  
              {{-- Dispatch event --}}
              $dispatch('swipedleft');
  
            },
  
            }" 
  
            x-init="
            element = $el;
  
            {{-- Initialize hammer js on current element --}}
            var hammertime = new Hammer(element);
  
            {{-- let the pan gesture support all directions. --}}
            hammertime.get('pan').set({
              direction   : Hammer.DIRECTION_ALL,
              touchAction: 'pan'
          });
  
            {{-- ON PAN --}}
            hammertime.on('pan', function (event) {
            
                    isSwiping= true;
                    if (event.deltaX === 0) return;
                    if (event.center.x === 0 && event.center.y === 0) return;
            
                    {{-- Swiped Right --}}
                    if ( event.deltaX > 20) {
            
                      swipingRight=true;//true
                      swipingLeft=false;
                      swipingUp=false;
            
                    } 
                    {{-- Swiped Left --}}
                    else if (event.deltaX < -20) {
                    
                      swipingLeft=true;//true
                      swipingRight=false;
                      swipingUp=false;
            
                    }
                    {{-- Super like feature --}}
                    else if (event.deltaY < -50 && Math.abs(event.deltaX) < 20 ) {
                      swipingUp=true;//true
                      swipingRight=false;
                      swipingLeft=false;
                    }
  
                    {{-- roate deg --}}
                    var rotate = event.deltaX/10;
  
                    {{--  Scroll effect along the Y-axis (upward scroll) --}}
  
                    {{-- Apply the transformation to rotate only in X direction in Clockwise and Anti-Clockwise by 10deg --}}
                    event.target.style.transform = 'translate(' + event.deltaX + 'px, ' + event.deltaY + 'px) rotate(' + rotate + 'deg)';
            
            });
  
  
            {{-- ON PANEND --}}
            hammertime.on('panend', function (event) {
  
              {{-- reset states --}}
              isSwiping =false;
              swipingLeft=false;
              swipingRight = false;
              swipingUp=false;
  
  
              {{-- Set thresholds for horizontal and vertical distances px --}}
              var horizontalThreshold = 200;
              var verticalThreshold = 200;
  
              {{-- Set thresholds for horizontal and vertical velocities --}}
              var velocityXThreshold = 0.5;
              var velocityYThreshold = 0.5;
  
              {{-- Check if the swipe distance and velocity are below the thresholds 
                  for both horizontal and vertical directions --}}
              var keep = Math.abs(event.deltaX) < horizontalThreshold && Math.abs(event.velocityX) < velocityXThreshold &&
                          Math.abs(event.deltaY) < verticalThreshold && Math.abs(event.velocityY) < velocityYThreshold;
  
              if (keep) {
  
                {{-- Adjust the duration and timing function as needed --}}
                event.target.style.transition = 'transform 0.3s ease-in-out';
                event.target.style.transform = '';
                $el.style.transform = '';
            
                {{-- Clear the transition property after the animation completes --}}
                setTimeout(() => {
                  event.target.style.transition = '';
                  event.target.style.transform = '';
                  $el.style.transform = '';
                }, 300); // Use the same duration as the transition
                
              } else {
  
                var moveOutWidth = document.body.clientWidth;
                var moveOutHeight  = document.body.clientHeight;
  
                
                {{-- Decide to push left or right or up --}}
  
                {{-- SwipeRight --}}
                if (event.deltaX > 20) {
                    {{-- Adjust the transform as needed --}}
                  event.target.style.transform = 'translate(' + moveOutWidth + 'px, 10px)';
                  $dispatch('swipedright');
                } 
  
                {{--Swipeleft  --}}
                else if (event.deltaX <-20)  {
                  $dispatch('swipedleft');
                  event.target.style.transform = 'translate(' + -moveOutWidth + 'px, 10px)';
  
                }
  
                {{-- Super like feature --}}
                else if (event.deltaY < -50 && Math.abs(event.deltaX) < 20 ) {
  
                $dispatch('swipedup');
                event.target.style.transform = 'translate(0px, ' + -moveOutHeight + 'px)';
  
                }
  
                {{-- remove element & draggged element from the DOM --}}
                event.target.remove();
                $el.remove();
              }
  
            });
        "
   
          :class="{'transform-none cursor-grab':isSwiping}"
          class="absolute inset-0 m-auto  transform ease-in-out duration-300   rounded-xl  bg-grey-500 cursor-pointer z-50">
            
        <div class="  h-full w-full">
        <div  style="background-image:url('https://source.unsplash.com/500x500?sexy-woman-{{$i}}')"  class="relative overflow-hidden  w-full h-full rounded-xl bg-cover  ">
          
          {{-- Swipe indicators  *pointer-events-none--}}
          <div class="pointer-events-none">
            <span x-cloak :class="{ 'invisible': !swipingRight }" class="border-2  rounded-md p-1 px-2 border-green-500 text-green-500 text-4xl  capitalize  font-extrabold   top-10  left-5 -rotate-12 absolute z-5">
              LIKE
            </span>
            <span x-cloak :class="{ 'invisible': !swipingLeft }" class="border-2  rounded-md p-1 px-2 border-red-500 text-red-500 text-4xl  capitalize  font-extrabold   top-10  right-5 rotate-12 absolute z-5 ">
              NOPE
            </span>
  
            <span  x-cloak :class="{ 'invisible': !swipingUp }" class="border-2  rounded-md p-1 px-2 border-blue-500 text-blue-500 text-5xl  capitalize  font-extrabold   bottom-48   max-w-fit inset-x-0 mx-auto -rotate-12 absolute z-5 ">
              SUPER LIKE
            </span>
          
          </div>
  
          {{-- *Add pointer-events-none--}}
          <section class="absolute inset-x-0 bottom-0 inset-y-1/2 py-2 bg-gradient-to-t from-black   to-black/0  pointer-events-none" >
          
            <div class=" flex flex-col h-full  gap-2.5 mt-auto p-5 text-white">
              {{-- Personal deatils --}}
  
              <div class="grid grid-cols-12 items-center ">
  
                <div class="col-span-10">
  
                  <h4 class="font-bold text-3xl ">
                    Mercy
                  </h4>
  
                  {{-- if no biography show distance  --}}
                  <p class="text-lg line-clamp-3">
                    Lorem ipsum, dolor sit amet consectetur adipisicing elit. Aliquid earum quas 
                  </p>
  
  
                </div>
  
                {{-- 
                  View profile 
                  *Add pointer events auto --}}
                <div class="col-span-2 justify-end flex pointer-events-auto">
                  <button >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 text-white">
                      <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 0 1 .67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 1 1-.671-1.34l.041-.022ZM12 9a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
                    </svg>
                  </button>
  
                  
  
                </div>
  
              </div>
  
              {{-- Actions !Add pointer events --}}
              <div class="grid grid-cols-5 gap-1 items-center mt-auto">
  
                {{-- arrow-heroicons Rewind()--}}
                <div>
  
                <button draggable="false" class="rounded-full border-2 pointer-events-auto group border-yellow-600 p-3 shrink-0 max-w-fit m flex items-center text-yellow-600">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-9 h-9 shrink-0 m-auto  group-hover:scale-105 transition-transform   stroke-2 stroke-current">
                    <path fill-rule="evenodd" d="M9.53 2.47a.75.75 0 0 1 0 1.06L4.81 8.25H15a6.75 6.75 0 0 1 0 13.5h-3a.75.75 0 0 1 0-1.5h3a5.25 5.25 0 1 0 0-10.5H4.81l4.72 4.72a.75.75 0 1 1-1.06 1.06l-6-6a.75.75 0 0 1 0-1.06l6-6a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" />
                  </svg>                
                </button>
              </div>
  
                  {{-- x-heroicons SwipeLeft()--}}
                  <div>
  
                  <button @click="swipeLeft()"   class="rounded-full border-2 pointer-events-auto group border-red-600 p-2 shrink-0 max-w-full flex items-center text-red-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="4" stroke="currentColor" class="w-11 h-11 shrink-0 m-auto group-hover:scale-105 transition-transform">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                  </button>
                    </div>
  
                    {{-- Star-heroicons Superlike() : Add scale-95 --}}
                    <div>
                    <button @click="swipeUp()"  class="rounded-full border-2 pointer-events-auto group border-blue-500 p-1.5 shrink-0 max-w-fit flex items-center text-blue-400 scale-95">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-11 h-11 shrink-0 m-auto group-hover:scale-105 transition-transform">
                        <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                      </svg>
                    </button>
                    </div>
  
  
                      {{-- Heart-heroicons SwipeRight()--}}
                      <div>
  
                  <button @click="swipeRight()"  class="rounded-full border-2 pointer-events-auto group border-green-500 p-2 shrink-0 max-w-full flex items-center text-green-500">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-10 h-10 shrink-0 m-auto group-hover:scale-105 transition-transform">
                      <path d="m11.645 20.91-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z" />
                    </svg>
                  </button>
                      </div>
  
                    {{--Bolt-heroicons Boost()--}}
                    <div>
                    <button class="rounded-full border-2 pointer-events-auto group border-purple-500 p-2 shrink-0 max-w-full flex items-center text-purple-500">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-10 h-10 shrink-0 m-auto group-hover:scale-105 transition-transform">
                        <path fill-rule="evenodd" d="M14.615 1.595a.75.75 0 0 1 .359.852L12.982 9.75h7.268a.75.75 0 0 1 .548 1.262l-10.5 11.25a.75.75 0 0 1-1.272-.71l1.992-7.302H3.75a.75.75 0 0 1-.548-1.262l10.5-11.25a.75.75 0 0 1 .913-.143Z" clip-rule="evenodd" />
                      </svg>
                    </button>
                  </div>
  
              </div>
  
  
  
            </div>
          
          
          </section>
  
      </div>
  
      </div>
  
      </div>
    @endfor
  </div>
  
   
  </div>