<?php

namespace App\Http\Controllers;

use App\Models\BiayaOperasional;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BiayaOperasionalController extends Controller
{
    public function index(): View
    {
        $biayaList = BiayaOperasional::orderByDesc('tanggal')->paginate(20);

        $totalBulanIni = BiayaOperasional::whereYear('tanggal', now()->year)
            ->whereMonth('tanggal', now()->month)
            ->sum('jumlah');

        return view('biaya-operasional.index', compact('biayaList', 'totalBulanIni'));
    }

    public function create(): View
    {
        $kategoriList = ['Transport', 'Packaging', 'Komunikasi', 'Tak Terduga', 'Lain-lain'];
        return view('biaya-operasional.create', compact('kategoriList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_biaya' => 'required|string|max:255',
            'kategori'   => 'required|in:Transport,Packaging,Komunikasi,Tak Terduga,Lain-lain',
            'jumlah'     => 'required|numeric|min:0',
            'tanggal'    => 'required|date',
        ]);

        BiayaOperasional::create($validated);

        return redirect()->route('biaya-operasional.index')
            ->with('success', 'Biaya operasional berhasil dicatat.');
    }

    public function edit(BiayaOperasional $biayaOperasional): View
    {
        $kategoriList = ['Transport', 'Packaging', 'Komunikasi', 'Tak Terduga', 'Lain-lain'];
        return view('biaya-operasional.edit', compact('biayaOperasional', 'kategoriList'));
    }

    public function update(Request $request, BiayaOperasional $biayaOperasional): RedirectResponse
    {
        $validated = $request->validate([
            'nama_biaya' => 'required|string|max:255',
            'kategori'   => 'required|in:Transport,Packaging,Komunikasi,Tak Terduga,Lain-lain',
            'jumlah'     => 'required|numeric|min:0',
            'tanggal'    => 'required|date',
        ]);

        $biayaOperasional->update($validated);

        return redirect()->route('biaya-operasional.index')
            ->with('success', 'Data biaya operasional berhasil diperbarui.');
    }

    public function destroy(BiayaOperasional $biayaOperasional): RedirectResponse
    {
        $biayaOperasional->delete();

        return redirect()->route('biaya-operasional.index')
            ->with('success', 'Biaya operasional berhasil dihapus.');
    }
}
