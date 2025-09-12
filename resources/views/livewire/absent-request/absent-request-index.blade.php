<div>
    @livewire('component.page.breadcrumb', ['breadcrumbs' => [['name' => __('ems.application'), 'url' => '/'], ['name' => __('ems.absent_request'), 'url' => route('absent-request.index')]]], key('breadcrumb'))

    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-md">
                    <div class="card">
                        <div class="card-body">
                            <label for="form-label">{{ __('ems.search') }}</label>
                            <input type="text" class="form-control" wire:model.live="search" placeholder="{{ __('ems.search_for') }}">
                        </div>
                    </div>
                </div>

                @can('absent-request-all')
                    <div class="col-md">
                        <div class="card">
                            <div class="card-body" wire:ignore>
                                <label for="employees">{{ __('ems.select_employee') }}</label>
                                <select class="form-control select2-multiple" id="employees"
                                    data-placeholder="{{ __('ems.select_employee') }}" wire:model="employee_id" multiple>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                @endcan

                <div class="col-md">
                    <div class="card">
                        <div class="card-body">

                            <label for="form-label">{{ __('ems.date') }}</label>
                            <div class="input-daterange input-group" id="absent-request-inputgroup"
                                data-provide="datepicker" data-date-format="yyyy-mm-dd"
                                data-date-container='#absent-request-inputgroup' data-date-autoclose="true">
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

                <div class="col-12 text-end mb-3">
                    <button class="btn btn-warning mt-2" wire:click="resetFilter" wire:loading.attr="disabled">{{ __('ems.reset') }}
                        {{ __('ems.filter') }}</button>
                    @can('create:absent-request')
                        <a href="{{ route('absent-request.create') }}" class="btn btn-primary mt-2">{{ __('ems.create') }}</a>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            @livewire('absent-request.absent-request-list', ['absent_requests' => $absent_requests->getCollection()], key('absent-request-list'))
        </div>

        {{ $absent_requests->links() }}
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

                let selectElement = $('.select2-multiple');
                initSelect2();

                function initSelect2() {
                    selectElement.select2({
                        width: '100%',
                    }).on('change', function(e) {
                        let selectedValues = $(this).val();
                        @this.set('employee_id', selectedValues);
                    });
                }

                function initDatePicker() {
                    $('#absent-request-inputgroup').datepicker({
                        format: 'yyyy-mm-dd',
                        autoclose: true,
                        todayHighlight: true,
                        inputs: $('#absent-request-inputgroup').find('input')
                    }).on('changeDate', function(e) {
                        // Update the Livewire property when date is selected
                        let startDate = $('#absent-request-inputgroup').find('input[name="start"]').val();
                        let endDate = $('#absent-request-inputgroup').find('input[name="end"]').val();

                        @this.set('start_date', startDate);
                        @this.set('end_date', endDate);
                    });
                }

            });
        </script>
    @endpush
</div>
