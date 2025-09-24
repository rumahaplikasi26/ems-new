<div>
    @livewire('component.page.breadcrumb', ['breadcrumbs' => [['name' => 'Application', 'url' => '/'], ['name' => 'Attendance', 'url' => route('attendance.index')]]], key('breadcrumb'))

    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body" wire:ignore>
                            <label for="form-label">Select Employees</label>
                            <select name="employees" wire:model="employees" class="form-select select2-multiple"
                                id="" multiple data-placeholder="Select Employees">
                                <option value="all">Select All</option>

                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->user->name }}</option>
                                @endforeach
                            </select>

                            @error('employees')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body" wire:ignore>
                            <label for="form-label">Select Shifts (Optional)</label>
                            <select name="shifts" wire:model="shifts" class="form-select select2-shift"
                                id="" multiple data-placeholder="Select Shifts">
                                <option value="all">All Shifts</option>

                                @foreach ($shifts as $shift)
                                    <option value="{{ $shift->id }}">{{ $shift->name }}</option>
                                @endforeach
                            </select>

                            @error('shifts')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">

                            <label for="form-label">Date</label>
                            <div class="input-daterange input-group" id="attendance-inputgroup"
                                data-provide="datepicker" data-date-format="yyyy-mm-dd"
                                data-date-container='#attendance-inputgroup' data-date-autoclose="true">
                                <input type="text" class="form-control @error('startDate') is-invalid @enderror"
                                    wire:model="startDate" placeholder="Start Date" name="start" />

                                @error('startDate')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <input type="text" class="form-control @error('endDate') is-invalid @enderror"
                                    wire:model="endDate" placeholder="End Date" name="end" />
                                @error('endDate')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-12 text-end mb-3">
                    <button class="btn btn-warning mt-2" wire:click="resetFilter" wire:loading.attr="disabled">Reset
                        Filter</button>
                    <button class="btn btn-primary mt-2" wire:click="preview"
                        wire:loading.attr="disabled">Preview</button>
                    <button class="btn btn-success mt-2" wire:click="exportExcel"
                        wire:loading.attr="disabled">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>
                </div>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            @livewire('report.attendance-preview', key('preview'))
        </div>
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
                let shiftElement = $('.select2-shift');

                selectElement.select2({
                    width: '100%',
                }).on('change', function() {
                    let selectedValues = $(this).val();
                    // console.log(selectedValues);
                    Livewire.dispatch('change-input-form', ['selectedEmployees', selectedValues]);
                });

                selectElement.on("select2:select", function(e) {
                    var data = e.params.data.text;
                    if (data == 'Select All') {
                        $(".select2-multiple > option").prop("selected", "selected");
                        $('.select2-multiple > option[value="all"]').prop("selected", false);
                        $(".select2-multiple").trigger("change");
                    }
                });

                shiftElement.select2({
                    width: '100%',
                }).on('change', function() {
                    let selectedValues = $(this).val();
                    Livewire.dispatch('change-input-form', ['selectedShifts', selectedValues]);
                });

                shiftElement.on("select2:select", function(e) {
                    var data = e.params.data.text;
                    if (data == 'All Shifts') {
                        $(".select2-shift > option").prop("selected", "selected");
                        $('.select2-shift > option[value="all"]').prop("selected", false);
                        $(".select2-shift").trigger("change");
                    }
                });

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

                        @this.set('startDate', startDate);
                        @this.set('endDate', endDate);
                    });
                }

            });
        </script>
    @endpush
</div>
