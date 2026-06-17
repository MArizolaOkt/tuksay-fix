<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BarangController extends Controller
{
    public function index(): View
    {
        $barangs = Barang::orderBy('nama')->paginate(20);
        return view('barangs.index', compact('barangs'));
    }

    public function create(): View
    {
        return view('barangs.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama'       => 'required|string|max:255|unique:barangs,nama',
            'satuan'     => 'required|in:kg,ikat,buah,pck',
            'harga_jual' => 'required|numeric|min:0',
        ]);

        $barang = Barang::create($validated);

        return redirect()->route('barangs.index')
            ->with('success', "Barang {$barang->nama} berhasil ditambahkan.");
    }

    public function edit(Barang $barang): View
    {
        return view('barangs.edit', compact('barang'));
    }

    public function update(Request $request, Barang $barang): RedirectResponse
    {
        $validated = $request->validate([
            'nama'       => 'required|string|max:255|unique:barangs,nama,' . $barang->id,
            'satuan'     => 'required|in:kg,ikat,buah,pck',
            'harga_jual' => 'required|numeric|min:0',
        ]);

        $barang->update($validated);

        return redirect()->route('barangs.index')
            ->with('success', 'Data barang berhasil diperbarui.');
    }

    public function destroy(Barang $barang): RedirectResponse
    {
        if ($barang->poItems()->exists()) {
            return back()->with('error', 'Barang tidak dapat dihapus karena memiliki data transaksi.');
        }

        $barang->delete();

        return redirect()->route('barangs.index')
            ->with('success', 'Barang berhasil dihapus.');
    }
}
