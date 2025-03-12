<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 12px;
        max-width: 250px;
        margin: auto;
        background: white;
    }

    .struk {
        border: 1px solid #000;
        padding: 10px;
        text-align: center;
    }

    h2,
    p {
        margin: 5px 0;
    }

    .item,
    .detail-item {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
    }

    .total {
        font-weight: bold;
        text-align: right;
        border-top: 1px dashed #000;
        margin-top: 5px;
        padding-top: 5px;
    }

    .title {
        font-weight: bold;
        text-align: left;
        border-top: 1px dashed #000;
        margin-top: 5px;
        padding-top: 5px;
    }

    .print-btn {
        display: block;
        margin: 10px auto;
        padding: 5px 10px;
        background: #007bff;
        color: white;
        border: none;
        cursor: pointer;
    }

    hr {
        border: none;
        border-top: 1px dashed #000;
        margin: 5px 0;
    }
</style>

<div class="struk">
    <h2>🍽️ RestoPos 🍽️</h2>
    <p>{{ $lokasi }}</p>
    <p>{{ $penjualan->no_faktur }}</p>
    <hr>

    <!-- Detail Payment -->
    @foreach ($penjualan->details as $detail)
        <div class="item">
            <span>{{ $detail->menu->nama_makanan }}</span>
            <span>{{ $detail->jumlah }} x {{ number_format($detail->harga_satuan, 0, ',', '.') }}</span>
        </div>
    @endforeach
    <hr>

    <p class="title">Detail Pembayaran</p>

    <div class="detail-item">
        <span>Subtotal:</span>
        <span>Rp {{ number_format($penjualan->total_harga, 0, ',', '.') }}</span>
    </div>

    <div class="detail-item">
        <span>Diskon:</span>
        <span>{{ $penjualan->diskon ? 'Rp ' . number_format($penjualan->diskon, 0, ',', '.') : '-' }}</span>
    </div>

    <div class="detail-item">
        <span>Pajak (10%):</span>
        <span>Rp {{ number_format(($penjualan->total_harga - $penjualan->diskon) * 0.1, 0, ',', '.') }}</span>
    </div>

    <div class="detail-item total">
        <span>Metode Pembayaran:</span>
        <span>{{ $penjualan->metode_pembayaran }}</span>
    </div>

    <div class="detail-item total">
        <span>Total:</span>
        <span>Rp {{ number_format(($penjualan->total_harga - $penjualan->diskon) * 1.1, 0, ',', '.') }}</span>
    </div>

    @if ($penjualan->metode_pembayaran === 'Cash')
        <div class="detail-item">
            <span>Tunai:</span>
            <span>Rp {{ number_format($uangDiberikan, 0, ',', '.') }}</span>
        </div>

        <div class="detail-item total">
            <span>Kembalian:</span>
            <span>Rp {{ number_format($kembalian, 0, ',', '.') }}</span>
        </div>
    @endif

    <hr>

    <p>{{ $penjualan->tanggal_formatted }}</p>
    <hr>
    <p>😊 Terima Kasih Telah Berbelanja! 😊</p>
</div>

<button class="print-btn" onclick="window.print()">Cetak Struk</button>
