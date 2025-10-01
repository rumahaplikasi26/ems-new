<?php

namespace App\Exports;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\AbsentRequest;
use App\Models\LeaveRequest;
use App\Helpers\ShiftHelper;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use DatePeriod;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class AttendanceReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    public $startDate;
    public $endDate;
    public $selectedEmployees;
    public $selectedShifts;
    public $dateRange;
    public $countDays;
    public $reports;

    public function __construct($startDate, $endDate, $selectedEmployees, $reports, $selectedShifts = [])
    {
        $this->startDate = Carbon::parse($startDate)->startOfDay();
        $this->endDate = Carbon::parse($endDate)->endOfDay();
        $this->selectedEmployees = $selectedEmployees;
        $this->selectedShifts = $selectedShifts;
        $this->reports = collect($reports);
        $this->countDays = daysBetween($this->startDate, $this->endDate) + 1;
        
        $this->dateRange = new DatePeriod(
            Carbon::parse($this->startDate),
            CarbonInterval::day(),
            Carbon::parse($this->endDate)
        );
    }

    public function collection()
    {
        return $this->reports;
    }

    public function headings(): array
    {
        $headings = [
            'Employee ID',
            'Employee Name'
        ];

        // Add date columns
        foreach ($this->dateRange as $date) {
            $headings[] = $date->format('d/m/Y');
        }

        return $headings;
    }

    public function map($report): array
    {
        $row = [
            $report['employee_id'],
            $report['name']
        ];

        // Add attendance data for each date
        foreach ($report['attendance_data'] as $timeRange) {
            $row[] = $timeRange;
        }

        return $row;
    }

    public function styles(Worksheet $sheet)
    {
        $highestColumn = $sheet->getHighestColumn();
        $highestRow = $sheet->getHighestRow();

        // Style the header row
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray($headerStyle);

        // Style the employee info columns (A and B)
        $employeeInfoStyle = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F2F2F2'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $sheet->getStyle('A2:B' . $highestRow)->applyFromArray($employeeInfoStyle);

        // Style the attendance data columns
        $attendanceStyle = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $sheet->getStyle('C2:' . $highestColumn . $highestRow)->applyFromArray($attendanceStyle);

        // Auto-filter
        $sheet->setAutoFilter('A1:' . $highestColumn . '1');

        // Freeze panes (freeze first 2 columns and first row)
        $sheet->freezePane('C2');

        return $sheet;
    }

    public function columnWidths(): array
    {
        $widths = [
            'A' => 15, // Employee ID
            'B' => 25, // Employee Name
        ];

        // Set width for date columns
        $column = 'C';
        foreach ($this->dateRange as $date) {
            $widths[$column] = 12;
            $column++;
        }

        return $widths;
    }

    public function title(): string
    {
        $title = 'Attendance Report';
        if (!empty($this->selectedShifts)) {
            $shiftNames = \App\Models\Shift::whereIn('id', $this->selectedShifts)->pluck('name')->implode(', ');
            $title .= ' - Shifts: ' . $shiftNames;
        }
        return $title;
    }
}
