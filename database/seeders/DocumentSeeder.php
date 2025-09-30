<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Document;
use Carbon\Carbon;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::create(2025, 12, 7, 18, 0, 0);

        Document::create([
            'name' => 'Dokumen semua korupsi.pdf',
            'status' => 'Selesai',
            'created_at' => $now,
            'updated_at' => $now
        ]);

        Document::create([
            'name' => 'Dokumen penjualan pulau.pdf',
            'status' => 'Ditolak',
            'created_at' => $now,
            'updated_at' => $now
        ]);

        Document::create([
            'name' => 'Pasar gelap bebas akses.pdf',
            'status' => 'Selesai',
            'created_at' => $now,
            'updated_at' => $now
        ]);

        Document::create([
            'name' => 'Dokumen semua korupsi.pdf',
            'status' => 'Tertunda',
            'created_at' => $now,
            'updated_at' => $now
        ]);

        Document::create([
            'name' => 'Strategi Korupsi Projek Jalan Layang.pdf',
            'status' => 'Selesai',
            'created_at' => $now,
            'updated_at' => $now
        ]);
    }
}