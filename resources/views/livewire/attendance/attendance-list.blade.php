<div>
    <div class="table-responsive">
        <table class="table project-list-table table-nowrap align-middle table-borderless">
            <thead>
                <tr>
                    <th scope="col" style="width: 100px">#</th>
                    <th scope="col">{{ __('ems.name') }}</th>
                    <th scope="col">{{ __('ems.timestamp') }}</th>
                    <th scope="col">{{ __('ems.shift') }}</th>
                    <th scope="col">{{ __('ems.distance') }}</th>
                    <th scope="col">{{ __('ems.site_name') }}</th>
                    <th scope="col">{{ __('ems.location') }}</th>
                    <th scope="col">{{ __('ems.notes') }}</th>
                    <th scope="col">{{ __('ems.action') }}</th>
                </tr>
            </thead>
            <tbody id="attendance-lists">
                @if ($attendances)
                    @foreach ($attendances as $attendance)
                        @livewire('attendance.attendance-item', ['attendance' => $attendance], key($attendance['id']))
                    @endforeach
                @else
                    <tr>
                        <td colspan="9" class="text-center fw-bold">{{ __('ems.no_data') }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    @push('js')
        <script>
            document.addEventListener('livewire:init', function() {
                $('#attendance-lists').on('click', '.read-more-in', function(e) {
                    var notes = $(this).data('notes');
                    var id = $(this).data('id');

                    $('#notes-in-' + id).html(notes);
                    $(this).html('{{ __('ems.show_less') }}');
                    $(this).removeClass('read-more-in');
                    $(this).addClass('show-less-in');
                })

                $('#attendance-lists').on('click', '.read-more-out', function(e) {
                    var notes = $(this).data('notes');
                    var id = $(this).data('id');

                    $('#notes-out-' + id).html(notes);
                    $(this).html('{{ __('ems.show_less') }}');
                    $(this).removeClass('read-more-out');
                    $(this).addClass('show-less-out');
                })

                $('#attendance-lists').on('click', '.show-less-in', function(e) {
                    var notes = $(this).data('excerpt');
                    var id = $(this).data('id');

                    $('#notes-in-' + id).html(notes);

                    $(this).html('{{ __('ems.show_more') }}');
                    $(this).removeClass('show-less-in');
                    $(this).addClass('read-more-in');
                })

                $('#attendance-lists').on('click', '.show-less-out', function(e) {
                    var notes = $(this).data('excerpt');
                    var id = $(this).data('id');

                    $('#notes-out-' + id).html(notes);

                    $(this).html('{{ __('ems.show_more') }}');
                    $(this).removeClass('show-less-out');
                    $(this).addClass('read-more-out');
                })
            });
        </script>
    @endpush
</div>
