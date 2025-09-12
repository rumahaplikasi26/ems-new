<div class="card" id="working-hours-analytics">
    <div class="card-body">
        <div class="clearfix">
            <div class="float-end">
                <div class="input-group input-group-sm" wire:loading.remove>
                    <select class="form-select form-select-sm" wire:model.live="selectedMonth">
                        @foreach ($months as $month => $monthName)
                            <option value="{{ $month }}">{{ $monthName }}</option>
                        @endforeach
                    </select>
                    <label class="input-group-text">{{ __('ems.month') }}</label>
                </div>

                <div wire:loading>
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>

            <h4 class="card-title mb-4">{{ __('ems.employees_working_hours_analytics') }}</h4>
        </div>

        <div class="table-responsive mt-3">
            <table class="table table-sm mb-0 table-hover table-borderless">
                <thead class="table-light align-middle text-center">
                    <tr>
                        <th scope="col" rowspan="2">{{ __('ems.name') }}</th>
                        <th scope="col" colspan="2">{{ __('ems.total_working_hours') }}</th>
                        <th scope="col" rowspan="2">{{ __('ems.percentage') }}</th>
                        <th scope="col" rowspan="2">{{ __('ems.detail') }}</th>
                    </tr>
                    <tr>
                        <th scope="col">{{ __('ems.this_month') }}</th>
                        <th scope="col">{{ __('ems.last_month') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employees as $employee)
                        <tr class="text-center">
                            <td class="text-start">{{ $employee['name'] }}</td>
                            <td>{{ number_format($employee['this_month'], 2) }} hrs</td>
                            <td>{{ number_format($employee['last_month'], 2) }} hrs</td>
                            <td>
                                <span class="badge @if ($employee['percentage'] >= 0) bg-success @else bg-danger @endif">
                                    {{ $employee['percentage'] }}%
                                </span>
                            </td>
                            <td>
                                <div wire:loading wire:target="openEmployeeChart">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>

                                <div wire:loading.remove wire:target="openEmployeeChart">
                                    <button wire:click="openEmployeeChart({{ $employee['id'] }}, {{ $selectedMonth }})"
                                        class="btn btn-sm btn-primary">Detail</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $employees->links() }}
        </div>
    </div>

    @livewire('component.widget.table.working-hours-analytics-modal')

    @push('js')
        <script>
            document.addEventListener('livewire:init', function() {
                Livewire.on('refresh-data-working-hours-analytics', () => {
                    const target = document.getElementById('working-hours-analytics');
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        </script>
    @endpush
</div>
