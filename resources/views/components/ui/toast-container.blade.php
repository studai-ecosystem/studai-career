@props([
    'defaultDuration' => 4000,
])

{{--
    MERIDIAN toast container — the single toast system for the app.
    Top-right, 360px wide, status communicated by a left accent bar.
    Fire from anywhere:  window.dispatchEvent(new CustomEvent('toast', {
        detail: { type: 'success'|'error'|'warning'|'info', title, message, duration }
    }))
    Or from Livewire:  $this->dispatch('toast', type: 'success', title: 'Saved');
--}}
<div
    x-data="{
        toasts: [],
        add(detail) {
            const id = Date.now() + Math.random();
            const duration = detail.duration ?? {{ $defaultDuration }};
            this.toasts.push({ id, type: detail.type || 'info', title: detail.title || '', message: detail.message || '' });
            if (duration > 0) setTimeout(() => this.remove(id), duration);
        },
        remove(id) { this.toasts = this.toasts.filter(t => t.id !== id); },
        bar(type) {
            return {
                success: 'var(--color-success)',
                error: 'var(--color-error)',
                warning: 'var(--color-warning)',
                info: 'var(--color-accent)',
            }[type] || 'var(--color-accent)';
        }
    }"
    x-on:toast.window="add($event.detail)"
    class="fixed top-4 right-4 z-50 flex flex-col gap-2 pointer-events-none"
    style="width: 360px;"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="true"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-x-2"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-x-0"
            x-transition:leave-end="opacity-0 translate-x-2"
            class="pointer-events-auto relative flex items-start gap-3 bg-surface border border-border rounded-md shadow-popover overflow-hidden pl-4 pr-3 py-3"
        >
            {{-- Left status bar --}}
            <span class="absolute left-0 top-0 bottom-0 w-1" :style="`background-color: ${bar(toast.type)}`"></span>

            <div class="flex-1 min-w-0">
                <p class="text-14 font-medium text-ink-1" x-text="toast.title" x-show="toast.title"></p>
                <p class="text-12 text-ink-3 mt-0.5" x-text="toast.message" x-show="toast.message"></p>
            </div>

            <button
                type="button"
                @click="remove(toast.id)"
                class="shrink-0 text-ink-4 hover:text-ink-1 transition-colors"
                aria-label="Dismiss"
            >
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M4 4l8 8M12 4l-8 8" stroke-linecap="round"/>
                </svg>
            </button>
        </div>
    </template>
</div>
