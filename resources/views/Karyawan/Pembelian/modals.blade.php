<div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="formModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formModalLabel">Tambah Pembelian Bahan Baku</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="POST" action="{{ route('pembelian.store') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="supplier_id">Supplier</label>
                        <select id="supplier_id" name="supplier_id" class="form-control" required>
                            <option value="" disabled selected>Pilih Supplier</option>
                            @foreach ($supplier as $sup)
                                <option value="{{ $sup->id }}">{{ $sup->nama_perusahaan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="bahan_baku_id">Bahan Baku</label>
                        <select id="bahan_baku_id" name="items[0][bahan_baku_id]" class="form-control" required>
                            <option value="" disabled selected>Pilih Bahan Baku</option>
                            @foreach ($bahanBaku as $data)
                                <option value="{{ $data->id }}">{{ $data->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="jumlah">Jumlah</label>
                        <input type="number" class="form-control" id="jumlah" name="items[0][jumlah]" required
                            min="1">
                    </div>

                    <div class="form-group">
                        <label for="harga_satuan">Harga Satuan</label>
                        <input type="number" class="form-control" id="harga_satuan" name="items[0][harga_satuan]"
                            required min="0">
                    </div>

                    <div class="form-group">
                        <label for="total_harga">Total Harga</label>
                        <input type="number" class="form-control" id="total_harga" name="total_harga">
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("input", function() {
        let jumlah = document.getElementById("jumlah").value;
        let harga_satuan = document.getElementById("harga_satuan").value;
        let total_harga = jumlah * harga_satuan;
        document.getElementById("total_harga").value = total_harga;
    });
</script>
