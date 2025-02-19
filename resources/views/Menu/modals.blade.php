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
                <form class="form-horizontal" method="POST" action="{{ url('/supplier') }}">
                    @csrf
                    <div id="method"></div>
                    <div class="form-group row">
                        <label for="nama_makanan">Nama Makanan</label>
                        <input type="text" class="form-control" autocomplete="off" id="nama_makanan"
                            name="nama_makanan" required>
                    </div>
                    <div class="form-group row">
                        <label for="harga">Harga</label>
                        <input type="number" class="form-control" autocomplete="off" id="harga" name="harga"
                            required>
                    </div>
                    <div class="form-group row">
                        <label for="stok">Stok</label>
                        <input type="number" class="form-control" autocomplete="off" id="stok" name="stok"
                            required>
                    </div>
                    <div class="form-group row">
                        <label for="kategori">Kategori</label>
                        <select id="kategori" name="kategori" class="form-control">
                            <option value="makanan" selected>Makanan</option>
                            <option value="minuman">Minuman</option>
                            <option value="snack">Snack</option>
                        </select>
                    </div>
                    <div class="form-group row">
                        <label for="deskripsi">Deskripsi</label>
                        <input type="textarea" class="form-control" autocomplete="off" id="deskripsi" name="deskripsi"
                            required>
                    </div>
                    <div class="form-group row">
                        <label for="gambar">Gambar</label>
                        <input type="text" class="form-control" autocomplete="off" id="gambar" name="gambar"
                            required>
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
