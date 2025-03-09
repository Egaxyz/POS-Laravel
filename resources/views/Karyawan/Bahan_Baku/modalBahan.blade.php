<div class="modal fade" id="formModalBahan" tabindex="-1" role="dialog" aria-labelledby="formModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formModalLabel">Tambah Kebutuhan Bahan Menu</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="bahanForm" action="{{ route('menu.bahan-baku.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="menu_id">Menu</label>
                        <select name="menu_id" id="menu_id" class="form-control" required>
                            <option value="" selected disabled>Silahkan pilih menu</option>
                            @foreach ($menu as $menus)
                                <option value="{{ $menus->id }}">{{ $menus->nama_makanan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="bahan_display">Pilih Bahan</label>
                        <div class="input-group">
                            <input type="text" id="bahan_display" class="form-control"
                                placeholder="Klik untuk memilih bahan" readonly data-toggle="modal"
                                data-target="#bahanModal">
                        </div>
                        <div id="selectedBahanList" class="mt-3"></div>
                        <input type="hidden" name="bahan" id="bahanInput">
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
            </form>
        </div>
    </div>
</div>
</div>

<!-- Modal Pilih Bahan -->
<div class="modal fade" id="bahanModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cari Bahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="text" id="searchBahan" class="form-control mb-3" placeholder="Cari Nama Bahan"
                    onkeyup="filterBahan()">
                <div id="bahanList" class="list-group">
                    @foreach ($bahan as $item)
                        <button type="button" class="list-group-item list-group-item-action"
                            onclick="selectBahan('{{ $item->id }}', '{{ $item->nama }}', '{{ $item->stok }}', '{{ $item->satuan }}')"
                            data-dismiss="modal">
                            <strong>{{ $item->nama }}</strong> - {{ $item->stok }} {{ $item->satuan }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let selectedBahan = [];

    function filterBahan() {
        let input = document.getElementById("searchBahan").value.toLowerCase();
        let items = document.querySelectorAll("#bahanList .list-group-item");
        items.forEach(item => {
            let text = item.textContent.toLowerCase();
            item.style.display = text.includes(input) ? "" : "none";
        });
    }

    function selectBahan(id, nama, stok, satuan) {
        let existingBahan = selectedBahan.find(bahan => bahan.id === id);
        if (!existingBahan) {
            selectedBahan.push({
                id,
                nama,
                stok,
                satuan,
                jumlah: 1
            });
        }
        renderSelectedBahan();
    }

    function updateJumlah(index, value) {
        selectedBahan[index].jumlah = value;
        renderSelectedBahan();

    }

    function hapusBahan(index) {
        selectedBahan.splice(index, 1);
        renderSelectedBahan();
    }

    function renderSelectedBahan() {
        let container = document.getElementById("selectedBahanList");
        container.innerHTML = "";

        selectedBahan.forEach((bahan, index) => {
            let bahanItem = `
            <div class="card mb-2 p-2 shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>${bahan.nama}</strong> - ${bahan.stok} ${bahan.satuan}
                    </div>
                    <button class="text-danger btn btn-sm" onclick="hapusBahan(${index})">×</button>
                </div>
                <div class="form-group mt-2">
                    <label>Jumlah (${bahan.satuan})</label>
                    <input type="number" class="form-control" min="1" value="${bahan.jumlah}" 
                        oninput="updateJumlah(${index}, this.value)">
                </div>
            </div>
        `;
            container.innerHTML += bahanItem;
        });

        // Update hidden input dengan data yang sesuai
        let bahanIds = selectedBahan.map(bahan => bahan.id);
        let jumlahs = selectedBahan.map(bahan => bahan.jumlah);
        document.getElementById("bahanInput").value = JSON.stringify({
            bahan_baku_id: bahanIds,
            jumlah: jumlahs
        });
    }

    document.getElementById("bahanForm").addEventListener("submit", function(e) {
        if (selectedBahan.length === 0) {
            alert("Harap pilih minimal satu bahan sebelum menyimpan.");
            e.preventDefault();
        }
    });
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

    #selectedBahanList {
        max-height: 200px;
        overflow-y: auto;
        border-radius: 5px;
    }

    .btn-sm {
        padding: 3px 8px;
    }

    .list-group-item-action:hover {
        background-color: #007bff;
        color: white;
    }
</style>
