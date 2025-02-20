<div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="formModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formModalLabel">Tambah Data Pegawai</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="POST" action="{{ url('/user') }}">
                    @csrf
                    <div id="method"></div>
                    <div class="form-group row">
                        <label for="nama">Nama Karyawan</label>
                        <input type="text" class="form-control" autocomplete="off" id="nama" name="nama"
                            required>
                    </div>
                    <div class="form-group row" id="passwordField">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" autocomplete="off" id="password" name="password"
                            required>
                    </div>
                    <div class="form-group row">
                        <label for="role">Role</label>
                        <select id="role" name="role" class="form-control">
                            <option value="manager">Manager</option>
                            <option value="karyawan">Karyawan</option>
                            <option value="superuser">SuperUser</option>
                        </select>

                    </div>
                    <div class="form-group row">
                        <label for="status">Status Akun</label>
                        <select id="status" name="status" class="form-control">
                            <option value="aktif" selected>Aktif</option>
                            <option value="nonaktif">Tidak Aktif</option>
                        </select>
                    </div>
                    <div class="form-group row">
                        <label for="no_hp">No Handphone</label>
                        <input type="number" class="form-control" autocomplete="off" id="no_hp" name="no_hp"
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
