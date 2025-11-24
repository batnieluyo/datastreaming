<div class="flex h-full flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] ">
    <!-- ====== Chat Box Start -->
    <div class="custom-scrollbar max-h-full flex-1 space-y-6 overflow-auto p-5 xl:space-y-2 xl:p-6">
        @foreach($messages as $message)
            @if($message['role'] === 'assistant')
                <div class="max-w-[600px]">
                    <div class="flex items-start gap-4 px-3 py-2">
                        <p class="text-sm text-gray-800 dark:text-white/90">
                            {{ $message['body'] }}
                        </p>
                    </div>
                </div>
            @else
                <div class="ml-auto max-w-[600px] text-right">
                    <div class="ml-auto max-w-max rounded-lg rounded-tr-sm bg-blue-500 px-3 py-2 dark:bg-brand-500">
                        <p class="text-sm text-white dark:text-white/90">
                            {{ $message['body'] }}
                        </p>
                    </div>
                </div>
            @endif
        @endforeach

        <div wire:stream="streaming" class="max-w-[600px] text-sm text-gray-800 dark:text-white/90 px-3 py-2">
            @if($thinking)<span>Pensando...</span>@endif
        </div>

    </div>
    <div class="sticky bottom-0 border-t border-gray-200 p-3 dark:border-gray-800">
        <form wire:submit="send" class="flex items-center justify-between">
            <div class="relative w-full">
                <input wire:model="body" type="text" placeholder="Type a message" class="h-9 w-full border-none bg-transparent pl-12 pr-5 text-sm text-gray-800 outline-hidden placeholder:text-gray-400 focus:border-0 focus:ring-0 dark:text-white/90">
            </div>

            <div class="flex items-center">
                <button type="submit" class="mr-2 text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white/90 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                    </svg>
                </button>
            </div>
        </form>
    </div>
    <!-- ====== Chat Box End -->
</div>

@script
<script>
    $wire.on('question-created', () => {
        Livewire.dispatch('talk-with-magi');
    });
</script>
@endscript