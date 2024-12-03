<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function index()
    {
        $transaksi = Transaksi::orderBy('tanggal_pembelian', 'DESC')->get();
        return view('transaksi.index', compact('transaksi'));
    }

    public function create()
    {
        return view('transaksi.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'tanggal_pembelian' => 'required|date',
        'bayar' => 'required|numeric',
        'nama_produk1' => 'required|string',
        'harga_satuan1' => 'required|numeric',
        'jumlah1' => 'required|numeric',
        'nama_produk2' => 'nullable|string',
        'harga_satuan2' => 'nullable|numeric',
        'jumlah2' => 'nullable|numeric',
        'nama_produk3' => 'nullable|string',
        'harga_satuan3' => 'nullable|numeric',
        'jumlah3' => 'nullable|numeric',
    ]);

    DB::beginTransaction();

    try {
        $transaksi = new Transaksi();
        $transaksi->tanggal_pembelian = $request->input('tanggal_pembelian');
        $transaksi->total_harga = 0;
        $transaksi->bayar = $request->input('bayar');
        $transaksi->kembalian = 0;
        $transaksi->save();

        $total_harga = 0;

        for ($i = 1; $i <= 3; $i++) {
            if ($request->has('nama_produk' . $i)) {
                $transaksidetail = new TransaksiDetail();
                $transaksidetail->id_transaksi = $transaksi->id;
                $transaksidetail->nama_produk = $request->input('nama_produk'.$i);
                $transaksidetail->harga_satuan = $request->input('harga_satuan'.$i);
                $transaksidetail->jumlah = $request->input('jumlah'.$i);
                $transaksidetail->subtotal = $request->input('harga_satuan'.$i)*$request->input('jumlah'.$i);
                $transaksidetail->save();
                $total_harga += $transaksidetail->subtotal;
            }
        }
        $transaksi->total_harga = $total_harga;
        $transaksi->kembalian = $request->input('bayar') - $total_harga;
        $transaksi->save();
        DB::commit();

            return redirect('transaksidetail/'.$transaksi->id)->with('pesan', 'Berhasil menambahkan data');
    } catch (\Exception $e) {
        DB::rollback();
        return redirect()->back()->withErrors(['Transaction' => 'Gagal menambahkan data'])->withInput();
    }
}

    public function edit($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        return view('transaksi.edit', compact('transaksi'));
    }

    public function update(Request $request, $id)
    {
        $request->validate(['bayar' => 'required|numeric']);

        $transaksi = Transaksi::findOrFail($id);
        $transaksi->bayar = $request->input('bayar');
        $transaksi->kembalian = $transaksi->bayar - $transaksi->total_harga;
        $transaksi->save();

        return redirect()->route('transaksi.index')->with('pesan', 'Berhasil mengubah data');
    }

    public function destroy($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->delete();

        return redirect()->route('transaksi.index')->with('pesan', 'Berhasil menghapus data');
    }
}
