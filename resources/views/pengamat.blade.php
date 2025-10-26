<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Validasi Pengamat</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="https://sda.pu.go.id/web/images/favicon.png" />
    <!-- Icons. Uncomment required icon fonts -->
    <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{asset('/')}}assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{asset('/')}}assets/vendor/css/theme-default.css"
        class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{asset('/')}}assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{asset('/')}}assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="{{asset('/')}}assets/vendor/css/pages/page-auth.css" />
    <!-- Helpers -->
    <script src="{{asset('/')}}assets/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{asset('/')}}assets/js/config.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.prod.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        .form-line {
            margin-bottom: 8px;
        }

        .form-line span {
            display: inline-block;
            min-width: 250px;
        }

        [v-cloak] {
            display: none;
        }
    </style>
</head>

<body class="bg-light">
    <div id="app" v-cloak class="container my-5">

        <!-- Input Kode Pengamat -->
        <div v-if="!pengamat">
            <div class="card shadow-lg">
                <div class="card-body">
                    <div class="col-12 ">
                        <a href="/">
                            <span class="tf-icons bx bx-arrow-left"></span>&nbsp; Kembali ke halaman depan
                        </a>
                    </div>
                    <h5 class="card-title mt-2">Masuk Pengamat</h5>
                    <div class="mb-3">
                        <label class="form-label">Masukkan Kode Pengamat</label>
                        <input type="text" v-model="kode" class="form-control" placeholder="Kode unik pengamat">
                    </div>
                    <button class="btn btn-primary" @click="cekPengamat">Masuk
                </div>
            </div>
        </div>

        <!-- Halaman Validasi -->
        <div v-else>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Halo, @{{ pengamat.nama }}</h4>
                <button class="btn btn-sm btn-danger" @click="logout">Keluar</button>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-3">Daerah Irigasi @{{ pengamat.daerah_irigasi.nama }}</h5>

                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <button class="nav-link" :class="{ active: activeTab === 'validasi' }" @click="activeTab = 'validasi'">
                                📝 Validasi Form
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" :class="{ active: activeTab === 'rekap' }" @click="activeTab = 'rekap'">
                                📊 Rekap Juru
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div v-if="activeTab === 'validasi'">
                        <div class="card shadow-sm mb-3">
                            <div class="card-body">
                                <div class="row g-2 align-items-end">
                                    <!-- Pilih DI -->
                                    <div class="col-6 col-md-3">
                                        <label class="form-label fw-bold">Juru</label>
                                        <select class="form-select" v-model="filterSaluran">
                                            <option value="">-- Pilih Juru --</option>
                                            <option
                                                v-for="s in petugas_saluran"
                                                :key="s.id"
                                                :value="s.id">
                                                @{{ s.petugas[0].nama }} - @{{ s.nama }}
                                            </option>
                                        </select>
                                    </div>

                                    <div class="col-6 col-md-3">
                                        <label class="form-label fw-bold">Status Validasi</label>
                                        <select class="form-select" v-model="filterpengamatValid">
                                            <option value="">Pilih Status</option>
                                            <option value="0">Belum Valid</option>
                                            <option value="1">Valid</option>
                                        </select>
                                    </div>
                                    <!-- Tanggal awal -->
                                    <div class="col-6 col-md-3">
                                        <label class="form-label fw-bold">Tanggal Awal</label>
                                        <input type="date" v-model="filterTanggalAwal" @change="syncTanggal" class="form-control form-control" />
                                    </div>
                                    <!-- Tanggal awal -->

                                    <!-- Tanggal akhir -->
                                    <div class="col-6 col-md-3">
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
                        <div class="table-responsive">

                            <table class="table table-bordered">
                                <thead>
                                    <tr class="text-center">
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Petugas</th>
                                        <th>Saluran</th>
                                        <th>Status Validasi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(f,index) in filteredItems" :key="f.id">
                                        <!-- <td>@{{index+1}}</td> -->

                                        <td>@{{ (pagination.current - 1) * perPage + (index + 1) }}</td>
                                        <td>@{{ formatTanggal(f.tanggal_pantau) }}</td>
                                        <td>@{{ f.petugas.nama }}</td>
                                        <td>@{{ f.saluran.nama }} / @{{ f.bangunan.nama }} / @{{ f.petak.nama }}</td>
                                        <td class="text-center">
                                            <span v-if="f.validasi && f.validasi.pengamat_valid==1">✅
                                                Valid</span>
                                            <span v-else>❌ Belum</span>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-warning" @click="showForm(f)">Validasi</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <!-- Loading -->
                            <div v-if="is_loading" class="alert alert-secondary text-center mt-3">
                                <div class="spinner-border spinner-border-sm me-2"></div>
                                Memuat data...
                            </div>

                            <!-- Tidak loading -->
                            <div v-else>
                                <p v-if="!is_filtered" class="text-muted text-center mt-2">
                                    Filter data terlebih dahulu
                                </p>

                                <p v-else-if="filteredItems.length === 0" class="text-muted text-center mt-2">
                                    Data tidak ditemukan
                                </p>
                            </div>

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
                    </div>
                    <div v-else>
                        <!-- Isi halaman rekap juru -->
                        <h4 class="mt-4">Rekap Juru</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Terakhir Mengisi</th>
                                        <th>Petugas</th>
                                        <th>Luas Tanam Padi</th>
                                        <th>Luas Tanam Palawija</th>
                                        <th>Luas Tanam Lainnya</th>
                                        <th>Total Luas Tanam</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(rekap, nama, index) in rekapPerPetugas" :key="nama">
                                        <td>@{{ index + 1 }}</td>
                                        <td>@{{ formatTanggal(rekap.terakhir_isi) }} <br>
                                            <small
                                                :class="{
                                                    'text-danger fw-bold': rekap.status_label === 'merah',
                                                    'text-warning fw-bold': rekap.status_label === 'kuning',
                                                    'text-success fw-bold': rekap.status_label === 'hijau'
                                                }">
                                                @{{ rekap.hari_lalu }} hari yang lalu
                                            </small>

                                        </td>
                                        <td>@{{ nama }}</td>
                                        <td>@{{ rekap.padi.toFixed(2) }}</td>
                                        <td>@{{ rekap.palawija.toFixed(2) }}</td>
                                        <td>@{{ rekap.lainnya.toFixed(2) }}</td>
                                        <td>@{{ rekap.total.toFixed(2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
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

                        <div class="p-3 rounded shadow-sm bg-light mb-4">
                            <div class="mb-2">
                                <small class="text-muted d-block">Nama Petugas OP</small>
                                <span class="fw-semibold">@{{ item.petugas ? item.petugas.nama : '-' }}</span>
                            </div>

                            <div class="mb-2">
                                <small class="text-muted d-block">Tanggal Pemantauan</small>
                                <span class="fw-semibold">@{{ formatTanggal(item.tanggal_pantau) }}</span>
                            </div>

                            <div class="mb-2">
                                <small class="text-muted d-block">Daerah Irigasi</small>
                                <span class="fw-semibold">@{{ item.daerah_irigasi ? item.daerah_irigasi.nama : '-' }}</span>
                            </div>

                            <div class="mb-2">
                                <small class="text-muted d-block">Desa/Kelurahan</small>
                                <span class="fw-semibold">@{{ item.desa }}</span>
                            </div>

                            <div class="mb-2">
                                <small class="text-muted d-block">Kecamatan</small>
                                <span class="fw-semibold">@{{ item.kecamatan }}</span>
                            </div>

                            <div class="mb-2">
                                <small class="text-muted d-block">Kabupaten/Kota</small>
                                <span class="fw-semibold">@{{item.kabupaten ? item.kabupaten.nama : '-'}}</span>
                            </div>

                            <div class="mb-2">
                                <small class="text-muted d-block">Nama Saluran (Sekunder/Primer)</small>
                                <span class="fw-semibold">@{{item.saluran ? item.saluran.nama : '-'}}</span>
                            </div>

                            <div class="mb-2">
                                <small class="text-muted d-block">Nama Bangunan Bagi/Sadap</small>
                                <span class="fw-semibold">@{{item.bangunan ? item.bangunan.nama : '-'}}</span>
                            </div>

                            <div class="mb-2">
                                <small class="text-muted d-block">Kode/Nama Petak Layanan</small>
                                <span class="fw-semibold">@{{item.petak ? item.petak.nama : '-'}}</span>
                            </div>
                            <!-- Tabel LTT -->
                            <hr class="mt-2">

                            <h5 class="fw-bold">Pemantauan Luas Tambah Tanam (LTT)</h5>
                            <div class="mb-2">
                                <small class="text-muted d-block">Luas Komulatif Padi</small>
                                <span class="fw-semibold">@{{ item.luas_padi }} ha</span>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted d-block">Luas Komulatif Palawija</small>
                                <span class="fw-semibold">@{{ item.luas_palawija }} ha</span>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted d-block">Luas Komulatif Lainnya</small>
                                <span class="fw-semibold">@{{ item.luas_lainnya }} ha</span>
                            </div>
                            <div class="mt-3">
                                <h4>Foto Pemantauan</h4>
                                <img
                                    v-if="item.foto_pemantauan"
                                    :src="`/storage/${item.foto_pemantauan}`"
                                    alt="Preview Foto"
                                    class="img-fluid rounded"
                                    width="300" />
                            </div>
                            <hr class="mt-2">
                            <h5 class="fw-bold">Kelembagaan P3A</h5>

                            <div class="mb-3">
                                <template v-if="item.form_pengisian_p3a && item.form_pengisian_p3a.length">
                                    <span v-for="p in item.form_pengisian_p3a" :key="p.id" class="badge bg-primary me-1 mb-1">
                                        @{{ p.p3a.nama }}
                                    </span>
                                </template>
                                <p v-else class="text-muted fst-italic">Data P3A tidak ada / tidak diisi.</p>
                            </div>


                            <!-- Tabel Permasalahan -->
                            <hr class="mt-2">

                            <h5 class="fw-bold">Permasalahan di Lapangan</h5>

                            <div class="mb-2" v-for="(p, index) in item.permasalahan" :key="p.id">
                                <span class="fw-semibold">
                                    @{{ p.master_permasalahan.id }}. @{{ p.master_permasalahan.nama }} <br>
                                    <span class="text-danger fw-bold">
                                        Ada
                                    </span> <br>
                                    Keterangan : @{{ p.keterangan || '-' }}<br>
                                    Foto permasalahan :
                                    <img
                                        :src="`/storage/${p.foto_permasalahan}`"
                                        alt="Preview Foto Permasalahan"
                                        class="img-fluid rounded mt-2"
                                        width="300">
                                </span>
                            </div>


                        </div>

                    </div>

                    <div class="modal-footer">
                        <button v-if="item.validasi?.pengamat_valid==0" class="btn btn-danger me-auto" @click="hapus(item.id)"><i class="menu-icon tf-icons bx bx-trash"></i> Hapus / Tidak Valid</button>

                        <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button v-if="item.validasi?.pengamat_valid==0"
                            class="btn btn-warning" @click="validasi(item.id)">
                            Validasi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>

    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const {
            createApp
        } = Vue;

        createApp({
            data() {
                return {
                    kode: "",
                    pengamat: null,
                    petugas_saluran: [],
                    filterSaluran: '',
                    forms: [],
                    item: {},
                    modalInstance: null,
                    filteredItems: [],
                    filterpengamatValid: '',
                    filterTanggalAwal: '',
                    filterTanggalAkhir: '',
                    pagination: {
                        current: 1,
                        last: 1,
                        total: 0,
                    },
                    perPage: 25, // default
                    is_filtered: false,
                    is_loading: false,
                    rekapPetugas: [],
                    activeTab: 'validasi', // default tab yang aktif saat halaman dibuka

                }
            },
            methods: {
                async cekPengamat() {
                    try {
                        let res = await axios.post("/api/pengamat/validasi-kode", {
                            kode: this.kode
                        });
                        console.log(res.data);

                        this.pengamat = res.data.pengamat;
                        console.log(this.pengamat);
                        localStorage.setItem("pengamat", JSON.stringify(res.data.pengamat));


                        // this.loadData(1)
                        this.loadPetugas()
                        this.loadRekap();


                    } catch (e) {
                        alert("Kode pengamat tidak valid!");
                    }
                },
                // async loadData() {
                //     try {
                //         let res = await axios.get(
                //             `/api/form-pengisian?di_id=${this.pengamat.daerah_irigasi_id}`);
                //         console.log(res);

                //         this.forms = res.data;
                //         this.filteredItems = res.data;
                //     } catch (e) {
                //         console.error(e);
                //     }
                // },

                async loadRekap() {
                    try {
                        const pengamat = JSON.parse(localStorage.getItem("pengamat"));
                        let url = `/api/form-pengisian?di_id=${pengamat.daerah_irigasi_id}`;

                        // if (this.filterSaluran) url += `&saluran=${this.filterSaluran}`;
                        // if (this.filterTanggalAwal) url += `&tanggal_awal=${this.filterTanggalAwal}`;
                        // if (this.filterTanggalAkhir) url += `&tanggal_akhir=${this.filterTanggalAkhir}`;
                        // if (this.filterpengamatValid != "") url += `&pengamat_valid=${this.filterpengamatValid}`;

                        let res = await axios.get(url);
                        console.log(res.data);

                        this.rekapPetugas = res.data;
                        this.is_loading = true;

                    } catch (err) {
                        console.error(err);
                    } finally {
                        this.is_loading = false;
                    }
                },
                async loadData(page = 1) {
                    try {
                        const pengamat = JSON.parse(localStorage.getItem("pengamat"));


                        let url = `/api/form-pengisian?page=${page}&per_page=${this.perPage}&di_id=${pengamat.daerah_irigasi_id}`;

                        if (this.filterSaluran) url += `&saluran=${this.filterSaluran}`;
                        if (this.filterTanggalAwal) url += `&tanggal_awal=${this.filterTanggalAwal}`;
                        if (this.filterTanggalAkhir) url += `&tanggal_akhir=${this.filterTanggalAkhir}`;
                        if (this.filterpengamatValid != "") url += `&pengamat_valid=${this.filterpengamatValid}`;

                        let res = await axios.get(url);
                        console.log(res.data);

                        this.items = res.data.data;
                        this.filteredItems = res.data.data;
                        this.pagination = {
                            current: res.data.current_page,
                            last: res.data.last_page,
                            total: res.data.total,
                        };
                        this.is_loading = true;

                    } catch (err) {
                        console.error(err);
                    } finally {
                        this.is_loading = false;
                    }
                },
                showForm(form) {
                    this.item = form;
                    console.log(this.item);

                    const modalEl = document.getElementById('formLTTModal');
                    this.modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
                    this.modalInstance.show();
                },
                async validasi(formId) {

                    if (!confirm("Yakin validasi form ini?")) return;
                    try {
                        let res = await axios.post(`/api/pengamat/validasi/${formId}`, {
                            pengamat_id: this.pengamat.id
                        });
                        console.log(res);

                        this.forms = this.forms.map(f => {
                            if (f.id === formId) {
                                f.validasi = {
                                    ...f.validasi,
                                    pengamat_valid: true
                                };
                            }
                            return f;
                        });
                        if (this.modalInstance) {
                            this.modalInstance.hide();
                        }
                    } catch (e) {
                        console.error(e);
                        alert("Gagal validasi");
                    }
                },
                formatTanggal(tgl) {
                    if (!tgl) return '-';

                    // Format ke 17 September 2025
                    return new Date(tgl).toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'long',
                        year: 'numeric'
                    });
                },
                logout() {
                    this.pengamat = null;
                    localStorage.removeItem("pengamat");

                    this.kode = "";
                    this.forms = [];
                },
                applyFilter() {
                    this.is_filtered = true
                    this.loadData(1)
                },
                resetFilter() {
                    this.filterTanggalAwal = ''
                    this.filterTanggalAkhir = ''
                    this.filterpengamatValid = ''
                    this.filterSaluran = ''
                    this.filteredItems = []
                    this.is_filtered = false

                },
                syncTanggal() {
                    // kalau user pilih tanggal awal, otomatis set tanggal akhir sama
                    this.filterTanggalAkhir = this.filterTanggalAwal;
                },
                loadPengamat() {
                    let data = localStorage.getItem("pengamat");
                    if (data) {
                        this.pengamat = JSON.parse(data);
                        // this.loadData(1)
                        this.loadPetugas()
                        this.loadRekap();

                        // bisa optional: validasi token ke server
                    }
                },
                async loadPetugas() {
                    try {
                        const pengamat = JSON.parse(localStorage.getItem("pengamat"));

                        console.log(pengamat.daerah_irigasi_id);

                        let res = await axios.get("/api/master/daerah-irigasi", {
                            params: {
                                per_page: 'all',
                                id: pengamat.daerah_irigasi_id
                            }
                        });

                        console.log(res.data.salurans);
                        this.petugas_saluran = res.data.salurans


                    } catch (e) {
                        alert("Kode pengamat tidak valid!");
                    }
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
            },
            computed: {
                rekapPerPetugas() {
                    const rekap = {};

                    this.rekapPetugas.forEach(i => {
                        const petugasNama = i.petugas?.nama || 'Tanpa Nama';
                        const saluranNama = i.saluran?.nama || 'Tanpa Saluran';
                        const key = `${petugasNama} - ${saluranNama}`;

                        if (!rekap[key]) {
                            rekap[key] = {
                                petugas: petugasNama,
                                saluran: saluranNama,
                                padi: 0,
                                palawija: 0,
                                lainnya: 0,
                                debit_air: 0,
                                total: 0,
                                terakhir_isi: i.tanggal_pantau
                            };
                        }

                        rekap[key].debit_air += parseFloat(i.debit_air) || 0;
                        rekap[key].padi += parseFloat(i.luas_padi) || 0;
                        rekap[key].palawija += parseFloat(i.luas_palawija) || 0;
                        rekap[key].lainnya += parseFloat(i.luas_lainnya) || 0;
                        rekap[key].total +=
                            (parseFloat(i.luas_padi) || 0) +
                            (parseFloat(i.luas_palawija) || 0) +
                            (parseFloat(i.luas_lainnya) || 0);

                        // Ambil tanggal terakhir isi (yang paling baru)
                        if (new Date(i.tanggal_pantau) > new Date(rekap[key].terakhir_isi)) {
                            rekap[key].terakhir_isi = i.tanggal_pantau;
                        }
                    });

                    // 🔹 Ubah jadi array & urutkan berdasarkan nama petugas
                    const sorted = Object.entries(rekap)
                        .sort(([, a], [, b]) => a.petugas.localeCompare(b.petugas))
                        .reduce((obj, [key, val]) => {
                            // Hitung berapa hari yang lalu
                            const today = new Date();
                            const lastDate = new Date(val.terakhir_isi);
                            const diffDays = Math.floor((today - lastDate) / (1000 * 60 * 60 * 24));

                            // Tentukan label warna
                            let label = '';
                            if (diffDays > 14) {
                                label = 'merah'; // lebih dari 14 hari
                            } else if (diffDays > 7) {
                                label = 'kuning'; // lebih dari 7 hari
                            } else {
                                label = 'hijau'; // masih baru
                            }

                            obj[key] = {
                                ...val,
                                hari_lalu: diffDays,
                                status_label: label
                            };

                            return obj;
                        }, {});

                    return sorted;
                }

            },
            mounted() {
                this.loadPengamat();
            }
        }).mount("#app");
    </script>
</body>

</html>