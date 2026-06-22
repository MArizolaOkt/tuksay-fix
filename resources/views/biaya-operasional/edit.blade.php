{{--
    Perubahan 5 — SKILL.md: Modul Biaya Operasional telah dihapus.
    File edit.blade.php dipertahankan sebagai arsip, route sudah dihapus.
--}}
<x-app-layout>
@section('title', 'Biaya Operasional (Tidak Aktif)')
@section('page-title', 'Biaya Operasional')
@section('page-subtitle', 'Modul ini sudah tidak aktif')

<div class="max-w-lg mx-auto mt-12 text-center">
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-8">
        <h2 class="text-lg font-bold text-amber-900 mb-2">Modul Tidak Aktif</h2>
        <p class="text-sm text-amber-700 mb-6">Modul Biaya Operasional telah dihapus dari sistem.</p>
        <a href="{{ route('dashboard') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-xl transition-colors">
            ← Kembali ke Dashboard
        </a>
    </div>
</div>
</x-app-layout>
