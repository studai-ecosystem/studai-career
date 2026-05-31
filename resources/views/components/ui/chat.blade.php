@props([
    'messages' => 'messages',     // Livewire/Alpine collection name on the wire
    'placeholder' => 'Type a message…',
    'sendMethod' => 'sendMessage',
    'model' => 'message',         // wire:model target
    'typing' => 'isTyping',       // wire boolean for the typing indicator
    'suggestions' => [],          // array of suggested prompt strings
    'emptyTitle' => 'Start the conversation',
    'emptyBody' => 'Ask anything to begin.',
])

{{--
    MERIDIAN single chat component for all conversational UIs.
    User messages: right-aligned, accent fill.
    AI messages:   left-aligned, bordered surface.
--}}
<div {{ $attributes->merge(['class' => 'flex flex-col h-full bg-surface border border-border rounded-lg overflow-hidden']) }}>

    {{-- Message stream --}}
    <div
        class="flex-1 overflow-y-auto px-6 py-6 space-y-4"
        x-data
        x-ref="stream"
        @scroll-to-bottom.window="$nextTick(() => $refs.stream.scrollTop = $refs.stream.scrollHeight)"
    >
        @if (isset($stream))
            {{ $stream }}
        @else
            <template x-if="!$wire.{{ $messages }} || $wire.{{ $messages }}.length === 0">
                <div class="h-full flex flex-col items-center justify-center text-center py-12">
                    <h3 class="font-display text-24 text-ink-1">{{ $emptyTitle }}</h3>
                    <p class="mt-1 text-14 text-ink-3 max-w-reading">{{ $emptyBody }}</p>
                </div>
            </template>

            <template x-for="(msg, i) in ($wire.{{ $messages }} || [])" :key="i">
                <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                    <div
                        :class="msg.role === 'user'
                            ? 'max-w-[80%] rounded-lg px-4 py-2.5 bg-accent text-white text-14'
                            : 'max-w-[80%] rounded-lg px-4 py-2.5 bg-surface border border-border text-ink-1 text-14'"
                        x-text="msg.content"
                    ></div>
                </div>
            </template>
        @endif

        {{-- Typing indicator --}}
        <div class="flex justify-start" wire:loading.flex wire:target="{{ $sendMethod }}" style="display:none;">
            <div class="rounded-lg px-4 py-3 bg-surface border border-border flex items-center gap-1.5">
                <span class="meridian-typing-dot w-1.5 h-1.5 rounded-full bg-ink-3"></span>
                <span class="meridian-typing-dot w-1.5 h-1.5 rounded-full bg-ink-3" style="animation-delay:.2s"></span>
                <span class="meridian-typing-dot w-1.5 h-1.5 rounded-full bg-ink-3" style="animation-delay:.4s"></span>
            </div>
        </div>
    </div>

    {{-- Suggested prompts (ghost buttons) --}}
    @if (!empty($suggestions))
        <div class="px-6 pb-3 flex flex-wrap gap-2">
            @foreach ($suggestions as $suggestion)
                <button
                    type="button"
                    wire:click="$set('{{ $model }}', @js($suggestion))"
                    class="inline-flex items-center h-8 px-3 rounded-sm text-12 font-medium text-ink-2 bg-transparent border border-border hover:bg-surface-raised hover:text-ink-1 transition-colors"
                >
                    {{ $suggestion }}
                </button>
            @endforeach
        </div>
    @endif

    {{-- Composer --}}
    <form wire:submit="{{ $sendMethod }}" class="border-t border-border p-4 flex items-end gap-2">
        <textarea
            wire:model="{{ $model }}"
            rows="1"
            placeholder="{{ $placeholder }}"
            class="flex-1 resize-none max-h-32 px-3 py-2.5 font-ui text-14 text-ink-1 bg-surface border border-border rounded-md placeholder:text-ink-4 focus:outline-none focus:border-accent focus:ring-4 focus:ring-accent-subtle"
            @keydown.enter.prevent="$wire.{{ $sendMethod }}()"
        ></textarea>
        <x-ui.button type="submit" size="md">Send</x-ui.button>
    </form>
</div>
