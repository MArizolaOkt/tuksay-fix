<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->no_invoice }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #1a1a1a;
            background: white;
        }

        /* ─── Print Bar (no-print) ─── */
        .print-bar {
            background: #2d6a4f;
            padding: 10px 20px;
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .print-bar button {
            padding: 7px 18px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            font-size: 12px;
            border: none;
        }
        .btn-print { background: white; color: #2d6a4f; }
        .btn-close  { background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.4) !important; }

        /* ─── Page ─── */
        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 14mm 16mm 16mm 16mm;
            background: white;
        }

        /* ─── Header ─── */
        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
        }

        .company-block .company-name {
            font-size: 15px;
            font-weight: 900;
            color: #1a1a1a;
            letter-spacing: 0.5px;
        }
        .company-block .company-addr {
            font-size: 9.5px;
            color: #333;
            line-height: 1.5;
            margin-top: 3px;
        }

        .invoice-center {
            text-align: center;
            flex: 1;
            padding-top: 2px;
        }
        .invoice-center h1 {
            font-size: 20px;
            font-weight: 900;
            letter-spacing: 6px;
            text-transform: uppercase;
            color: #111;
        }
        .invoice-center .inv-slash {
            font-size: 13px;
            color: #555;
            margin-top: 2px;
            letter-spacing: 1px;
        }

        /* Logo TUKSAY (styled text) */
        .logo-block {
            text-align: right;
        }
        .logo-tuksay {
            display: inline-block;
            background: #fff;
            border: 2px solid #2d6a4f;
            border-radius: 8px;
            padding: 5px 12px 5px 10px;
            text-align: center;
        }
        .logo-tuksay .logo-name {
            font-size: 22px;
            font-weight: 900;
            color: #2d6a4f;
            letter-spacing: 2px;
            line-height: 1;
        }
        .logo-tuksay .logo-tagline {
            font-size: 7px;
            color: #2d6a4f;
            letter-spacing: 0.5px;
            margin-top: 2px;
            font-style: italic;
        }

        /* ─── Info Box (border) ─── */
        .info-box {
            border: 1px solid #999;
            display: flex;
            justify-content: space-between;
            padding: 9px 12px;
            margin: 10px 0;
            gap: 16px;
        }
        .info-box .recipient .recipient-label {
            font-size: 9px;
            color: #555;
            margin-bottom: 3px;
        }
        .info-box .recipient .recipient-name {
            font-size: 11.5px;
            font-weight: 700;
        }
        .info-box .recipient .recipient-addr {
            font-size: 9.5px;
            color: #333;
            line-height: 1.55;
            margin-top: 2px;
        }

        .info-box .meta-table {
            font-size: 10.5px;
            border-collapse: collapse;
            min-width: 200px;
            align-self: flex-start;
            flex-shrink: 0;
        }
        .info-box .meta-table td {
            padding: 1.5px 0;
            vertical-align: top;
        }
        .info-box .meta-table td.label {
            white-space: nowrap;
            color: #444;
            width: 80px;
        }
        .info-box .meta-table td.sep {
            padding: 1.5px 6px;
            color: #444;
        }
        .info-box .meta-table td.value {
            font-weight: 600;
        }

        /* ─── Items Table ─── */
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
            font-size: 10.5px;
        }
        table.items thead tr {
            background: #c8c8c8;
        }
        table.items thead th {
            padding: 6px 7px;
            font-weight: 700;
            font-size: 10px;
            text-align: center;
            border: 1px solid #999;
        }
        table.items thead th.left { text-align: left; }
        table.items thead th.right { text-align: right; }

        table.items tbody td {
            padding: 5px 7px;
            border: 1px solid #ccc;
            vertical-align: top;
        }
        table.items tbody td.center { text-align: center; }
        table.items tbody td.right  { text-align: right; }

        /* PO separator row */
        table.items tbody tr.po-row td {
            background: #f0f0f0;
            font-size: 9.5px;
            font-weight: 700;
            color: #2d6a4f;
            padding: 3px 7px;
            border: 1px solid #bbb;
        }

        /* Summary rows (subtotal, ppn, grand total) */
        table.items tfoot td {
            padding: 5px 7px;
            border: 1px solid #ccc;
            font-size: 10.5px;
        }
        table.items tfoot tr.summary-row td.label {
            text-align: right;
            font-weight: 600;
        }
        table.items tfoot tr.summary-row td.value {
            text-align: right;
            min-width: 90px;
        }
        table.items tfoot tr.grand-total td {
            font-weight: 700;
            font-size: 11px;
            background: #f5f5f5;
        }
        table.items tfoot tr.grand-total td.label {
            text-align: right;
        }
        table.items tfoot tr.grand-total td.value {
            text-align: right;
        }

        /* ─── Note / Bank ─── */
        .note-section {
            margin-top: 14px;
            font-size: 10.5px;
        }
        .note-section .note-label {
            font-weight: 700;
            margin-bottom: 2px;
        }
        .note-section .bank-info {
            font-weight: 700;
            font-size: 11px;
        }

        /* ─── Signature ─── */
        .sign-row {
            display: flex;
            justify-content: space-between;
            margin-top: 36px;
            gap: 24px;
        }
        .sign-col {
            flex: 1;
            font-size: 10.5px;
        }
        .sign-col .sign-title {
            margin-bottom: 64px;
        }
        .sign-col .sign-line {
            border-top: 1px solid #555;
            padding-top: 4px;
            display: inline-block;
            min-width: 160px;
        }

        /* Print */
        @media print {
            .print-bar { display: none !important; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .page { margin: 0; padding: 12mm 14mm; }
        }
    </style>
</head>
<body>

{{-- Print Bar (hidden on print) --}}
<div class="print-bar">
    <button class="btn-print" onclick="window.print()">🖨️ Cetak Invoice</button>
    <button class="btn-close" onclick="window.history.back()">← Kembali</button>
</div>

<div class="page">

    {{-- ─── HEADER ─── --}}
    <div class="header-row">
        {{-- Kiri: Info Perusahaan --}}
        <div class="company-block">
            <div class="company-name">TUKSAY</div>
            <div class="company-addr">
                Kalia Residence 3 No. A1<br>
                Jl. Rawa Kopi RT 06 RW 04, Pangkalan Jati Baru<br>
                Cinere, Depok 16513 &nbsp;|&nbsp; Phone : 081290126525
            </div>
        </div>

        {{-- Tengah: Judul Invoice --}}
        <div class="invoice-center">
            <h1>INVOICE</h1>
            <div class="inv-slash">{{ $invoice->no_invoice }}</div>
        </div>

        {{-- Kanan: Logo TUKSAY --}}
        <div class="logo-block">
            <div class="logo-tuksay">
                <div class="logo-name">TUKSAY</div>
                <div class="logo-tagline">sayur &amp; buah berkualitas</div>
            </div>
        </div>
    </div>

    {{-- ─── INFO BOX ─── --}}
    <div class="info-box">
        {{-- Kiri: Kepada --}}
        <div class="recipient">
            <div class="recipient-label">Kepada:</div>
            <div class="recipient-name">{{ strtoupper($invoice->customer->nama) }}</div>
            @if($invoice->customer->nama_perusahaan)
                <div class="recipient-addr">{{ $invoice->customer->nama_perusahaan }}</div>
            @endif
            @if($invoice->customer->alamat)
                <div class="recipient-addr">{!! nl2br(e($invoice->customer->alamat)) !!}</div>
            @endif
        </div>

        {{-- Kanan: Meta Info --}}
        <table class="meta-table">
            <tr>
                <td class="label">Tanggal</td>
                <td class="sep">:</td>
                <td class="value">{{ \Carbon\Carbon::parse($invoice->tanggal)->translatedFormat('d F Y') }}</td>
            </tr>
            @php
                $firstPo = $pos->first();
            @endphp
            @if($firstPo)
            <tr>
                <td class="label">Nomor PO</td>
                <td class="sep">:</td>
                <td class="value">{{ $firstPo->no_po }}{{ $pos->count() > 1 ? ', ...' : '' }}</td>
            </tr>
            @endif
            <tr>
                <td class="label">Payment</td>
                <td class="sep">:</td>
                <td class="value">{{ strtoupper($invoice->customer->payment_method ?? '-') }}</td>
            </tr>
        </table>
    </div>

    {{-- ─── ITEMS TABLE ─── --}}
    @php
        $no = 1;
        $grandTotal = 0;
    @endphp

    <table class="items">
        <thead>
            <tr>
                <th style="width:28px;">No</th>
                <th style="width:40px;">QTY</th>
                <th style="width:36px;">Satuan</th>
                <th class="left">Nama Barang</th>
                <th class="right" style="width:90px;">Harga Satuan</th>
                <th class="right" style="width:90px;">Harga Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pos as $po)
                {{-- Baris pemisah per PO --}}
                @if($pos->count() > 1)
                <tr class="po-row">
                    <td colspan="6">{{ $po->no_po }} — {{ $po->outlet->nama_outlet ?? $invoice->customer->nama }} ({{ \Carbon\Carbon::parse($po->tanggal)->format('d/m/Y') }})</td>
                </tr>
                @endif

                @foreach($po->items as $item)
                    @php
                        $subtotal = $item->qty * $item->harga_jual;
                        $grandTotal += $subtotal;
                    @endphp
                    <tr>
                        <td class="center">{{ $no++ }}</td>
                        <td class="center">
                            {{-- Format qty: hilangkan trailing zeros --}}
                            {{ rtrim(rtrim(number_format($item->qty, 3, ',', '.'), '0'), ',') }}
                        </td>
                        <td class="center">{{ $item->barang->satuan ?? '-' }}</td>
                        <td>{{ $item->barang->nama }}</td>
                        <td class="right">Rp &nbsp;{{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                        <td class="right">Rp &nbsp;{{ number_format($subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
        <tfoot>
            <tr class="summary-row">
                <td colspan="5" class="label">Sub Total</td>
                <td class="value">Rp &nbsp;{{ number_format($grandTotal, 0, ',', '.') }}</td>
            </tr>
            <tr class="summary-row">
                <td colspan="5" class="label">Ppn11%</td>
                <td class="value" style="text-align:right; color:#888;">-</td>
            </tr>
            <tr class="grand-total">
                <td colspan="5" class="label">Grand Total</td>
                <td class="value">Rp &nbsp;{{ number_format($grandTotal, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- ─── NOTE / BANK ─── --}}
    <div class="note-section" style="margin-top:12px;">
        <div class="note-label">Note :</div>
        <div class="bank-info">BCA &nbsp;2670285378 &nbsp;a/n Wirawan Putra Haryatama</div>
    </div>

    {{-- ─── TANDA TANGAN ─── --}}
    <div class="sign-row">
        <div class="sign-col">
            <div class="sign-title">Diterima Oleh,</div>
            <div class="sign-line">( ....................................... )</div>
        </div>
        <div class="sign-col" style="text-align:right;">
            <div class="sign-title">Hormat Kami,<br><strong>TUKSAY</strong></div>
            <div style="display:inline-block; text-align:center;">
                {{-- Placeholder cap/stempel --}}
                <div style="width:90px; height:90px; border:2px dashed #2d6a4f; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 6px; color:#2d6a4f; font-size:9px; font-weight:700; line-height:1.3; text-align:center;">
                    CAP<br>TUKSAY
                </div>
            </div>
        </div>
    </div>

</div>{{-- /page --}}

</body>
</html>
