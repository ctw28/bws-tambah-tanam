@extends('admin.template')

@section('content')
<div id="app" v-cloak class="container mt-4">
    <div class="card h-100">
        <div class="card-header">
            <h5 class="mb-0">Daftar Permasalahan</h5>
        </div>
        <div class="card-body">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <div class="row g-2 align-items-end">
                        <!-- Pilih DI -->
                        <div class="col-12 col-md-3">
                            <label class="form-label fw-bold">Daerah Irigasi</label>
                            <select class="form-select form-select" v-model="filterDI">
                                <option value="">-- Pilih DI --</option>
                                <option v-for="d in daerahIrigasis" :value="d.id">@{{ d.nama }}</option>
                            </select>
                        </div>

                        <!-- Tanggal awal -->
                        <div class="col-6 col-md-2">
                            <label class="form-label fw-bold">Tanggal Awal</label>
                            <input type="date" v-model="filterTanggalAwal" @change="syncTanggal" class="form-control form-control" />
                        </div>

                        <!-- Tanggal akhir -->
                        <div class="col-6 col-md-2">
                            <label class="form-label fw-bold">Tanggal Akhir</label>
                            <input type="date" v-model="filterTanggalAkhir" class="form-control form-control" />
                        </div>

                        <!-- Tombol -->
                        <div class="col-12 col-md-3 d-flex gap-2">
                            <button class="btn btn-primary btn w-100" @click="applyFilter">
                                <span v-if="is_loading" class="spinner-border spinner-border me-1"></span>
                                <span v-else>Filter</span>
                            </button>
                            <button class="btn btn-secondary btn w-100" @click="resetFilter">Reset</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive" v-if="items.length > 0">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal Pantau</th>
                            <th>Nama Petugas</th>
                            <th>Daerah Irigasi</th>
                            <th>Permasalahan</th>
                            <th>Lihat Form</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item,index) in items" :key="item.id">
                            <td>@{{index+1}}</td>
                            <td>@{{ item.tanggal_pantau }}</td>
                            <td>@{{ item.petugas?.nama ?? '-' }}</td>
                            <td>@{{ item.daerah_irigasi?.nama ?? '-' }}
                                / @{{ item.bangunan?.nama ?? '-' }}
                                / @{{ item.petak?.nama ?? '-' }}
                            </td>
                            <td>
                                <ul class="mb-0" style="list-style: none; padding-left: 0;">
                                    <li v-for="p in item.permasalahan" :key="p.id">
                                        @{{ p.master_permasalahan?.id }}. @{{ p.master_permasalahan?.nama }} : @{{ p.keterangan }} <br>
                                        <img v-if="p.foto_permasalahan" :src="`/storage/${p.foto_permasalahan}`" width="200">
                                    </li>
                                </ul>
                            </td>
                            <td>
                                <button @click="showForm(item)" class="btn btn-primary btn-sm"><i
                                        class="menu-icon tf-icons bx bx-eye me-0"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <!-- Pilih jumlah data per halaman -->
                    <div class="d-flex align-items-center gap-2">
                        <select v-model="perPage" @change="loadData(1)" class="form-select form-select" style="width: auto;">
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span>per halaman</span>
                    </div>


                    <!-- Navigasi Pagination -->
                    <nav>
                        <ul class="pagination pagination mb-0">
                            <li class="page-item" :class="{ disabled: pagination.current === 1 }">
                                <a class="page-link" href="#" @click.prevent="loadData(pagination.current - 1)">Prev</a>
                            </li>

                            <li v-for="page in pagination.last" :key="page" class="page-item" :class="{ active: page === pagination.current }">
                                <a class="page-link" href="#" @click.prevent="loadData(page)">@{{ page }}</a>
                            </li>

                            <li class="page-item" :class="{ disabled: pagination.current === pagination.last }">
                                <a class="page-link" href="#" @click.prevent="loadData(pagination.current + 1)">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            <!-- Placeholder ketika belum ada data -->
            <!-- Saat loading -->
            <div v-if="is_loading" class="alert alert-secondary text-center mt-3">
                <div class="spinner-border spinner-border-sm me-2"></div>
                Memuat data...
            </div>

            <!-- Saat sudah difilter tapi kosong -->
            <div v-else-if="is_filtered && items.length === 0" class="alert alert-info text-center mt-3">
                <div class="text-center text-muted">
                    Data tidak ditemukan
                </div>
            </div>

            <!-- Saat belum difilter -->
            <div v-else-if="!is_filtered" class="alert alert-warning text-center mt-3">
                <div class="text-center text-muted">
                    Silakan filter data terlebih dahulu
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="formLTTModal" tabindex="-1">
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
                                    <td>@{{ p.id }}</td>
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
                    <!-- <button class="btn btn-danger me-auto" @click="hapus(item.id)"><i class="menu-icon tf-icons bx bx-trash"></i> Hapus</button> -->
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
                items: [],
                item: {},
                modalInstance: null,
                daerahIrigasis: [],
                filterDI: '',
                is_filtered: false,
                is_loading: false,
                pagination: {
                    current: 1,
                    last: 1,
                    total: 0,
                },
                perPage: 25, // default
                filteredItems: [], // data hasil filter
                filterTanggalAwal: '',
                filterTanggalAkhir: '',

            };
        },
        async mounted() {
            // await this.loadData();
            this.loadDI();

        },
        methods: {
            async loadDI() {
                let res = await axios.get('/api/koordinator-di');
                console.log(res.data);
                this.daerahIrigasis = res.data;

            },
            applyFilter() {
                this.is_filtered = true
                this.is_loading = true;

                this.loadData(1);
            },
            async loadData(page = 1) {
                try {
                    let url = `/api/form-pengisian?page=${page}&per_page=${this.perPage}&pengamat_valid=1&upi_valid=1&has_permasalahan=1`;

                    if (this.filterDI) url += `&di_id=${this.filterDI}`;
                    if (this.filterTanggalAwal) url += `&tanggal_awal=${this.filterTanggalAwal}`;
                    if (this.filterTanggalAkhir) url += `&tanggal_akhir=${this.filterTanggalAkhir}`;

                    let res = await axios.get(url);
                    console.log(res.data);

                    this.items = res.data.data;
                    this.filteredItems = res.data.data;
                    this.pagination = {
                        current: res.data.current_page,
                        last: res.data.last_page,
                        total: res.data.total,
                    };
                } catch (err) {
                    console.error(err);
                } finally {
                    this.is_loading = false;
                }
            },
            syncTanggal() {
                // kalau user pilih tanggal awal, otomatis set tanggal akhir sama
                this.filterTanggalAkhir = this.filterTanggalAwal;
            },
            resetFilter() {
                this.filterDI = "";
                this.filterTanggalAwal = "";
                this.filterTanggalAkhir = "";
                this.items = [];
                this.is_filtered = false

            },

            async loadData1() {
                let token = localStorage.getItem("token");

                let dis = await axios.get('/api/user-dis');
                console.log(dis.data);

                let items = [];
                let seen = new Set();

                for (let di of dis.data) {
                    let url = di.has_upi ?
                        `/api/form-pengisian?di_id=${di.id}&pengamat_valid=1&upi_valid=1&has_permasalahan=1` :
                        `/api/form-pengisian?di_id=${di.id}&pengamat_valid=1&has_permasalahan=1`;
                    console.log(url);

                    let res = await axios.get(url);

                    for (let d of res.data.data) {
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
            showForm(form) {
                this.item = form;
                console.log(this.item);

                const modalEl = document.getElementById('formLTTModal');
                this.modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
                this.modalInstance.show();
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
        }
    }).mount('#app');
</script>
@endpush