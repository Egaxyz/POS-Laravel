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

                    <div id="bahanBakuContainer">
                        <div class="bahan-baku-item">
                            <div class="form-group">
                                <label for="bahan_baku">Bahan Baku</label>
                                <select name="items[0][bahan_baku_id]" class="form-control bahan-baku" required>
                                    <option value="" disabled selected>Pilih Bahan Baku</option>
                                    @foreach ($bahanBaku as $data)
                                        <option value="{{ $data->id }}" data-supplier-id="{{ $data->supplier_id }}"
                                            data-supplier-name="{{ $data->supplier->nama_perusahaan ?? '' }}">
                                            {{ $data->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="supplier">Supplier</label>
                                <input type="text" name="items[0][supplier]" class="form-control supplier" readonly>
                                <input type="hidden" name="supplier_id" class="supplier-id">
                            </div>

                            <div class="form-group">
                                <label for="jumlah">Jumlah</label>
                                <input type="number" class="form-control jumlah" name="items[0][jumlah]" required
                                    min="1">
                            </div>

                            <div class="form-group">
                                <label for="harga_satuan">Harga Satuan</label>
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

        // Event listener untuk update supplier ketika bahan baku dipilih
        document.getElementById("bahanBakuContainer").addEventListener("change", function(event) {
            if (event.target.classList.contains("bahan-baku")) {
                let selectedOption = event.target.options[event.target.selectedIndex];
                let supplierName = selectedOption.getAttribute("data-supplier-name") || "";
                let supplierId = selectedOption.getAttribute("data-supplier-id") || "";

                let itemContainer = event.target.closest(".bahan-baku-item");
                let supplierInput = itemContainer.querySelector(".supplier");
                let supplierIdInput = itemContainer.querySelector(".supplier-id");

                console.log("Bahan Baku Terpilih:", selectedOption.text);
                console.log("Supplier ID:", supplierId);

                supplierInput.value = supplierName;
                supplierIdInput.value = supplierId;

                // Update input hidden utama untuk supplier_id (ambil dari item pertama)
                if (document.querySelectorAll(".bahan-baku-item").length === 1) {
                    document.getElementById("supplier_id").value = supplierId;
                }
            }
        });

        // Event untuk menambah bahan baku baru
        document.getElementById("addBahanBaku").addEventListener("click", function() {
            let container = document.getElementById("bahanBakuContainer");
            let firstItem = document.querySelector(".bahan-baku-item");
            let newItem = firstItem.cloneNode(true);

            newItem.querySelector(".bahan-baku").name = `items[${bahanBakuIndex}][bahan_baku_id]`;
            newItem.querySelector(".supplier-id").name = `items[${bahanBakuIndex}][supplier_id]`;
            newItem.querySelector(".supplier").name = `items[${bahanBakuIndex}][supplier]`;
            newItem.querySelector(".jumlah").name = `items[${bahanBakuIndex}][jumlah]`;
            newItem.querySelector(".harga-satuan").name = `items[${bahanBakuIndex}][harga_satuan]`;

            newItem.querySelector(".bahan-baku").selectedIndex = 0;
            newItem.querySelector(".supplier-id").value = "";
            newItem.querySelector(".supplier").value = "";
            newItem.querySelector(".jumlah").value = "";
            newItem.querySelector(".harga-satuan").value = "";

            newItem.querySelector(".remove-item").style.display = "block";

            container.appendChild(newItem);
            bahanBakuIndex++;
        });

        // Event untuk menghapus bahan baku
        document.getElementById("bahanBakuContainer").addEventListener("click", function(event) {
            if (event.target.classList.contains("remove-item")) {
                event.target.closest(".bahan-baku-item").remove();
                calculateTotalHarga();
            }
        });

        // Event untuk menghitung total harga
        document.addEventListener("input", function() {
            calculateTotalHarga();
        });

        function calculateTotalHarga() {
            let total = 0;
            document.querySelectorAll(".bahan-baku-item").forEach(item => {
                let jumlah = parseFloat(item.querySelector(".jumlah").value) || 0;
                let hargaSatuan = parseFloat(item.querySelector(".harga-satuan").value) || 0;
                total += jumlah * hargaSatuan;
            });
            document.getElementById("total_harga").value = total;
        }

        // Validasi sebelum submit
        document.querySelector("form").addEventListener("submit", function(event) {
            let isValid = true;
            let debugData = [];

            document.querySelectorAll(".bahan-baku-item").forEach(item => {
                let bahanBaku = item.querySelector(".bahan-baku").value;
                let supplierId = item.querySelector(".supplier-id").value;

                debugData.push({
                    bahanBaku,
                    supplierId
                });


            });

            console.log("Debug Data Sebelum Submit:", debugData);

            if (!isValid) {
                event.preventDefault();
            }
        });

    });
</script>
