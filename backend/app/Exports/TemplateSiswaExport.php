<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TemplateSiswaExport implements FromArray, WithHeadings, WithStyles
{
    public function headings(): array
    {
        return [
            'nama',
            'nis',
            'kelas',
            'telepon', // sesuaikan dengan kolom tabel siswa kamu
        ];
    }

    public function array(): array
    {
        // Baris contoh supaya user tahu format isinya
        return [
            ['Budi Santoso', '12345', '6A', '08123456789'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Bold header
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}