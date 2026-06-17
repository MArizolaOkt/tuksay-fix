<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan {{ $suratJalan->no_sj }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #1a1a1a; background: white; }
        .page { width: 210mm; min-height: 297mm; margin: 0 auto; padding: 20mm; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; }
        .company-name { font-size: 22px; font-weight: bold; color: #1d5738; letter-spacing: 1px; }
        .company-sub { font-size: 11px; color: #666; margin-top: 2px; }
        .doc-title { text-align: right; }
        .doc-title h1 { font-size: 18px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; color: #333; }
        .doc-title .no { font-size: 13px; color: #1d5738; font-weight: 700; margin-top: 4px; }
        .divider { border-top: 2px solid #1d5738; margin: 16px 0; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px; }
        .info-box { }
        .info-label { font-size: 10px; text-transform: uppercase; color: #888; font-weight: 600; margin-bottom: 6px; }
        .info-value { font-size: 12px; font-weight: 600; color: #1a1a1a; }
        .info-sub { font-size: 11px; color: #555; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        thead tr { background: #1d5738; color: white; }
        th { padding: 8px 10px; text-align: left; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        th:last-child { text-align: right; }
        tbody tr:nth-child(even) { background: #f8f9f7; }
        td { padding: 8px 10px; font-size: 12px; border-bottom: 1px solid #e8e8e8; }
        td:last-child { text-align: right; font-weight: 600; }
        .footer { margin-top: 32px; display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 24px; }
        .sign-box { text-align: center; }
        .sign-label { font-size: 11px; color: #555; margin-bottom: 56px; }
        .sign-line { border-top: 1px solid #333; padding-top: 4px; font-size: 11px; color: #333; }
        .no-print { display: block; }
        @media print {
            .no-print { display: none; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .page { padding: 15mm; }
        }
    </style>
</head>
<body>

{{-- Print/Close buttons (hidden on print) --}}
<div class="no-print" style="background:#1d5738; padding:12px 20mm; display:flex; gap:12px; align-items:center;">
    <button onclick="window.print()" style="padding:8px 20px; background:white; color:#1d5738; border:none; border-radius:8px; font-weight:600; cursor:pointer; font-size:13px;">
        🖨️ Cetak / Print
    </button>
    <button onclick="window.close()" style="padding:8px 16px; background:rgba(255,255,255,0.2); color:white; border:1px solid rgba(255,255,255,0.4); border-radius:8px; cursor:pointer; font-size:13px;">
        Tutup
    </button>
    <span style="color:rgba(255,255,255,0.7); font-size:12px; margin-left:auto;">Format A4 — Surat Jalan</span>
</div>

<div class="page">
    <div class="header">
        <div>
            <div class="company-name">TUKSAY</div>
            <div class="company-sub">Manajemen Supplier Produk Segar</div>
        </div>
        <div class="doc-title">
            <h1>Surat Jalan</h1>
            <div class="no">{{ $suratJalan->no_sj }}</div>
        </div>
    </div>
    <div class="divider"></div>

    <div class="info-grid">
        <div class="info-box">
            <div class="info-label">Dikirim Kepada</div>
            <div class="info-value">{{ $suratJalan->customer->nama }}</div>
            <div class="info-sub">{{ $suratJalan->customer->nama_perusahaan }}</div>
            <div class="info-sub" style="margin-top:4px;">{{ $suratJalan->customer->alamat }}</div>
        </div>
        <div class="info-box" style="text-align:right;">
            <div class="info-label">Outlet Tujuan</div>
            <div class="info-value">{{ $suratJalan->outlet->nama_outlet ?? '-' }}</div>
            <div style="margin-top:12px;">
                <div class="info-label">Tanggal Pengiriman</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($suratJalan->tanggal)->format('d F Y') }}</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:40px;">No</th>
                <th>Nama Produk</th>
                <th style="text-align:right;">Qty</th>
                <th style="text-align:right;">Satuan</th>
                <th style="text-align:right;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($pos as $po)
                @foreach($po->items as $item)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $item->barang->nama }}</td>
                        <td style="text-align:right;">{{ number_format($item->qty, 3) }}</td>
                        <td style="text-align:right;">{{ $item->barang->satuan }}</td>
                        <td style="text-align:right; font-weight:normal; color:#666;">{{ $po->no_po }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div class="sign-box">
            <div class="sign-label">Disiapkan oleh,</div>
            <div class="sign-line">( __________________ )</div>
        </div>
        <div class="sign-box">
            <div class="sign-label">Dikirim oleh,</div>
            <div class="sign-line">( __________________ )</div>
        </div>
        <div class="sign-box">
            <div class="sign-label">Diterima oleh,</div>
            <div class="sign-line">( __________________ )</div>
        </div>
    </div>

    <p style="margin-top:32px; font-size:10px; color:#aaa; text-align:center;">
        Dokumen ini dicetak pada {{ now()->format('d/m/Y H:i') }} — TUKSAY ERP
    </p>
</div>
</body>
</html>
