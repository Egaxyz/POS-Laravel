<div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="formModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formModalLabel">Tambah Data Bahan Baku</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="POST" action="{{ url('/bahan-baku') }}">
                    @csrf
                    <div id="method"></div>
                    <div class="form-group row">
                        <label for="supplier_id">Nama Perusahaan</label>
                        <select id="supplier_id" name="supplier_id" class="form-control">
                            <option value="" disabled selected>Pilih Supplier</option>
                            @foreach ($supplier as $data)
                                <option value="{{ $data->id }}"
                                    {{ old('supplier_id', isset($data) ? $data->id : '') == $data->id ? 'selected' : '' }}>
                                    {{ $data->nama_perusahaan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group row">
                        <label for="nama">Nama</label>
                        <input type="text" class="form-control" autocomplete="off" id="nama" name="nama"
                            required>
                    </div>
                    <div class="form-group row">
                        <label for="stok">Stok</label>
                        <input type="number" class="form-control" autocomplete="off" id="stok" name="stok"
                            required>
                    </div>
                    <div class="form-group row">
                        <label for="satuan">Satuan</label>
                        <select id="satuan" name="satuan" class="form-control">
                            <option value="kg" selected>Kg</option>
                            <option value="liter">Liter</option>
                            <option value="gram">Gram</option>
                            <option value="pcs">PCS</option>
                        </select>
                    </div>
                    <div class="form-group row">
                        <label for="harga_satuan">Harga Satuan</label>
                        <input type="number" class="form-control" autocomplete="off" id="harga_satuan"
                            name="harga_satuan" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
