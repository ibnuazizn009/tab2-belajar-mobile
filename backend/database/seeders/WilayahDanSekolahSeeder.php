<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WilayahDanSekolahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $kotaBogorId = DB::table('kota')->insertGetId([
            'nama_kota' => 'Kota Bogor', 'provinsi' => 'Jawa Barat', 'created_at' => now(), 'updated_at' => now()
        ]);
        $kabBogorId = DB::table('kota')->insertGetId([
            'nama_kota' => 'Kabupaten Bogor', 'provinsi' => 'Jawa Barat', 'created_at' => now(), 'updated_at' => now()
        ]);
        $kotaBandungId = DB::table('kota')->insertGetId([
            'nama_kota' => 'Kota Bandung', 'provinsi' => 'Jawa Barat', 'created_at' => now(), 'updated_at' => now()
        ]);

        DB::table('sekolah')->insert([
            // Sekolah di Kota Bogor
            [
                'npsn' => '20220302',
                'nama_sekolah' => 'SDN Polisi 1',
                'status' => 'NEGERI',
                'alamat' => 'Jl. Paledang No.21, Paledang, Kec. Bogor Tengah',
                'kota_id' => $kotaBogorId, // Terhubung ke Kota Bogor
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'npsn' => '20220147',
                'nama_sekolah' => 'SD Pertiwi Bogor',
                'status' => 'SWASTA',
                'alamat' => 'Jl. Sukasari III No.4, Baranangsiang, Kec. Bogor Timur',
                'kota_id' => $kotaBogorId, // Terhubung ke Kota Bogor
                'created_at' => now(), 'updated_at' => now()
            ],
            // Sekolah di Kabupaten Bogor
            [
                'npsn' => '20200880',
                'nama_sekolah' => 'SDN Cibinong 01',
                'status' => 'NEGERI',
                'alamat' => 'Jl. Mayor Oking No.12, Kec. Cibinong',
                'kota_id' => $kabBogorId, // Terhubung ke Kab Bogor
                'created_at' => now(), 'updated_at' => now()
            ],
            // Sekolah di Kota Bandung
            [
                'npsn' => '20219193',
                'nama_sekolah' => 'SDN 001 Merdeka',
                'status' => 'NEGERI',
                'alamat' => 'Jl. Merdeka No.9, Babakan Ciamis, Kec. Sumur Bandung',
                'kota_id' => $kotaBandungId, // Terhubung ke Kota Bandung
                'created_at' => now(), 'updated_at' => now()
            ]
        ]);
    }
}
