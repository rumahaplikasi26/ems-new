<?php

namespace App\Exports;

use App\Models\LeaveRequest;
use App\Models\Employee;
use Carbon\Carbon;
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

class LeaveRequestReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    public $startDate;
    public $endDate;
    public $selectedEmployees;
    public $reports;

    public function __construct($startDate, $endDate, $selectedEmployees, $reports)
    {
        $this->startDate = Carbon::parse($startDate)->startOfDay();
        $this->endDate = Carbon::parse($endDate)->endOfDay();
        $this->selectedEmployees = $selectedEmployees;
        // Ensure we have a collection, whether it's passed as array or collection
        $this->reports = is_array($reports) ? collect($reports) : $reports;
    }

    public function collection()
    {
        return $this->reports;
    }

    public function headings(): array
    {
        return [
            'Employee ID',
            'Employee Name',
            'Start Date',
            'End Date',
            'Total Days',
            'Current Leave',
            'Remaining Leave',
            'Notes'
        ];
    }

    public function map($leaveRequest): array
    {
        try {
            // Handle both object and array data
            $startDate = Carbon::parse(is_array($leaveRequest) ? $leaveRequest['start_date'] : $leaveRequest->start_date);
            $endDate = Carbon::parse(is_array($leaveRequest) ? $leaveRequest['end_date'] : $leaveRequest->end_date);

            // Get employee name safely
            $employeeName = 'N/A';
            if (is_array($leaveRequest)) {
                $employeeName = $leaveRequest['employee']['user']['name'] ?? 'N/A';
            } else {
                $employeeName = $leaveRequest->employee->user->name ?? 'N/A';
            }

            return [
                is_array($leaveRequest) ? $leaveRequest['employee_id'] : $leaveRequest->employee_id,
                $employeeName,
                $startDate->format('d/m/Y'),
                $endDate->format('d/m/Y'),
                is_array($leaveRequest) ? ($leaveRequest['total_days'] ?? 0) : ($leaveRequest->total_days ?? 0),
                is_array($leaveRequest) ? ($leaveRequest['current_total_leave'] ?? 0) : ($leaveRequest->current_total_leave ?? 0),
                is_array($leaveRequest) ? ($leaveRequest['total_leave_after_request'] ?? 0) : ($leaveRequest->total_leave_after_request ?? 0),
                is_array($leaveRequest) ? ($leaveRequest['notes'] ?? '-') : ($leaveRequest->notes ?? '-')
            ];
        } catch (\Exception $e) {
            // Return error data if mapping fails
            return [
                'ERROR',
                'Error: ' . $e->getMessage(),
                'N/A',
                'N/A',
                0,
                0,
                0,
                'Error'
            ];
        }
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

        // Style the data columns
        $dataStyle = [
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

        $sheet->getStyle('C2:' . $highestColumn . $highestRow)->applyFromArray($dataStyle);

        // Auto-filter
        $sheet->setAutoFilter('A1:' . $highestColumn . '1');

        // Freeze panes (freeze first row)
        $sheet->freezePane('A2');

        return $sheet;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // Employee ID
            'B' => 25, // Employee Name
            'C' => 15, // Start Date
            'D' => 15, // End Date
            'E' => 12, // Total Days
            'F' => 15, // Current Leave
            'G' => 15, // Remaining Leave
            'H' => 30, // Notes
        ];
    }

    public function title(): string
    {
        return 'Leave Request Report';
    }
}
