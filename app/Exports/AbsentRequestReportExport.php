<?php

namespace App\Exports;

use App\Models\AbsentRequest;
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

class AbsentRequestReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
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
            'Start Date',
            'End Date',
            'Duration (Days)',
            'Type',
            'Notes',
            'Status'
        ];
    }

    public function map($absentRequest): array
    {
        $startDate = Carbon::parse($absentRequest->start_date);
        $endDate = Carbon::parse($absentRequest->end_date);
        $duration = $startDate->diffInDays($endDate) + 1;

        return [
            $absentRequest->employee_id,
            $absentRequest->employee->user->name ?? 'N/A',
            $startDate->format('d/m/Y'),
            $endDate->format('d/m/Y'),
            $duration,
            $absentRequest->type_absent ?? 'N/A',
            $absentRequest->notes ?? '-',
            $absentRequest->is_approved ? 'Approved' : 'Pending'
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
            'C' => 15, // Start Date
            'D' => 15, // End Date
            'E' => 15, // Duration
            'F' => 15, // Type
            'G' => 30, // Notes
            'H' => 12, // Status
        ];
    }

    public function title(): string
    {
        return 'Absent Request Report';
    }
}
