<div class="flex flex-col h-full max-w-4xl mx-auto" x-data="coachChat()">

    <!-- Toast Error -->
    <div
        x-show="errorMsg"
        x-transition
        class="fixed top-4 right-4 z-50 bg-red-600 text-white px-4 py-3 rounded-lg shadow-lg flex items-center gap-3"
        style="display:none"
    >
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span x-text="errorMsg"></span>
        <button @click="errorMsg = ''" class="ml-2 text-white/80 hover:text-white">&#x2715;</button>
    </div>

    <!-- Messages Container -->
    <div class="flex-1 overflow-y-auto p-4 space-y-4" id="messages-container">
        @foreach($messages as $msg)
        <div class="flex {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }}" wire:key="msg-{{ $msg['id'] }}">
            <div class="max-w-[80%]">
                @if($msg['role'] === 'assistant')
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center flex-shrink-0">
                        <span class="text-lg">&#127919;</span>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl rounded-tl-none px-4 py-3 shadow-sm">
                        <div class="prose prose-sm dark:prose-invert max-w-none">
                            {!! \Illuminate\Support\Str::markdown($msg['content']) !!}
                        </div>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">{{ $msg['created_at'] }}</p>
                    </div>
                </div>
                @else
                <div class="bg-indigo-600 text-white rounded-2xl rounded-tr-none px-4 py-3">
                    <p class="whitespace-pre-wrap">{{ $msg['content'] }}</p>
                    <p class="text-xs text-indigo-200 mt-2">{{ $msg['created_at'] }}</p>
                </div>
                @endif
            </div>
        </div>
        @endforeach

        <div wire:loading wire:target="sendMessage" class="flex justify-start">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center flex-shrink-0">
                    <span class="text-lg">&#127919;</span>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl rounded-tl-none px-4 py-3 shadow-sm">
                    <div class="flex items-center gap-2">
                        <div class="flex gap-1">
                            <span class="w-2 h-2 bg-indigo-600 rounded-full animate-bounce" style="animation-delay:0ms"></span>
                            <span class="w-2 h-2 bg-indigo-600 rounded-full animate-bounce" style="animation-delay:150ms"></span>
                            <span class="w-2 h-2 bg-indigo-600 rounded-full animate-bounce" style="animation-delay:300ms"></span>
                        </div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Thinking...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Input Area -->
    <div class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-4">
        <form wire:submit="sendMessage" class="flex items-end gap-3">
            <div class="flex-1 relative">
                <textarea
                    wire:model="message"
                    placeholder="Type your message..."
                    rows="1"
                    :disabled="listening"
                    class="w-full px-4 py-3 pr-12 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white resize-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent disabled:opacity-60"
                    x-on:keydown.enter.prevent="if (!$event.shiftKey) { $wire.sendMessage(); }"
                    x-on:input="$el.style.height = 'auto'; $el.style.height = Math.min($el.scrollHeight, 150) + 'px';"
                ></textarea>
                <button type="button" @click="toggleVoice()"
                    :class="listening ? 'text-red-600 bg-red-50 animate-pulse' : 'text-gray-400 hover:text-indigo-600 hover:bg-indigo-50'"
                    class="absolute right-3 bottom-3 p-1 rounded-full transition-colors"
                    :title="listening ? 'Stop listening' : 'Voice input'">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 14c1.66 0 3-1.34 3-3V5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3zm-1-9c0-.55.45-1 1-1s1 .45 1 1v6c0 .55-.45 1-1 1s-1-.45-1-1V5zm6 6c0 2.76-2.24 5-5 5s-5-2.24-5-5H5c0 3.53 2.61 6.43 6 6.92V21h2v-3.08c3.39-.49 6-3.39 6-6.92h-2z"/>
                    </svg>
                </button>
            </div>
            <button type="submit" wire:loading.attr="disabled" wire:loading.class="opacity-50 cursor-not-allowed" :disabled="listening"
                class="px-4 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                <span wire:loading.remove wire:target="sendMessage">Send</span>
                <span wire:loading wire:target="sendMessage">Sending...</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
            </button>
        </form>

        <div x-show="listening" x-transition class="mt-2 flex items-center gap-2 text-sm text-red-600" style="display:none">
            <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse inline-block"></span>
            <span x-text="voiceStatus"></span>
            <button @click="stopVoice()" class="ml-auto text-xs underline">Stop</button>
        </div>
        <div x-show="noSpeechApi" x-transition class="mt-2 text-xs text-amber-600" style="display:none">
            Your browser does not support voice input. Try Chrome or Edge.
        </div>

        <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
            <div class="text-sm text-gray-500 dark:text-gray-400">{{ count($messages) }} messages</div>
            <button wire:click="endSession" wire:confirm="Are you sure you want to end this session?"
                class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                End Session
            </button>
        </div>
    </div>

</div>


