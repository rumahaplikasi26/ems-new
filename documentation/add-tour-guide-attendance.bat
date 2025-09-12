@echo off
echo Adding Tour Guide to Attendance Management...

REM Add tour guide button to attendance index
echo Processing Attendance Index...
powershell -Command "(Get-Content 'resources\views\livewire\attendance\attendance-index.blade.php') -replace '<div>', '<div>@include(''components.tour-guide-button'')' | Set-Content 'resources\views\livewire\attendance\attendance-index.blade.php'"

REM Add tour guide scripts to attendance index
echo Adding tour guide scripts to attendance index...
powershell -Command "(Get-Content 'resources\views\livewire\attendance\attendance-index.blade.php') -replace '@endpush', '@include(''components.tour-guide-scripts'')@endpush' | Set-Content 'resources\views\livewire\attendance\attendance-index.blade.php'"

echo Tour Guide has been added to Attendance Management!
echo Please test the tour guide functionality.
pause
