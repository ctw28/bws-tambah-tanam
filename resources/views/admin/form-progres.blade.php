@extends('admin.template')

@section('content')
<div id="app" v-cloak class="container mt-4">
    <div class="card h-100">
        <div class="card-body">
            <h5 class="card-title">Progres Formulir Pengisian</h5>
            <!-- Filter tanggal -->
            <div class="mb-3">
                <div class="row g-2">
                    <div class="col-6 col-md-3">
                        <select class="form-select" v-model="filterDI">
                            <option value="">--Pilih DI--</option>
                            <option v-for="d in daerahIrigasis" :value="d.id">@{{ d.nama }}</option>
                        </select>
                    </div>
                    <!-- Input tanggal awal -->
                    <div class="col-6 col-md-3">
                        <input type="date" v-model="filterTanggalPantau" class="form-control" />
                        <!-- <input type="date" v-model="filterTanggalPantau" @change="syncTanggal" class="form-control" /> -->

                    </div>
                    <!-- Tombol (hanya di layar md ke atas) -->
                    <div class="col-md-3 col-12 d-none d-md-flex gap-2">
                        <button class="btn btn-primary btn-sm" @click="applyFilter">Filter</button>
                        <button class="btn btn-secondary btn-sm" @click="resetFilter">Reset</button>
                    </div>
                </div>

                <!-- Tombol (khusus HP, tampil di bawah input tanggal) -->
                <div class="d-flex gap-2 mt-2 d-md-none">
                    <button class="btn btn-primary " @click="applyFilter">Filter</button>
                    <button class="btn btn-secondary btn-sm" @click="resetFilter">Reset</button>
                </div>
            </div>
            <div class="table-responsive">

                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th rowspan="2">No</th>
                            <th rowspan="2">Tanggal Pantau</th>
                            <th rowspan="2">Lokasi</th>
                            <th rowspan="2">Petugas</th>
                            <th rowspan="2">Saluran / Bangunan / Petak</th>
                            <th rowspan="2">Debit Air</th>
                            <th rowspan="2">Masa Tanam</th>
                            <!-- <th rowspan="2">Luas</th> -->
                            <th colspan="2" class="text-center">Validasi</th>
                            <th rowspan="2">Aksi</th>
                        </tr>
                        <tr>
                            <th>Pengamat</th>
                            <th>UPI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in filteredItems" :key="item.id">
                            <td>@{{ index + 1 }}</td>
                            <td>@{{ formatTanggal(item.tanggal_pantau) }}</td>
                            <td>
                                @{{ item.kabupaten?.nama ?? '-' }}<br>
                                <span class="badge bg-success">@{{ item.daerah_irigasi?.nama ?? '-' }}</span>
                            </td>
                            <td>@{{ item.petugas?.nama ?? '-' }}</td>
                            <td>
                                @{{ item.saluran?.nama ?? '-' }} /
                                @{{ item.bangunan?.nama ?? '-' }} /
                                @{{ item.petak?.nama ?? '-' }}
                            </td>
                            <td>@{{ item.debit_air }}</td>
                            <td>@{{ item.masa_tanam }}</td>

                            <!-- Validasi Pengamat -->
                            <td>
                                <span v-if="item.validasi?.pengamat_valid == 1" class="badge bg-success">✔ Valid</span>
                                <span v-else class="badge bg-danger">✘ Belum</span>
                            </td>

                            <!-- Validasi UPI -->
                            <td>
                                <span v-if="item.validasi?.upi_valid == 1" class="badge bg-success">✔ Valid</span>
                                <span v-else class="badge bg-danger">✘ Belum</span>
                            </td>

                            <td>
                                <button @click="showForm(item)" class="btn btn-sm btn-info">Detail</button>
                            </td>
                        </tr>
                    </tbody>
                </table>

            </div>

        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="formLTTModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Form Pemantauan LTT</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <!-- Header -->
                    <h4 class="text-center mb-3">FORM PEMANTAUAN LUAS TAMBAH TANAM (LTT)</h4>

                    <!-- Identitas -->
                    <div class="mb-3">
                        <div class="form-line"><span>Nama Petugas OP</span>:
                            @{{ item.petugas ? item.petugas.nama : '-' }}</div>
                        <div class="form-line"><span>Tanggal Pemantauan</span>:
                            @{{ formatTanggal(item.tanggal_pantau) }}</div>
                        <div class="form-line"><span>Daerah Irigasi</span>: DI
                            @{{ item.daerah_irigasi ? item.daerah_irigasi.nama : '-' }}</div>
                        <div class="form-line"><span>Desa/Kelurahan</span>: @{{item.desa}}</div>
                        <div class="form-line"><span>Kecamatan</span>: @{{item.kecamatan}}</div>
                        <div class="form-line"><span>Kabupaten/Kota</span>:
                            @{{item.kabupaten ? item.kabupaten.nama : '-'}}
                        </div>
                        <div class="form-line"><span>Nama Saluran (Sekunder/Primer)</span>:
                            @{{item.saluran ? item.saluran.nama : '-'}}</div>
                        <div class="form-line"><span>Nama Bangunan Bagi/Sadap</span>:
                            @{{item.bangunan ? item.bangunan.nama : '-'}}</div>
                        <div class="form-line"><span>Kode/Nama Petak Layanan</span>:
                            @{{item.petak ? item.petak.nama : '-'}}
                        </div>
                        <div class="form-line"><span>Koordinat Bangunan Bagi/Sadap</span>: @{{item.koordinat}}
                        </div>
                    </div>

                    <!-- Tabel -->
                    <div class="table-responsive">

                        <table class="table table-bordered text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th rowspan="2">Debit Air (lt/detik)</th>
                                    <th rowspan="2">Luas Petak Skema (Ha)</th>
                                    <th colspan="3">Pemantauan Luas Tambah Tanam (LTT)</th>
                                </tr>
                                <tr>
                                    <th>Padi (Ha)</th>
                                    <th>Palawija (Ha)</th>
                                    <th>Lainnya (Ha)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td height="50px">@{{item.debit_air}}</td>
                                    <td>@{{item.petak ? item.petak.luas : '-'}}</td>
                                    <td>@{{item.luas_padi}}</td>
                                    <td>@{{item.luas_palawija}}</td>
                                    <td>@{{item.luas_lainnya}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="table-responsive">

                        <!-- Tabel -->
                        <table class="table table-bordered text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Pemantauan Permasalahan</th>
                                    <th>Ada/Tidak</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody v-for="(p,index) in item.permasalahan" :key="p.id">
                                <tr>
                                    <td>@{{ index+1}}</td>
                                    <td>@{{ p.master_permasalahan.nama }}
                                    </td>
                                    <td class="text-center">
                                        <span v-if="p.status==1">Ada</span>
                                        <span v-else="p.status==0">Tidak</span>
                                    </td>
                                    <td>@{{ p.keterangan}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <h4>Foto Pemantauan</h4>
                    <img v-if="modalInstance" :src="`/storage/${item.foto_pemantauan}`" width="100%">

                </div>

                <div class="modal-footer">
                    <button class="btn btn-danger me-auto" @click="hapus(item.id)"><i class="menu-icon tf-icons bx bx-trash"></i> Hapus</button>
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')

<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script>
    const {
        createApp
    } = Vue;

    createApp({
        data() {
            return {
                items: [], // data asli
                item: {}, //detail
                filteredItems: [], // data hasil filter
                filterTanggalPantau: '',
                filterDI: '',
                daerahIrigasis: [],
                modalInstance: null,

            }
        },
        mounted() {
            this.loadData();
            this.loadDI();
        },
        methods: {
            async loadData1(page = 1) {
                let token = localStorage.getItem("token");

                let dis = await axios.get('/api/user-dis');
                console.log(dis.data);

                let items = [];
                let seen = new Set();

                for (let di of dis.data) {
                    let url = di.has_upi ?
                        `/api/form-pengisian?di_id=${di.id}&pengamat_valid=1&upi_valid=1&page=${page}&per_page=25` :
                        `/api/form-pengisian?di_id=${di.id}&pengamat_valid=1&page=${page}&per_page=25`;
                    if (this.filterDI) url += `&di_id=${this.filterDI}`;
                    if (this.filterTanggalPantau) url += `&tanggal_pantau=${this.filterTanggalPantau}`;

                    console.log(url);

                    let res = await axios.get(url);

                    for (let d of res.data) {
                        if (!seen.has(d.id)) {
                            seen.add(d.id);
                            items.push(d);
                        }
                    }
                }

                console.log(items);

                this.items = res.data.data; // isi data
                this.filteredItems = res.data.data; // sama dulu
                this.pagination = {
                    current: res.data.current_page,
                    last: res.data.last_page,
                    total: res.data.total,
                };




                let url = `/api/form-pengisian?page=${page}&per_page=25`;

                // tambahkan filter langsung di request
                if (this.filterDI) url += `&di_id=${this.filterDI}`;
                if (this.filterTanggalAwal) url += `&tanggal_awal=${this.filterTanggalAwal}`;
                if (this.filterTanggalAkhir) url += `&tanggal_akhir=${this.filterTanggalAkhir}`;

                let res = await axios.get(url);

                this.items = res.data.data; // isi data
                this.filteredItems = res.data.data; // sama dulu
                this.pagination = {
                    current: res.data.current_page,
                    last: res.data.last_page,
                    total: res.data.total,
                };
            },
            async loadData() {
                let token = localStorage.getItem("token");

                let dis = await axios.get('/api/user-dis');
                console.log(dis.data);

                let items = [];
                let seen = new Set();

                for (let di of dis.data) {
                    let url = di.has_upi ?
                        `/api/form-pengisian?di_id=${di.id}&pengamat_valid=0&upi_valid=0` :
                        `/api/form-pengisian?di_id=${di.id}&pengamat_valid=0&upi_valid=0`;
                    console.log(url);

                    let res = await axios.get(url);

                    for (let d of res.data) {
                        if (!seen.has(d.id)) {
                            seen.add(d.id);
                            items.push(d);
                        }
                    }
                }

                console.log(items);

                this.items = items;
                this.filteredItems = items;
            },
            async loadDI() {
                let res = await axios.get('/api/koordinator-di');
                console.log(res.data);
                this.daerahIrigasis = res.data;

            },
            showForm(form) {
                this.item = form;
                console.log(this.item);

                const modalEl = document.getElementById('formLTTModal');
                this.modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
                this.modalInstance.show();
            },
            applyFilter(mode = "equal") {
                const awal = this.filterTanggalPantau ? new Date(this.filterTanggalPantau) : null;
                const di = this.filterDI;

                this.filteredItems = this.items.filter(item => {
                    // pastikan tipe id sama
                    const itemDi = String(item.daerah_irigasi_id);
                    const filterDi = di ? String(di) : null;

                    // parsing tanggal item
                    let tgl = new Date(item.tanggal_pantau);
                    if (isNaN(tgl)) {
                        tgl = new Date(item.tanggal_pantau.replace(" ", "T"));
                    }

                    // Filter daerah irigasi
                    if (filterDi && itemDi !== filterDi) return false;

                    // Filter tanggal
                    if (awal && tgl instanceof Date && !isNaN(tgl)) {
                        const tglStr = tgl.toISOString().split("T")[0];
                        const awalStr = awal.toISOString().split("T")[0];

                        // Hanya izinkan tanggal yang persis sama
                        if (tglStr !== awalStr) return false;
                    }


                    return true;
                });

                console.log("HASIL FILTER:", this.filteredItems);
            },

            syncTanggal() {
                // kalau user pilih tanggal awal, otomatis set tanggal akhir sama
                this.filterTanggalAkhir = this.filterTanggalPantau;
            },
            resetFilter() {
                this.filterTanggalPantau = '';
                this.filterTanggalAkhir = '';
                this.filteredItems = this.items;
                this.filterDI = ''
            },
            formatTanggal(tgl) {
                if (!tgl) return '-';

                // Format ke 17 September 2025
                return new Date(tgl).toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            },
            async hapus(id) {
                if (!confirm("Yakin ingin menghapus data ini?")) {
                    return;
                }

                try {
                    let res = await axios.delete(`/api/form-pengisian/${id}`);
                    console.log(res);

                    // Tutup modal setelah berhasil hapus
                    let modal = bootstrap.Modal.getInstance(document.getElementById('formLTTModal'));
                    modal.hide();

                    // Refresh data tabel
                    this.loadData();

                } catch (e) {
                    console.error(e);
                    alert("Gagal menghapus data!");
                }
            }
        }
    }).mount('#app');
</script>
@endpush