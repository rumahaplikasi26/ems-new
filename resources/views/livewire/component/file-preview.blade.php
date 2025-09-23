<div class="file-preview-component">
    <div class="d-flex align-items-center justify-content-between p-3 border rounded bg-light">
        <div class="d-flex align-items-center">
            <i class="mdi {{ $this->getFileIcon() }} me-2" style="font-size: 24px;"></i>
            <div>
                <h6 class="mb-0">{{ $fileName }}</h6>
                <small class="text-muted">{{ strtoupper($fileType) }} File</small>
            </div>
        </div>
        <div class="d-flex gap-2">
            @if($this->canPreview())
                <button type="button" class="btn btn-sm btn-outline-info" wire:click="togglePreview">
                    <i class="mdi mdi-eye me-1"></i> {{ __('ems.preview') }}
                </button>
            @endif
            <a href="{{ $fileUrl }}" target="_blank" class="btn btn-sm btn-outline-primary">
                <i class="mdi mdi-open-in-new me-1"></i> {{ __('ems.open') }}
            </a>
            <a href="{{ $fileUrl }}" download class="btn btn-sm btn-primary">
                <i class="mdi mdi-download me-1"></i> {{ __('ems.download') }}
            </a>
        </div>
    </div>

    @if($showPreview && $this->canPreview())
        <div class="mt-3 border rounded">
            <div class="d-flex justify-content-between align-items-center p-2 border-bottom bg-light">
                <h6 class="mb-0">{{ __('ems.preview') }}: {{ $fileName }}</h6>
                <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="togglePreview">
                    <i class="mdi mdi-close"></i>
                </button>
            </div>
            <div class="p-3" style="max-height: 500px; overflow-y: auto;">
                @if($fileType === 'pdf')
                    <iframe src="{{ $fileUrl }}" width="100%" height="400" style="border: none;"></iframe>
                @elseif(in_array($fileType, ['jpg', 'jpeg', 'png', 'gif']))
                    <img src="{{ $fileUrl }}" alt="{{ $fileName }}" class="img-fluid" style="max-width: 100%;">
                @elseif($fileType === 'txt')
                    <div class="alert alert-info">
                        <i class="mdi mdi-information me-2"></i>
                        {{ __('ems.text_file_preview_not_available') }}
                        <a href="{{ $fileUrl }}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                            {{ __('ems.open_in_new_tab') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
