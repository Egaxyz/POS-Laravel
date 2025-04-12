<!-- Modal Form -->
<div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="formModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formModalLabel">Tambah Data Penjualan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('penjualan.store') }}">
                    @csrf
                    <input type="hidden" name="menus" id="menus">
                    <div class="form-group">
                        <label for="menu_display">Pilih Menu</label>
                        <div class="input-group">
                            <input type="text" id="menu_display" class="form-control"
                                placeholder="Klik untuk memilih menu" readonly data-toggle="modal"
                                data-target="#menuModal">
                        </div>
                        <div id="selectedMenuList" class="mt-3"></div>
                    </div>

                    <div class="form-group">
                        <label for="metode_pembayaran">Metode Pembayaran</label>
                        <select id="metode_pembayaran" name="metode_pembayaran" class="form-control" required>
                            <option value="" selected disabled>Pilih Metode Pembayaran</option>
                            <option value="Cash">Cash</option>
                            <option value="Qris">Qris</option>
                            <option value="Bank">Bank</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="total_harga">Total Harga</label>
                        <input type="text" class="form-control" id="total_harga_display" readonly>
                        <input type="hidden" class="form-control" id="total_harga" name="total_harga" readonly>
                    </div>
                    <div class="form-group" id="uang_diberikan_group" style="display: none;">
                        <label for="uang_diberikan">Uang Diberikan</label>
                        <input type="text" class="form-control" id="uang_diberikan"
                            placeholder="Masukkan jumlah uang" autocomplete="off">
                    </div>

                    <div class="form-group" id="kembalian_group" style="display: none;">
                        <label for="kembalian">Kembalian</label>
                        <input type="text" class="form-control" id="kembalian" readonly>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <button type="button" class="btn btn-warning" onclick="cetakStruk()">
                            <i class="fas fa-receipt"></i> Cetak Struk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pilih Menu -->
<div class="modal fade" id="menuModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cari Menu</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="text" id="searchMenu" class="form-control mb-3"
                    placeholder="Cari Nama Menu atau Kategori" onkeyup="filterMenu()">
                <div id="menuList" class="list-group">
                    @foreach ($menu as $item)
                        <button type="button" class="list-group-item list-group-item-action mb-2"
                            onclick="selectMenu('{{ $item->nama_makanan }}', '{{ $item->kategori }}', '{{ $item->harga }}', '{{ $item->stok }}')"
                            {{ $item->stok < 1 ? 'disabled' : '' }}>
                            <strong>{{ $item->nama_makanan }}</strong> - {{ $item->kategori }} Stok:
                            {{ $item->stok }}
                            <span class="float-right badge {{ $item->stok < 1 ? 'badge-danger' : 'badge-success' }}">
                                {{ $item->stok < 1 ? 'Habis' : 'Tersedia' }}
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let selectedMenus = [];

    function filterMenu() {
        let input = document.getElementById("searchMenu").value.toLowerCase();
        let items = document.querySelectorAll("#menuList .list-group-item");
        items.forEach(item => {
            let text = item.textContent.toLowerCase();
            item.style.display = text.includes(input) ? "" : "none";
        });
    }

    function selectMenu(nama_makanan, kategori, harga, stok) {
        if (stok < 1) {
            alert("Menu ini sudah habis!");
            return;
        }

        let existingMenu = selectedMenus.find(menu => menu.nama_makanan === nama_makanan);
        if (existingMenu) {
            if (existingMenu.jumlah < stok) {
                existingMenu.jumlah += 1;
            } else {
                alert("Jumlah pesanan melebihi stok tersedia!");
            }
        } else {
            selectedMenus.push({
                nama_makanan,
                kategori,
                harga,
                jumlah: 1,
                stok
            });
        }
        renderSelectedMenus();
    }

    document.getElementById("metode_pembayaran").addEventListener("change", function() {
        let pembayaran = this.value;
        let uangDiberikanGroup = document.getElementById("uang_diberikan_group");
        let kembalianGroup = document.getElementById("kembalian_group");

        if (pembayaran === "Cash") {
            uangDiberikanGroup.style.display = "block";
            kembalianGroup.style.display = "block";
        } else {
            uangDiberikanGroup.style.display = "none";
            kembalianGroup.style.display = "none";
            document.getElementById("uang_diberikan").value = "";
            document.getElementById("kembalian").value = "";
        }
    });

    document.getElementById("uang_diberikan").addEventListener("input", function() {
        let value = this.value.replace(/\D/g, ""); // Hanya angka
        let formattedValue = new Intl.NumberFormat("id-ID").format(value);
        this.value = value ? `Rp ${formattedValue}` : "";

        hitungKembalian();
    });

    function hitungKembalian() {
        let totalHarga = parseInt(document.getElementById("total_harga").value) || 0;
        let uangDiberikan = parseInt(document.getElementById("uang_diberikan").value.replace(/\D/g, "")) || 0;

        let kembalian = uangDiberikan - totalHarga;
        document.getElementById("kembalian").value = kembalian >= 0 ? `Rp ${kembalian.toLocaleString("id-ID")}` :
            "Uang kurang!";
    }

    function hitungKembalian() {
        let totalHarga = parseInt(document.getElementById("total_harga").value) || 0;
        let uangDiberikan = parseInt(document.getElementById("uang_diberikan").value.replace(/\D/g, "")) || 0;

        let kembalian = uangDiberikan - totalHarga;
        document.getElementById("kembalian").value = kembalian >= 0 ? `Rp ${kembalian.toLocaleString("id-ID")}` :
            "Uang kurang!";
    }

    function formatRupiah(angka) {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(angka);
    }

    function cetakStruk() {
        let totalHarga = parseInt(document.getElementById("total_harga").value) || 0;
        let uangDiberikan = parseInt(document.getElementById("uang_diberikan").value.replace(/\D/g, "")) || 0;
        let kembalian = uangDiberikan - totalHarga;
        let metodePembayaran = document.getElementById("metode_pembayaran").value;

        // Kirim data menu yang dipilih sebagai query parameter
        let selectedMenusEncoded = encodeURIComponent(JSON.stringify(selectedMenus));

        // Redirect to the struk page with query parameters
        let url =
            `/karyawan/penjualan/struk?total_harga=${totalHarga}&uang_diberikan=${uangDiberikan}&kembalian=${kembalian}&metode_pembayaran=${metodePembayaran}&menus=${selectedMenusEncoded}`;
        window.open(url, '_blank');
    }


    function ubahJumlah(index, value) {
        selectedMenus[index].jumlah += value;
        if (selectedMenus[index].jumlah < 1) {
            selectedMenus[index].jumlah = 1;
        }
        renderSelectedMenus();
    }

    function hapusMenu(index) {
        selectedMenus.splice(index, 1);
        renderSelectedMenus();
    }

    function renderSelectedMenus() {
        let container = document.getElementById("selectedMenuList");
        container.innerHTML = "";
        let totalHarga = 0;

        selectedMenus.forEach((menu, index) => {
            let total = menu.harga * menu.jumlah;
            totalHarga += total;

            let menuItem = `
        <div class="card mb-2 p-2 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong>${menu.nama_makanan}</strong>
                    <p class="mb-0">${formatRupiah(menu.harga)} x ${menu.jumlah} = <strong>${formatRupiah(total)}</strong></p>
                </div>
                <div>
                    <button class="btn btn-sm btn-outline-secondary" onclick="ubahJumlah(${index}, -1)">-</button>
                    <span class="mx-2">${menu.jumlah}</span>
                    <button class="btn btn-sm btn-outline-secondary" onclick="ubahJumlah(${index}, 1)">+</button>
                    <button class="btn btn-sm btn-danger ml-2" onclick="hapusMenu(${index})">×</button>
                </div>
            </div>
        </div>
        `;
            container.innerHTML += menuItem;
        });

        // Format totalHarga ke dalam Rupiah
        document.getElementById("total_harga").value = totalHarga;
        document.getElementById("total_harga_display").value = formatRupiah(totalHarga);

        // Ensure the menus field is set before form submission
        document.getElementById("menus").value = JSON.stringify(selectedMenus);

        document.querySelector("form").addEventListener("submit", function(e) {
            if (selectedMenus.length === 0) {
                alert("Harap pilih minimal satu menu sebelum menyimpan.");
                e.preventDefault(); // Mencegah form dikirim
                return;
            }
        });
    }
</script>

<style>
    .modal-dialog {
        max-height: 90vh;
        display: flex;
        flex-direction: column;
    }

    .modal-content {
        max-height: 90vh;
        overflow-y: auto;
    }

    #selectedMenuList {
        max-height: 200px;
        overflow-y: auto;
        border-radius: 5px;
    }

    .list-group-item {
        margin-bottom: 10px;
        /* Jarak antar item menu */
    }

    .card {
        border-radius: 8px;
        border: 1px solid #ddd;
        padding: 10px;
    }

    .list-group-item-action:hover {
        background-color: #007bff;
        color: white;
    }
</style>
