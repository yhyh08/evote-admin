@props(['type' => 'success', 'dismissible' => true])

@if (session()->has($type))
    <div {{ $attributes->merge(['class' => "alert alert-{$type} alert-dismissible fade show"]) }}
         x-data="{ show: true }" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 3000)"
         role="alert">
        <div class="d-flex align-items-center justify-content-between">
            <span class="flex-grow-1">{{ session($type) }}</span>
            @if($dismissible)
                <button type="button" 
                        class="btn-close ms-2"
                        data-bs-dismiss="alert" 
                        aria-label="Close"
                        @click="show = false">
                    <span aria-hidden="true">&times;</span>
                </button>
            @endif
        </div>
    </div>
@endif 