<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\TransaksiDetail;

class DashboardController extends Controller
{
    public function index()
    {
        $transaksi_count = Transaksi::count();
        $item_count = TransaksiDetail::sum('jumlah');
        $omzet = Transaksi::sum('total_harga');

        return view('dashboard', compact('transaksi_count', 'item_count', 'omzet'));
    }
}
