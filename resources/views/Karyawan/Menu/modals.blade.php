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
                <form class="form-horizontal" method="POST" action="{{ url('/menu') }}" enctype="multipart/form-data">
                    @csrf
                    <div id="method"></div>
                    <input type="hidden" name="old_image" id="old_image">
                    <div class="form-group">
                        <label for="nama_makanan">Nama Makanan</label>
                        <input type="text" class="form-control" autocomplete="off" id="nama_makanan"
                            name="nama_makanan" required>
                    </div>
                    <div class="form-group">
                        <label for="harga">Harga</label>
                        <input type="number" class="form-control" autocomplete="off" id="harga" name="harga"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="stok">Stok</label>
                        <input type="number" class="form-control" autocomplete="off" id="stok" name="stok"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="kategori">Kategori</label>
                        <select id="kategori" name="kategori" class="form-control">
                            <option value="makanan" selected>Makanan</option>
                            <option value="minuman">Minuman</option>
                            <option value="snack">Snack</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="deskripsi">Deskripsi</label>
                        <textarea class="form-control" autocomplete="off" id="deskripsi" name="deskripsi" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="gambar">Gambar</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="gambar" name="gambar" required
                                onchange="previewImage()">
                            <label class="custom-file-label" for="gambar">Pilih Gambar</label>
                        </div>
                    </div>
                    <div class="text-center mt-2">
                        <img class="img-preview img-fluid" style="max-height: 200px; display: none;">
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

<!-- JavaScript untuk Menampilkan Nama File & Preview -->
<script>
    document.getElementById("gambar").addEventListener("change", function() {
        if (this.files.length > 0) {
            var fileName = this.files[0].name;
            this.nextElementSibling.innerText = fileName;
        } else {
            this.nextElementSibling.innerText = "Pilih Gambar";
        }
    });

    function previewImage() {
        const gambar = document.querySelector('#gambar');
        const imgPreview = document.querySelector('.img-preview');

        if (gambar.files && gambar.files[0]) {
            const oFReader = new FileReader();
            oFReader.onload = function(oFREvent) {
                imgPreview.style.display = 'block';
                imgPreview.src = oFREvent.target.result;
            };
            oFReader.readAsDataURL(gambar.files[0]);
        } else {
            // Jika tidak ada file yang dipilih, tampilkan gambar lama (jika ada)
            const oldImage = document.querySelector('#old_image').value;
            if (oldImage) {
                imgPreview.style.display = 'block';
                imgPreview.src = '{{ asset('storage/menu-image') }}/' + oldImage;
            } else {
                imgPreview.style.display = 'none';
            }
        }
    }
</script>
