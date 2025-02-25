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
                    <div id="bahanBakuContainer">
                        <div class="bahan-baku-item">
                            <div class="form-group">
                                <label for="bahan_baku_id_0">Bahan Baku</label>
                                <select name="items[0][bahan_baku_id]" class="form-control bahan-baku" required>
                                    <option value="" disabled selected>Pilih Bahan Baku</option>
                                    @foreach ($bahanBaku as $data)
                                        <option value="{{ $data->id }}">{{ $data->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="jumlah_0">Jumlah</label>
                                <input type="number" class="form-control jumlah" name="items[0][jumlah]" required
                                    min="1">
                            </div>
                            <div class="form-group">
                                <label for="harga_satuan_0">Harga Satuan</label>
                                <input type="number" class="form-control harga-satuan" name="items[0][harga_satuan]"
                                    required min="0">
                            </div>
                            <button type="button" class="btn btn-danger remove-item"
                                style="display:none;">Hapus</button>
                            <hr>
                        </div>
                    </div>
                    <button type="button" class="btn btn-success" id="addBahanBaku">Tambah Bahan Baku</button>
                    <div class="form-group mt-3">
                        <label for="total_harga">Total Harga</label>
                        <input type="number" class="form-control" id="total_harga" name="total_harga" readonly>
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
    document.addEventListener("DOMContentLoaded", function() {
        let bahanBakuIndex = 1;

        document.getElementById("addBahanBaku").addEventListener("click", function() {
            let container = document.getElementById("bahanBakuContainer");
            let newItem = document.querySelector(".bahan-baku-item").cloneNode(true);

            newItem.querySelector(".bahan-baku").name = `items[${bahanBakuIndex}][bahan_baku_id]`;
            newItem.querySelector(".jumlah").name = `items[${bahanBakuIndex}][jumlah]`;
            newItem.querySelector(".harga-satuan").name = `items[${bahanBakuIndex}][harga_satuan]`;

            newItem.querySelector(".jumlah").value = "";
            newItem.querySelector(".harga-satuan").value = "";
            newItem.querySelector(".remove-item").style.display = "block";

            container.appendChild(newItem);
            bahanBakuIndex++;
        });

        document.getElementById("bahanBakuContainer").addEventListener("click", function(event) {
            if (event.target.classList.contains("remove-item")) {
                event.target.parentElement.remove();
                calculateTotalHarga();
            }
        });

        document.addEventListener("input", function() {
            calculateTotalHarga();
        });

        function calculateTotalHarga() {
            let total = 0;
            document.querySelectorAll(".bahan-baku-item").forEach(item => {
                let jumlah = item.querySelector(".jumlah").value || 0;
                let hargaSatuan = item.querySelector(".harga-satuan").value || 0;
                total += (jumlah * hargaSatuan);
            });
            document.getElementById("total_harga").value = total;
        }
    });
</script>
