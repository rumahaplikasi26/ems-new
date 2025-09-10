<?php

namespace App\Exports;

use App\Models\OvertimeRequest;
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

class OvertimeRequestReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
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
        $this->reports = collect($reports);
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
            'Start Date & Time',
            'End Date & Time',
            'Duration (Hours)',
            'Duration (Days)',
            'Priority',
            'Reason',
            'Status'
        ];
    }

    public function map($overtimeRequest): array
    {
        $startDate = Carbon::parse($overtimeRequest['start_date']);
        $endDate = Carbon::parse($overtimeRequest['end_date']);
        $durationHours = $startDate->diffInHours($endDate, true);
        $durationDays = $startDate->startOfDay()->diffInDays($endDate->startOfDay()) + 1;

        return [
            $overtimeRequest['employee_id'],
            $overtimeRequest['employee']['user']['name'] ?? 'N/A',
            $startDate->format('d/m/Y H:i'),
            $endDate->format('d/m/Y H:i'),
            number_format($durationHours, 1),
            $durationDays,
            ucfirst($overtimeRequest['priority'] ?? 'N/A'),
            $overtimeRequest['reason'] ?? '-',
            $overtimeRequest['is_approved'] ? 'Approved' : 'Pending'
        ];
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
            'C' => 18, // Start Date & Time
            'D' => 18, // End Date & Time
            'E' => 15, // Duration Hours
            'F' => 15, // Duration Days
            'G' => 12, // Priority
            'H' => 40, // Reason
            'I' => 12, // Status
        ];
    }

    public function title(): string
    {
        return 'Overtime Request Report';
    }
}
