<div>
    @livewire('component.page.breadcrumb', ['breadcrumbs' => [
        ['name' => __('ems.application'), 'url' => '/'], 
        ['name' => 'Shift Schedule', 'url' => route('shift-schedule.index')], 
        ['name' => $is_edit ? 'Edit Shift Schedule' : 'Create Shift Schedule']
    ]], key('breadcrumb'))

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">{{ $is_edit ? 'Edit Shift Schedule' : 'Create Shift Schedule' }}</h4>
                    
                    <form wire:submit.prevent="save" class="needs-validation">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="employee_id" class="form-label">Employee <span class="text-danger">*</span></label>
                                    <select class="form-select @error('employee_id') is-invalid @enderror" 
                                            id="employee_id" 
                                            wire:model="employee_id">
                                        <option value="">Select Employee</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}">
                                                {{ $employee->user->name }} ({{ $employee->id }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('employee_id')
                                        <div class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="shift_id" class="form-label">Shift <span class="text-danger">*</span></label>
                                    <select class="form-select @error('shift_id') is-invalid @enderror" 
                                            id="shift_id" 
                                            wire:model="shift_id">
                                        <option value="">Select Shift</option>
                                        @foreach ($shifts as $shift)
                                            <option value="{{ $shift->id }}">
                                                {{ $shift->name }} ({{ $shift->start_time }} - {{ $shift->end_time }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('shift_id')
                                        <div class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                                    <input type="date" 
                                           class="form-control @error('date') is-invalid @enderror" 
                                           id="date" 
                                           wire:model="date">
                                    @error('date')
                                        <div class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="is_active" 
                                               wire:model="is_active">
                                        <label class="form-check-label" for="is_active">
                                            Active
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" 
                                      rows="3" 
                                      wire:model="notes" 
                                      placeholder="Optional notes about this shift schedule..."></textarea>
                            @error('notes')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('shift-schedule.index') }}" class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left me-1"></i> Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-content-save me-1"></i> 
                                {{ $is_edit ? 'Update' : 'Create' }} Schedule
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>