<div>
    @livewire('component.page.breadcrumb', ['breadcrumbs' => [['name' => 'Application', 'url' => '/'], ['name' => 'Overtime Request', 'url' => route('overtime-request.index')], ['name' => $mode . ' Overtime Request', 'url' => '#']]], key('breadcrumb'))

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">{{ $mode }} Overtime Request</h4>

                    <form wire:submit.prevent="save">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Start Date & Time <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror" 
                                           id="start_date" wire:model="start_date" min="{{ date('Y-m-d\TH:i') }}">
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">End Date & Time <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" 
                                           id="end_date" wire:model="end_date">
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                                    <select class="form-control @error('priority') is-invalid @enderror" 
                                            id="priority" wire:model="priority">
                                        <option value="">Select Priority</option>
                                        <option value="minor">Minor</option>
                                        <option value="major">Major</option>
                                    </select>
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('reason') is-invalid @enderror" 
                                      id="reason" wire:model="reason" rows="4" 
                                      placeholder="Please provide a detailed reason for overtime request"></textarea>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3" wire:ignore>
                            <label for="recipients" class="form-label">Recipients <span class="text-danger">*</span></label>
                            <select class="form-control select2-multiple @error('recipients') is-invalid @enderror" 
                                    id="recipients" multiple data-placeholder="Select Recipients">
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->user->name }}</option>
                                @endforeach
                            </select>
                            @error('recipients')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end">
                            <a href="{{ route('overtime-request.index') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove>{{ $mode == 'Create' ? 'Create' : 'Update' }} Overtime Request</span>
                                <span wire:loading>Processing...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link href="{{ asset('libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    @endpush

    @push('js')
        <script src="{{ asset('libs/select2/js/select2.min.js') }}"></script>
        <script>
            document.addEventListener('livewire:init', function() {
                let selectElement = $('.select2-multiple');

                selectElement.select2({
                    width: '100%',
                }).on('change', function() {
                    let selectedValues = $(this).val();
                    Livewire.dispatch('change-input-form', ['recipients', selectedValues]);
                });

                // Listen for default form values
                Livewire.on('set-default-form', (event) => {
                    const param = event.param;
                    const value = event.value;
                    
                    if (param === 'recipients') {
                        selectElement.val(value).trigger('change');
                    }
                });
            });
        </script>
    @endpush
</div>
