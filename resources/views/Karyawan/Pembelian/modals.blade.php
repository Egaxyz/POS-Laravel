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
                                <input type="text" class="form-control harga-satuan" name="items[0][harga_satuan]"
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
                        <input type="text" class="form-control" id="total_harga" name="total_harga" readonly>
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

        // Fungsi untuk memformat angka ke format mata uang
        function formatCurrency(value) {
            return "Rp. " + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // Fungsi untuk menghapus format mata uang dan mengembalikan angka murni
        function unformatCurrency(value) {
            return parseFloat(value.replace(/[^0-9]/g, ""));
        }

        // Event listener untuk update supplier ketika bahan baku dipilih
        document.getElementById("bahanBakuContainer").addEventListener("change", function(event) {
            if (event.target.classList.contains("bahan-baku")) {
                let selectedOption = event.target.options[event.target.selectedIndex];
                let supplierName = selectedOption.getAttribute("data-supplier-name") || "";
                let supplierId = selectedOption.getAttribute("data-supplier-id") || "";

                let itemContainer = event.target.closest(".bahan-baku-item");
                let supplierInput = itemContainer.querySelector(".supplier");
                let supplierIdInput = itemContainer.querySelector(".supplier-id");

                supplierInput.value = supplierName;
                supplierIdInput.value = supplierId;
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

        // Event untuk memformat input harga satuan
        document.getElementById("bahanBakuContainer").addEventListener("input", function(event) {
            if (event.target.classList.contains("harga-satuan")) {
                event.target.value = event.target.value.replace(/[^0-9]/g, ""); // Hanya angka
            }
            calculateTotalHarga();
        });


        // Event untuk menghitung total harga
        function calculateTotalHarga() {
            let total = 0;
            document.querySelectorAll(".bahan-baku-item").forEach(item => {
                let jumlah = parseFloat(item.querySelector(".jumlah").value) || 0;
                let hargaSatuan = unformatCurrency(item.querySelector(".harga-satuan").value) || 0;
                total += jumlah * hargaSatuan;
            });
            document.getElementById("total_harga").value = (total);
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

                // Hapus format mata uang sebelum submit
                let hargaSatuanInput = item.querySelector(".harga-satuan");
                hargaSatuanInput.value = hargaSatuanInput.value.replace(/[^0-9]/g,
                    ""); // Hapus semua karakter selain angka


            });

            // Hapus format mata uang dari total harga sebelum submit
            let totalHargaInput = document.getElementById("total_harga");
            totalHargaInput.value = totalHargaInput.value.replace(/[^0-9]/g,
                ""); // Hapus semua karakter selain angka


            console.log("Debug Data Sebelum Submit:", debugData);

            if (!isValid) {
                event.preventDefault();
            }
        });
    });
</script>
