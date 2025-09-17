<div>
    @livewire('component.page.breadcrumb', ['breadcrumbs' => [['name' => __('ems.application'), 'url' => '/'], ['name' => __('ems.attendance'), 'url' => route('attendance.index')]]], key('breadcrumb'))

    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-md-6" data-tour="attendance-search">
                    <div class="card">
                        <div class="card-body">
                            <label for="form-label">{{ __('ems.search') }}</label>
                            <input type="text" class="form-control" wire:model.live="search"
                                placeholder="{{ __('ems.search_for') }}">
                        </div>
                    </div>
                </div>

                <div class="col-md-6" data-tour="attendance-date">
                    <div class="card">
                        <div class="card-body">

                            <label for="form-label">{{ __('ems.date') }}</label>
                            <div class="input-daterange input-group" id="attendance-inputgroup"
                                data-provide="datepicker" data-date-format="yyyy-mm-dd"
                                data-date-container='#attendance-inputgroup' data-date-autoclose="true">
                                <input type="text" class="form-control @error('start_date') is-invalid @enderror"
                                    wire:model="start_date" placeholder="{{ __('ems.start_date') }}" name="start" />
                                @error('start_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <input type="text" class="form-control @error('end_date') is-invalid @enderror"
                                    wire:model="end_date" placeholder="{{ __('ems.end_date') }}" name="end" />
                                @error('end_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-12 text-end mb-3" data-tour="attendance-create">
                    <button class="btn btn-warning mt-2" wire:click="resetFilter" wire:loading.attr="disabled">{{ __('ems.reset') }}
                        {{ __('ems.filter') }}</button>
                    @can('create:attendance')
                        <a href="{{ route('attendance.create') }}" class="btn btn-primary mt-2">{{ __('ems.attendance_now') }}</a>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{ $attendances->links() }}

        <div class="col-lg-12" data-tour="attendance-table">
            @livewire('attendance.attendance-list', ['attendances' => $attendances->items()], key('attendance-list'))
        </div>

        {{ $attendances->links() }}
    </div>


    @push('styles')
        <link href="{{ asset('libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
        <link href="{{ asset('libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    @endpush

    @push('js')
        <script src="{{ asset('libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
        <script src="{{ asset('libs/select2/js/select2.min.js') }}"></script>
        <script>
            document.addEventListener('livewire:init', function() {
                initDatePicker();
                // let selectElement = $('.select2-multiple');

                function initDatePicker() {
                    $('#attendance-inputgroup').datepicker({
                        format: 'yyyy-mm-dd',
                        autoclose: true,
                        todayHighlight: true,
                        inputs: $('#attendance-inputgroup').find('input')
                    }).on('changeDate', function(e) {
                        // Update the Livewire property when date is selected
                        let startDate = $('#attendance-inputgroup').find('input[name="start"]').val();
                        let endDate = $('#attendance-inputgroup').find('input[name="end"]').val();

                        @this.set('start_date', startDate);
                        @this.set('end_date', endDate);
                    });
                }

            });
        </script>

        
    @endpush

    
</div>
