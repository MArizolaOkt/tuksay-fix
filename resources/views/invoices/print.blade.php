<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->no_invoice }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #1a1a1a; background: white; }
        .page { width: 210mm; min-height: 297mm; margin: 0 auto; padding: 20mm; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; }
        .company-name { font-size: 24px; font-weight: bold; color: #1d5738; }
        .company-sub { font-size: 11px; color: #666; }
        .invoice-title { text-align: right; }
        .invoice-title h1 { font-size: 20px; font-weight: bold; text-transform: uppercase; color: #333; letter-spacing: 2px; }
        .invoice-title .no { font-size: 14px; color: #7c3aed; font-weight: 700; margin-top: 4px; }
        .invoice-title .status { display: inline-block; margin-top: 6px; padding: 3px 10px; border-radius: 20px; font-size: 10px; font-weight: 700; text-transform: uppercase; }
        .status-terbit { background: #fef3c7; color: #92400e; }
        .status-lunas { background: #d1fae5; color: #065f46; }
        .divider { border-top: 2px solid #1d5738; margin: 16px 0; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px; }
        .info-label { font-size: 10px; text-transform: uppercase; color: #888; font-weight: 600; margin-bottom: 4px; letter-spacing: 0.5px; }
        .info-value { font-size: 13px; font-weight: 600; }
        .info-sub { font-size: 11px; color: #555; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        thead tr { background: #1d5738; color: white; }
        th { padding: 9px 12px; text-align: left; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        th.right { text-align: right; }
        tbody tr:nth-child(even) { background: #f8faf9; }
        td { padding: 9px 12px; font-size: 12px; border-bottom: 1px solid #e8e8e8; }
        .td-right { text-align: right; }
        .group-row { background: #ecfdf5 !important; }
        .group-row td { font-weight: 600; color: #065f46; font-size: 11px; padding: 6px 12px; }
        tfoot td { padding: 10px 12px; font-weight: 700; font-size: 13px; }
        .total-row { background: #f0fdf4; }
        .total-amount { color: #7c3aed; font-size: 16px; }
        .footer-info { margin-top: 24px; padding: 16px; background: #f8faf9; border-radius: 8px; border: 1px solid #e8e8e8; }
        .footer-info h4 { font-size: 11px; color: #666; margin-bottom: 8px; text-transform: uppercase; font-weight: 600; }
        .sign-grid { margin-top: 40px; display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        .sign-box { text-align: center; }
        .sign-label { font-size: 11px; color: #555; margin-bottom: 60px; }
        .sign-line { border-top: 1px solid #333; padding-top: 4px; font-size: 11px; }
        .no-print { display: block; }
        @media print {
            .no-print { display: none; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>

<div class="no-print" style="background:#7c3aed; padding:12px 20mm; display:flex; gap:12px; align-items:center;">
    <button onclick="window.print()" style="padding:8px 20px; background:white; color:#7c3aed; border:none; border-radius:8px; font-weight:600; cursor:pointer; font-size:13px;">
        🖨️ Cetak Invoice
    </button>
    <button onclick="window.close()" style="padding:8px 16px; background:rgba(255,255,255,0.2); color:white; border:1px solid rgba(255,255,255,0.4); border-radius:8px; cursor:pointer; font-size:13px;">
        Tutup
    </button>
</div>

<div class="page">
    <div class="header">
        <div>
            <div class="company-name">TUKSAY</div>
            <div class="company-sub">Manajemen Supplier Produk Segar</div>
        </div>
        <div class="invoice-title">
            <h1>Invoice</h1>
            <div class="no">{{ $invoice->no_invoice }}</div>
            <div class="status {{ $invoice->status === 'lunas' ? 'status-lunas' : 'status-terbit' }}">
                {{ strtoupper($invoice->status) }}
            </div>
        </div>
    </div>
    <div class="divider"></div>

    <div class="info-grid">
        <div>
            <div class="info-label">Ditagihkan kepada</div>
            <div class="info-value">{{ $invoice->customer->nama }}</div>
            <div class="info-sub">{{ $invoice->customer->nama_perusahaan }}</div>
            <div class="info-sub" style="margin-top:4px;">{{ $invoice->customer->alamat }}</div>
            <div style="margin-top:8px;">
                <span style="display:inline-block; padding:2px 8px; background:#dbeafe; color:#1d4ed8; border-radius:10px; font-size:10px; font-weight:700;">
                    {{ $invoice->customer->payment_method }}
                </span>
            </div>
        </div>
        <div style="text-align:right;">
            <div class="info-label">Tanggal Invoice</div>
            <div class="info-value">{{ \Carbon\Carbon::parse($invoice->tanggal)->format('d F Y') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:36px;">No</th>
                <th>Deskripsi</th>
                <th class="right" style="width:70px;">Qty</th>
                <th class="right" style="width:50px;">Sat</th>
                <th class="right" style="width:100px;">Harga Satuan</th>
                <th class="right" style="width:110px;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; $grandTotal = 0; @endphp
            @foreach($pos as $po)
                <tr class="group-row">
                    <td colspan="6">{{ $po->no_po }} — {{ $po->outlet->nama_outlet ?? '-' }} ({{ \Carbon\Carbon::parse($po->tanggal)->format('d/m/Y') }})</td>
                </tr>
                @foreach($po->items as $item)
                    @php $subtotal = $item->qty * $item->barang->harga_jual; $grandTotal += $subtotal; @endphp
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $item->barang->nama }}</td>
                        <td class="td-right">{{ number_format($item->qty, 3) }}</td>
                        <td class="td-right">{{ $item->barang->satuan }}</td>
                        <td class="td-right">Rp {{ number_format($item->barang->harga_jual, 0, ',', '.') }}</td>
                        <td class="td-right">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" style="text-align:right; font-weight:700; font-size:13px;">TOTAL TAGIHAN</td>
                <td class="td-right total-amount">Rp {{ number_format($invoice->total_tagihan, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer-info">
        <h4>Informasi Pembayaran</h4>
        <p style="font-size:12px; color:#555;">
            Metode pembayaran: <strong>{{ $invoice->customer->payment_method }}</strong>
            — Mohon cantumkan nomor invoice <strong>{{ $invoice->no_invoice }}</strong> sebagai referensi pembayaran.
        </p>
    </div>

    <div class="sign-grid">
        <div class="sign-box">
            <div class="sign-label">Dikeluarkan oleh,</div>
            <div class="sign-line">( __________________ )<br><small>TUKSAY</small></div>
        </div>
        <div class="sign-box">
            <div class="sign-label">Diterima oleh,</div>
            <div class="sign-line">( __________________ )<br><small>{{ $invoice->customer->nama }}</small></div>
        </div>
    </div>

    <p style="margin-top:24px; font-size:10px; color:#aaa; text-align:center;">
        Invoice dicetak pada {{ now()->format('d/m/Y H:i') }} — TUKSAY ERP
    </p>
</div>
</body>
</html>
