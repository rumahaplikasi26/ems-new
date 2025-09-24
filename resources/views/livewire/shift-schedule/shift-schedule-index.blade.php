<div>
    @livewire('component.page.breadcrumb', ['breadcrumbs' => [
        ['name' => __('ems.application'), 'url' => '/'], 
        ['name' => 'Shift Schedule Management']
    ]], key('breadcrumb'))

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">Shift Schedule Management</h4>
                        <a href="{{ route('shift-schedule.create') }}" class="btn btn-primary">
                            <i class="mdi mdi-plus me-1"></i> Create Schedule
                        </a>
                    </div>

                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <input type="text" 
                                   class="form-control" 
                                   placeholder="Search employee..." 
                                   wire:model.live="search">
                        </div>
                        <div class="col-md-2">
                            <input type="date" 
                                   class="form-control" 
                                   wire:model.live="date_filter">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" wire:model.live="employee_filter">
                                <option value="">All Employees</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">
                                        {{ $employee->user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" wire:model.live="shift_filter">
                                <option value="">All Shifts</option>
                                @foreach ($shifts as $shift)
                                    <option value="{{ $shift->id }}">
                                        {{ $shift->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="button" 
                                    class="btn btn-outline-secondary w-100" 
                                    wire:click="$set('search', ''); $set('date_filter', '{{ \Carbon\Carbon::today()->format('Y-m-d') }}'); $set('employee_filter', ''); $set('shift_filter', '')">
                                Reset
                            </button>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Employee</th>
                                    <th>Shift</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($schedules as $schedule)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>{{ $schedule->employee->user->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $schedule->employee->id }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">
                                                {{ $schedule->shift->name }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $schedule->date->format('d/m/Y') }}
                                            <br>
                                            <small class="text-muted">{{ $schedule->date->format('l') }}</small>
                                        </td>
                                        <td>
                                            {{ $schedule->shift->start_time }} - {{ $schedule->shift->end_time }}
                                        </td>
                                        <td>
                                            @if($schedule->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($schedule->notes)
                                                <span class="text-truncate d-inline-block" style="max-width: 150px;" 
                                                      title="{{ $schedule->notes }}">
                                                    {{ $schedule->notes }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('shift-schedule.edit', $schedule->id) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger"
                                                        wire:click="deleteSchedule({{ $schedule->id }})"
                                                        wire:confirm="Are you sure you want to delete this shift schedule?">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="mdi mdi-information-outline me-2"></i>
                                                No shift schedules found
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($schedules->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $schedules->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>