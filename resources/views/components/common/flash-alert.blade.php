@props([
    'type' => 'success',
    'message' => null,
    'duration' => 4500,
])

@php
    $type = in_array($type, ['success', 'error', 'warning', 'info']) ? $type : 'success';
    $styles = [
        'success' => [
            'box' => 'bg-success-50 text-success-800 border-success-200 dark:bg-success-900/20 dark:text-success-400 dark:border-success-800/30',
            'icon' => 'ti ti-circle-check text-success-500',
            'button' => 'hover:bg-success-100 dark:hover:bg-success-800/40 text-success-700 dark:text-success-300',
        ],
        'error' => [
            'box' => 'bg-error-50 text-error-800 border-error-200 dark:bg-error-900/20 dark:text-error-400 dark:border-error-800/30',
            'icon' => 'ti ti-alert-circle text-error-500',
            'button' => 'hover:bg-error-100 dark:hover:bg-error-800/40 text-error-700 dark:text-error-300',
        ],
        'warning' => [
            'box' => 'bg-warning-50 text-warning-800 border-warning-200 dark:bg-warning-900/20 dark:text-warning-400 dark:border-warning-800/30',
            'icon' => 'ti ti-alert-triangle text-warning-500',
            'button' => 'hover:bg-warning-100 dark:hover:bg-warning-800/40 text-warning-700 dark:text-warning-300',
        ],
        'info' => [
            'box' => 'bg-blue-50 text-blue-800 border-blue-200 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800/30',
            'icon' => 'ti ti-info-circle text-blue-500',
            'button' => 'hover:bg-blue-100 dark:hover:bg-blue-800/40 text-blue-700 dark:text-blue-300',
        ],
    ];
    $current = $styles[$type];
@endphp

<div
    x-data="{ 
        show: true,
        timeout: null,
        startTimer() {
            this.timeout = setTimeout(() => this.show = false, {{ $duration }});
        },
        pauseTimer() {
            if (this.timeout) clearTimeout(this.timeout);
        }
    }"
    x-init="startTimer()"
    x-show="show"
    x-transition:leave="transition ease-out duration-300"
    x-transition:leave-start="opacity-100 transform translate-y-0"
    x-transition:leave-end="opacity-0 transform -translate-y-2"
    @mouseenter="pauseTimer()"
    @mouseleave="startTimer()"
    role="alert"
    class="flash-alert rounded-lg p-4 border flex items-center justify-between gap-3 shadow-sm {{ $current['box'] }}">
    <div class="flex items-center gap-2.5">
        <i class="{{ $current['icon'] }} text-xl shrink-0"></i>
        <div class="text-sm font-medium leading-relaxed">
            {{ $message ?? $slot }}
        </div>
    </div>
    <button @click="show = false" type="button" class="p-1 rounded-lg transition shrink-0 {{ $current['button'] }}" title="Tutup">
        <i class="ti ti-x text-base"></i>
    </button>
</div>
