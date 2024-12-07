<div class="modal fade show" tabindex="-1" role="dialog" style="display: {{ $attributes->get('wire:model') ? 'block' : 'none' }};">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $title }}</h5>
            </div>
            <div class="modal-body">
                {{ $content }}
            </div>
            <div class="modal-footer">
                {{ $footer }}
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
</div> 