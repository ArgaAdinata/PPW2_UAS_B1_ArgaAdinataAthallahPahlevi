<?php

namespace Database\Seeders;

use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class TransaksiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $startDate = Carbon::create(2024, 11, 1); // Mulai dari 1 November 2024
        $endDate = Carbon::create(2024, 11, 10); // Sampai 10 November 2024

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            $numberOfTransactions = $faker->numberBetween(15, 20); // Jumlah transaksi per hari (acak antara 15-20)

            for ($i = 0; $i < $numberOfTransactions; $i++) {
                $total_harga = $faker->numberBetween(10000, 500000);
                $bayar = $faker->numberBetween($total_harga, $total_harga + 100000);
                $kembalian = $bayar - $total_harga;

                Transaksi::create([
                    'tanggal_pembelian' => $date->format('Y-m-d'),
                    'total_harga' => $total_harga,
                    'bayar' => $bayar,
                    'kembalian' => $kembalian,
                ]);
            }
        }
    }
}
