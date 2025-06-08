<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

// DIUBAH: Nama Class
class PupukPopulerChart extends ChartWidget
{
    // DIUBAH: Judul Widget
    protected static ?string $heading = 'Top 5 Pupuk Paling Laris (Status Selesai)';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        // DIUBAH: Query disesuaikan dengan tabel dan kolom baru
        $data = DB::table('item_pesanan') // Mulai dari tabel pivot baru
            ->join('pesanan', 'item_pesanan.pesanan_id', '=', 'pesanan.id')
            ->join('pupuk', 'item_pesanan.pupuk_id', '=', 'pupuk.id') // Join ke tabel pupuk
            ->select('pupuk.nama_pupuk', DB::raw('SUM(item_pesanan.jumlah) as total_jumlah')) // Pilih nama pupuk
            ->where('pesanan.status', '=', 'selesai') // Filter pesanan yang 'selesai'
            ->groupBy('pupuk.id', 'pupuk.nama_pupuk') // Kelompokkan per pupuk
            ->orderByDesc('total_jumlah')
            ->limit(5)
            ->get();

        // DIUBAH: Ambil nama_pupuk sebagai label
        $labels = $data->pluck('nama_pupuk')->toArray();
        $values = $data->pluck('total_jumlah')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Terjual',
                    'data' => $values,
                    // Warna bisa disesuaikan jika ingin tema warna yang berbeda
                    'backgroundColor' => [
                        'rgba(75, 192, 192, 0.6)', // Hijau
                        'rgba(153, 102, 255, 0.6)',// Ungu
                        'rgba(255, 159, 64, 0.6)', // Oranye
                        'rgba(54, 162, 235, 0.6)', // Biru
                        'rgba(255, 99, 132, 0.6)',  // Merah
                    ],
                    'borderColor' => [
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 99, 132, 1)',
                    ],
                    'borderWidth' => 1
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // Tipe chart: bar, line, pie, doughnut, etc.
    }
}