{{--
    Perubahan 5 — SKILL.md: Modul Biaya Operasional telah dihapus dari sistem.
    File ini dipertahankan sebagai arsip. Route sudah dihapus dari web.php.
    Tabel biaya_operasionals tetap ada di database sebagai arsip data historis.
--}}
<x-app-layout>
@section('title', 'Biaya Operasional (Tidak Aktif)')
@section('page-title', 'Biaya Operasional')
@section('page-subtitle', 'Modul ini sudah tidak aktif')

<div class="max-w-lg mx-auto mt-12 text-center">
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-8">
        <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5C3.58 18.333 4.54 20 6.08 20z"/>
            </svg>
        </div>
        <h2 class="text-lg font-bold text-amber-900 mb-2">Modul Tidak Aktif</h2>
        <p class="text-sm text-amber-700 mb-6">
            Modul Biaya Operasional telah dihapus sesuai kebijakan terbaru.
            Data historis tetap tersimpan di database untuk keperluan audit.
        </p>
        <a href="{{ route('dashboard') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-xl transition-colors">
            ← Kembali ke Dashboard
        </a>
    </div>
</div>
</x-app-layout>
