<div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="formModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formModalLabel">Tambah Data Supplier</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="POST" action="{{ url('/supplier') }}">
                    @csrf
                    <div id="method"></div>
                    <div class="form-group row">
                        <label for="nama_perusahaan">Nama Perusahaan</label>
                        <input type="text" class="form-control" autocomplete="off" id="nama_perusahaan"
                            name="nama_perusahaan" required>
                    </div>
                    <div class="form-group row">
                        <label for="kontak">Kontak</label>
                        <input type="text" class="form-control" autocomplete="off" id="kontak" name="kontak"
                            required>
                    </div>
                    <div class="form-group row">
                        <label for="alamat">Alamat</label>
                        <input type="text" class="form-control" autocomplete="off" id="alamat" name="alamat"
                            required>
                    </div>
                    <div class="form-group row">
                        <label for="email">Email</label>
                        <input type="text" class="form-control" autocomplete="off" id="email" name="email"
                            required>
                    </div>
                    <div class="form-group row">
                        <label for="status">Status Supplier</label>
                        <select id="status" name="status" class="form-control">
                            <option value="aktif" selected>Aktif</option>
                            <option value="nonaktif">Tidak Aktif</option>
                        </select>
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
