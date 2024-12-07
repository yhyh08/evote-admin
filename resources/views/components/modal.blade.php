@props(['id' => null, 'maxWidth' => null])

<div
    x-data="{ show: @entangle($attributes->wire('model')).defer }"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    id="{{ $id }}"
    class="fixed inset-0 z-50 px-4 py-6 overflow-y-auto sm:px-0"
    style="display: none;"
>
    <div class="fixed inset-0 transform transition-all" x-on:click="show = false">
        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
    </div>

    <div class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full {{ $maxWidth ?? 'sm:max-w-lg' }} sm:mx-auto"
        x-show="show"
        x-on:click.away="show = false">
        {{ $slot }}
    </div>
</div> 