<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SiswaImport implements ToModel, WithHeadingRow, WithValidation
{
    protected $sekolahId;

    public function __construct($sekolahId) {
        $this->sekolahId = $sekolahId;
    }

    public function model(array $row)
    {
        // Lookup kelas by nama, filter by sekolah
        $kelas = Kelas::where('nama_kelas', $row['kelas'])
                      ->where('sekolah_id', $this->sekolahId)
                      ->first();

        if (!$kelas) {
            throw new \Exception("Kelas '{$row['kelas']}' tidak ditemukan.");
        }

        return new Siswa([
            'sekolah_id'      => $this->sekolahId,
            'nis'             => $row['nis'],
            'nama_siswa'      => $row['nama_siswa'],
            'kelas_id'        => $kelas->id,
            'no_wa_orang_tua' => $row['no_wa_orang_tua'],
            'is_active'       => $row['is_active'] ?? 1,
            'saldo'           => 0,
        ]);
    }

    public function rules(): array
    {
        return [
            'nis'             => 'required',
            'nama_siswa'      => 'required',
            'no_wa_orang_tua' => 'required',
            'kelas'           => 'required',
        ];
    }
}