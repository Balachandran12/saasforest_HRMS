<x-filament-panels::page>
    <style>
        /* This is based on the work of Joshua Saunders */
        .list-layout {
            /* display: grid; */
            /* grid-gap: 10px; */
        }
        .board-text {
            font-weight: bold;
            font-size: 28px;
            padding: 5px;
        }
        .board-lists {
            /* display: grid; */
            /* display: flex; */
            /* grid-auto-columns: 275px; */
            /* grid-auto-flow: column; */
            /* grid-gap: 10px; */
            /* height: 100%; */
            /* background-color: red;
            height: 800px; */
        }
        .board-list {
            /* background-color: rgb(235, 236, 240); */
            border-radius: 3px;
             display: grid;
            grid-auto-rows: max-content;
            grid-gap: 10px;
            /* Chrome use a fixed height */
            /* height: 300px; */
            /* padding: 10px; */
            /* background-color: red; */
            height: 800px;
        }
        .list-title {
            font-size: 18px;
            font-weight: bold;
        }
        .card {
            /* background-color: white; */
            /* border-radius: 3px; */
            /* box-shadow: 0 1px 0 rgba(9, 30, 66, .25); */
            /* padding: 10px; */
            cursor: pointer;
        }
    </style>
<div x-data="{
    value: @entangle('clicktap'),
    set: 'change text-[#E0BF00] font-medium py-2 px-5 rounded-lg shadow-[0px_1px_3px_0px_rgba(0,0,0,0.16)]',
    first: 'cursor-pointer',}">
    <div class=" flex gap-x-10">
        <button class=" text-[#4B5563] font-medium" wire:click="OpenTap('1')"
        :class="value == '1' ? set : first" >Board</button>
        <button wire:click="OpenTap('2')"
        :class="value == '2' ? set : first" class=" text-[#4B5563] font-medium">Chat</button>
        <button wire:click="OpenTap('3')"
        :class="value == '3' ? set : first" class=" text-[#4B5563] font-medium">Docs</button>
    </div>
{{-- Task card Section --}}
  @if($clicktap=='1')
    <div class=" pt-8">
        <img src="/icon/line.svg" alt="" class=" w-full">
        <div id='boardlists' class="board-lists  flex justify-between pt-10">
            <div id='list1' class="board-list w-[280px]" ondrop="dropIt(event)" ondragover="allowDrop(event)">
                <div class="list-title flex gap-x-10">
                    <span class="text-[#172B4D]">UNDER REVIEW</span>
                    <span class=" flex items-center justify-center  bg-[#E5E5E5] rounded-full h-6 w-6">{{$count_assined}}</span>
                </div>
                @foreach ($project_assined as $assined)
                    <div class="border border-[#D1D5D8] rounded-[14px] p-5 shadow-[0px_1px_3px_0px_rgba(0,0,0,0.07)] mt-10" wire:key="{{$assined->id}}" id='{{ $assined->id }}' class="card " draggable="true" ondragstart="dragStart(event)" wire:click="open({{$assined->task_id}})">
                        <div>
                            <b>{{ $assined->task->name }}</b>
                            <p>{{ $assined->task->description }}</p>
                        </div>
                        {{-- @dd($pro_task) --}}
                        <div class=" pt-5">
                            <img src="{{$assined->users->employee->profile_picture_url}}" alt="" class="h-10 w-10 rounded-full">
                        </div>
                    </div>
                @endforeach
            </div>
            <div id='list2' class="board-list  w-[280px]" ondrop="dropIt(event)" ondragover="allowDrop(event)">
                <div class="list-title flex gap-x-10">
                    <span class="text-[#172B4D]">IN PROGRESS</span>
                    <span class="flex items-center justify-center  bg-[#E5E5E5] rounded-full h-6 w-6">{{$count_inpro}}</span>
                </div>
                @foreach ($project_inpro as $inprogress)

                <div class="border border-[#D1D5D8] rounded-[14px] p-5 shadow-[0px_1px_3px_0px_rgba(0,0,0,0.07)] mt-10" wire:key="{{$inprogress->id}}" id='{{ $inprogress->id }}' class="card" draggable="true" ondragstart="dragStart(event)" wire:click="open({{$inprogress->task_id}})">
                    <div>
                        <b>{{ $inprogress->task->name }}</b>
                        <p>{{ $inprogress->task->description }}</p>
                    </div>
                    <div class=" pt-5 flex gap-5 items-center">
                        <img src="{{$inprogress->users->employee->profile_picture_url}}" alt="" class="h-10 w-10 rounded-full">
                        <p>AssinedBy:{{$inprogress->createdBy->name}}</p>
                    </div>
                </div>
                @endforeach
            </div>
            {{-- ready for qa --}}
            <div id='list3' class="board-list  w-[280px]" ondrop="dropIt(event)" ondragover="allowDrop(event)">
                <div class="list-title flex gap-x-10">
                    <span class="text-[#172B4D]">READY FOR QA</span>
                    <span class="flex items-center justify-center  bg-[#E5E5E5] rounded-full h-6 w-6">{{$count_ready}}</span>
                </div>
                @foreach ($project_ready as $ready)
                <div class="border border-[#D1D5D8] rounded-[14px] p-5 shadow-[0px_1px_3px_0px_rgba(0,0,0,0.07)] mt-10" wire:key="{{$ready->id}}" id='{{ $ready->id }}' class="card" :draggable="{{auth()->id()}}=={{$ready->user_id}}||{{auth()->id()}}=={{$ready->created_by}}?'true':'false'" ondragstart="dragStart(event)" wire:click="open({{$ready->task_id}})">
                    <div>
                        <b>{{ $ready->task->name }}</b>
                        <p>{{ $ready->task->description }}</p>
                    </div>
                    <div class="flex gap-5 items-center pt-5">
                        <img src="{{$ready->users->employee->profile_picture_url}}" alt="" class="h-10 w-10 rounded-full">
                  
                        {{-- <p>AssinedBy:{{$ready->createdBy->name}}</p> --}}
                    </div>
                </div>
                @endforeach
            </div>
            {{-- ready for qa end  --}}
            <div id='list4' class="board-list  w-[280px]" ondrop="dropIt(event)" ondragover="allowDrop(event)">
                <div class="list-title flex gap-x-10">
                    <span class="text-[#172B4D]">DONE</span>
                    <span class="flex items-center justify-center  bg-[#E5E5E5] rounded-full h-6 w-6">{{$count_done}}</span>
                </div>
                @foreach ($project_done as $done)
                <div class="border border-[#D1D5D8] rounded-[14px] p-5 shadow-[0px_1px_3px_0px_rgba(0,0,0,0.07)] mt-10" wire:key="{{$done->id}}" id='{{ $done->id }}' class="card" draggable="true" ondragstart="dragStart(event)" wire:click="open({{$done->task_id}})">
                    <div>
                        <b>{{ $done->task->name }}</b>
                        <p>{{ $done->task->description }}</p>
                    </div>
                    <div class="flex gap-5 pt-5 items-center">
                        <img src="{{$done->users->employee->profile_picture_url}}" alt="" class="h-10 w-10 rounded-full">
                        {{-- <p>AssinedBy:{{$done->createdBy->name}}</p> --}}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
    {{-- Figure out end --}}
{{-- Task card section end --}}
    {{-- open model for card details view --}}

        <x-filament::modal alignment="center" id="open" width="3xl">

            <x-slot name="heading" >
               {{$project_name}}
            </x-slot>
            <b>Task Name</b>
            <x-filament::input.wrapper width="100px">
                <x-filament::input
                    type="text"
                    wire:model="task_name"
                />
            </x-filament::input.wrapper>
            <b>Task Description</b>
            <textarea name="" id="" cols="40" rows="5" wire:model="task_desc">{{$this->task_desc}}</textarea>
            <b>Assiner</b>
            <x-filament::input.wrapper>

                <x-filament::input
                    type="text"
                    wire:model="task_assiner"
                />
            </x-filament::input.wrapper>

        {{-- Modal content --}}
        </x-filament::modal>


    {{-- open model for card details view --}}
</div>
    <script>
        function allowDrop(ev) {
            console.log(ev);
            ev.preventDefault(); // default is not to allow drop
        }
        function dragStart(ev, n) {
            ev.dataTransfer.setData("text/plain", ev.target.id, +"," + n);
        }
        function dropIt(ev) {
            ev.preventDefault(); // default is not to allow drop
            console.log('Item dropped!');
            let sourceId = ev.dataTransfer.getData("text/plain");
            let sourceIdEl = document.getElementById(sourceId);
            let sourceIdParentEl = sourceIdEl.parentElement;
            let targetEl = document.getElementById(ev.target.id)
            console.log(targetEl.id);
            let targetParentEl = targetEl.parentElement;
            if (targetParentEl.id !== sourceIdParentEl.id) {
                if (targetEl.className === sourceIdEl.className) {
                    console.log('yes');
                    Livewire.dispatch('post-created', {
                        postId: sourceId,
                        id: targetEl.id
                    })
                    targetParentEl.appendChild(sourceIdEl);
                } else {
                    Livewire.dispatch('post-created', {
                        postId: sourceId,
                        id: targetEl.id
                    })
                    targetEl.appendChild(sourceIdEl);
                }
            }
            // else {
            //     console.log('no');
            //     let holder = targetEl;
            //     let holderText = holder.textContent;
            //     targetEl.textContent = sourceIdEl.textContent;
            //     sourceIdEl.textContent = holderText;
            //     holderText = '';
            // }
        }
    </script>
</x-filament-panels::page>
