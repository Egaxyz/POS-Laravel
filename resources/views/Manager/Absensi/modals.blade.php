<div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="formModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formModalLabel">Tambah Data Absensi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="POST" action="{{ url('/manager/absen') }}">
                    @csrf
                    <div id="method"></div>
                    <div class="form-group">
                        <label for="user_id">Pilih Karyawan</label>
                        <select id="user_id" name="user_id" class="form-control" required>
                            <option value="" disabled selected>Pilih Karyawan</option>
                            @foreach ($akun as $data)
                                <option value="{{ $data->id }}">
                                    {{ $data->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tanggal">Tanggal</label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                    </div>

                    <div class="form-group">
                        <label for="waktu_masuk">Waktu Masuk</label>
                        <input type="time" class="form-control" id="waktu_masuk" name="waktu_masuk">
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="hadir">Hadir</option>
                            <option value="sakit">Sakit</option>
                            <option value="cuti">Cuti</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="waktu_pulang">Waktu Pulang</label>
                        <input type="time" class="form-control" id="waktu_pulang" name="waktu_pulang">
                    </div>

                    <!-- Field Keterangan yang akan muncul hanya untuk sakit/cuti -->
                    <div class="form-group" id="keterangan-group" style="display: none;">
                        <label for="keterangan">Keterangan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
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

@push('script')
    <script>
        $(document).ready(function() {
            // Tampilkan/sembunyikan field keterangan berdasarkan status
            $('#status').change(function() {
                if ($(this).val() === 'sakit' || $(this).val() === 'cuti') {
                    $('#keterangan-group').show();
                    $('#keterangan').prop('required', true);
                } else {
                    $('#keterangan-group').hide();
                    $('#keterangan').prop('required', false);
                }
            });

            // Trigger change event saat modal dibuka untuk edit
            $('#formModal').on('show.bs.modal', function(e) {
                const btn = $(e.relatedTarget);
                const mode = btn.data('mode');
                const status = btn.data('status');

                if (mode === 'edit') {
                    if (status === 'sakit' || status === 'cuti') {
                        $('#keterangan-group').show();
                        $('#keterangan').prop('required', true);
                    } else {
                        $('#keterangan-group').hide();
                        $('#keterangan').prop('required', false);
                    }
                } else {
                    // Mode tambah baru, sembunyikan keterangan
                    $('#keterangan-group').hide();
                    $('#keterangan').prop('required', false);
                }
            });
        });
    </script>
@endpush
