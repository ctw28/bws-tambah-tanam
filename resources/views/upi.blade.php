<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Validasi Unit Pengelola Irigasi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

        <!-- Input Kode Upi -->
        <div v-if="!upi">
            <div class="card shadow-lg">
                <div class="card-body">
                    <div class="col-12 ">
                        <a href="/">
                            <span class="tf-icons bx bx-arrow-left"></span>&nbsp; Kembali ke halaman depan
                        </a>
                    </div>
                    <h5 class="card-title mt-2">Masuk Unit Pengelola Irigasi</h5>
                    <div class="mb-3">
                        <label class="form-label">Masukkan Kode Unit Pengelola Irigasi</label>
                        <input type="text" v-model="kode" class="form-control" placeholder="Kode unik Upi">
                    </div>
                    <button class="btn btn-primary" @click="cekUpi">Masuk
                </div>
            </div>
        </div>

        <!-- Halaman Validasi -->
        <div v-else>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Halo, @{{ upi.nama }}</h4>
                <button class="btn btn-sm btn-danger" @click="logout">Keluar</button>
            </div>

            <div class="card shadow-lg">
                <div class="card-header">
                    <h5 class="mb-3">Daftar Validasi Form</h5>

                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <button class="nav-link" :class="{ active: activeTab === 'dashboard' }" @click="activeTab = 'dashboard'">
                                📝 Basisdata Hasil Pemantauan
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" :class="{ active: activeTab === 'validasi' }" @click="activeTab = 'validasi'">
                                📝 Validasi Form
                            </button>
                        </li>
                        <li class="nav-item">
                            <button
                                class="nav-link"
                                :class="{ active: activeTab === 'permasalahan' }"
                                @click="activeTab = 'permasalahan'">
                                📊 Permasalahan
                            </button>

                        </li>
                    </ul>
                </div>
                <div class="card-body">



                    <div v-if="activeTab === 'dashboard'">
                        <div class="card shadow-sm mb-3">
                            <div class="card-body">
                                <div class="row g-2 align-items-end">

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
                                        <button class="btn btn-primary btn w-100" @click="applyFilterDashboard">
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
                                    <tr>
                                        <th>No</th>
                                        <th>Daerah Irigasi</th>
                                        <th>Padi (ha)</th>
                                        <th>Palawija (ha)</th>
                                        <th>Lainnya (ha)</th>
                                        <th>Total Luas Tanam (ha)</th>
                                        <th>Baku</th>
                                        <th>Potensial</th>
                                        <th>Fungsional</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(data, namaDI,index) in rekapPerDaerahIrigasi" :key="namaDI">
                                        <td>@{{ index + 1 }}</td>
                                        <td>@{{ namaDI }}</td>
                                        <td>@{{ data.padi.toFixed(2) }}</td>
                                        <td>@{{ data.palawija.toFixed(2) }}</td>
                                        <td>@{{ data.lainnya.toFixed(2) }}</td>
                                        <td>@{{ data.total.toFixed(2) }}</td>
                                        <td>@{{ data.baku.toFixed(3) }}</td>
                                        <td>@{{ data.potensial.toFixed(3) }}</td>
                                        <td>@{{ data.fungsional.toFixed(3) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <h4 class="mt-5">📈 Informasi Grafis Luas Tanam Daerah Irigasi </h4>
                        <canvas id="chartDI" height="100"></canvas>
                        <h4 class="mt-5">📈 Informasi Grafis Jenis Tanaman</h4>
                        <canvas id="chartItem" height="100"></canvas>

                    </div>
                    <div v-else-if="activeTab === 'validasi'">
                        <!-- Filter tanggal -->
                        <div class="card shadow-sm mb-3">
                            <div class="card-body">
                                <div class="row g-2 align-items-end">
                                    <!-- Pilih DI -->
                                    <div class="col-6 col-md-3">
                                        <label class="form-label fw-bold">Daerah Irigasi</label>
                                        <select class="form-select" v-model="filterDi">
                                            <option value="">-- Pilih Daerah Irigasi --</option>
                                            <option
                                                v-for="s in upi.daerah_irigasis"
                                                :key="s.id"
                                                :value="s.id">
                                                @{{ s.nama }}
                                            </option>
                                        </select>
                                    </div>

                                    <div class="col-6 col-md-3">
                                        <label class="form-label fw-bold">Status Validasi</label>
                                        <select class="form-select" v-model="filterUpiValid">
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
                                        <th>Pengamat</th>
                                        <th>Saluran</th>
                                        <th>Status Validasi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(f,index) in filteredItems" :key="f.id">
                                        <td>@{{ (pagination.current - 1) * perPage + (index + 1) }}</td>

                                        <td>@{{ formatTanggal(f.tanggal_pantau) }}</td>
                                        <td>@{{ f.daerah_irigasi?.pengamat?.nama }}</td>
                                        <td>@{{ f.saluran.nama }} / @{{ f.bangunan.nama }} / @{{ f.petak.nama }}</td>
                                        <td class="text-center">
                                            <span v-if="f.validasi && f.validasi.upi_valid == 1">✅ Valid</span>
                                            <span v-else>❌ Belum</span>

                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-warning" @click="showForm(f)">
                                                Lihat Form / Validasi
                                            </button>
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
                        <div class="table-responsive">
                            <div class="card shadow-sm mb-3">
                                <div class="card-body">
                                    <div class="row g-2 align-items-end">
                                        <!-- Pilih DI -->
                                        <div class="col-6 col-md-3">
                                            <label class="form-label fw-bold">Daerah Irigasi</label>
                                            <select class="form-select" v-model="filterDi">
                                                <option value="">-- Pilih Daerah Irigasi --</option>
                                                <option
                                                    v-for="s in upi.daerah_irigasis"
                                                    :key="s.id"
                                                    :value="s.id">
                                                    @{{ s.nama }}
                                                </option>
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
                                            <button class="btn btn-primary btn w-100" @click="applyFilterPermasalahan">
                                                <span v-if="is_loading" class="spinner-border spinner-border me-1"></span>
                                                <span v-else>Filter</span>
                                            </button>
                                            <button class="btn btn-secondary btn w-100" @click="resetFilter">Reset</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                                    <tr v-for="(item,index) in permasalahans" :key="item.id">
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
                                    <select v-model="perPage" @change="loadPermasalahan(1)" class="form-select form-select" style="width: auto;">
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                    <span>per halaman</span>
                                </div>


                                <!-- Navigasi Pagination -->
                                <nav>
                                    <ul class="pagination pagination mb-0">
                                        <li class="page-item" :class="{ disabled: paginationPermasalahan.current === 1 }">
                                            <a class="page-link" href="#" @click.prevent="loadPermasalahan(pagination.current - 1)">Prev</a>
                                        </li>

                                        <li v-for="page in paginationPermasalahan.last" :key="page" class="page-item" :class="{ active: page === paginationPermasalahan.current }">
                                            <a class="page-link" href="#" @click.prevent="loadPermasalahan(page)">@{{ page }}</a>
                                        </li>

                                        <li class="page-item" :class="{ disabled: pagination.current === pagination.last }">
                                            <a class="page-link" href="#" @click.prevent="loadPermasalahan(paginationPermasalahan.current + 1)">Next</a>
                                        </li>
                                    </ul>
                                </nav>
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
                            <div class="table-responsive">

                                <!-- Tabel -->
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
                            <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button v-if="item.validasi?.upi_valid==0"
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
    <script src="{{asset('/')}}assets/vendor/libs/apex-charts/apexcharts.js"></script>

    <script>
        const {
            createApp
        } = Vue;

        createApp({
            data() {
                return {
                    kode: "",
                    upi: null,
                    forms: [],
                    item: {},
                    modalInstance: null,
                    filteredItems: [],
                    permasalahans: [],
                    filterDi: '',
                    filterDi: '',
                    filterTanggalAwal: '',
                    filterTanggalAkhir: '',
                    filterTanggalAwal: '',
                    filterAkhirPermasalahan: '',
                    filterUpiValid: '',
                    pagination: {
                        current: 1,
                        last: 1,
                        total: 0,
                    },
                    perPage: 25, // default,
                    paginationPermasalahan: {
                        current: 1,
                        last: 1,
                        total: 0,
                    },
                    perPagePermasalahan: 25, // default
                    is_filtered: false,
                    is_loading: false,
                    activeTab: 'dashboard', // default tab yang aktif saat halaman dibuka,
                    rekapItems: [],
                    chartDI: null,
                    chartItem: null
                }
            },
            methods: {

                async cekUpi() {
                    try {
                        let res = await axios.post("/api/upi/validasi-kode", {
                            kode: this.kode
                        });
                        this.upi = res.data.upi;
                        console.log(this.upi);
                        localStorage.setItem("upi", JSON.stringify(res.data.upi));
                    } catch (e) {
                        alert("Kode Upi tidak valid!");
                    }
                },
                // async loadDashboard() {
                //     try {
                //         let url = `/api/form-pengisian?per_page=all&di_id=${this.filterDi}`;

                //         if (this.filterTanggalAwal) url += `&tanggal_awal=${this.filterTanggalAwal}`;
                //         if (this.filterTanggalAkhir) url += `&tanggal_akhir=${this.filterTanggalAkhir}`;

                //         let res = await axios.get(url);
                //         console.log(res.data);
                //         this.filteredItems = res.data.data;

                //     } catch (e) {
                //         console.error(e);
                //     } finally {
                //         this.is_loading = false;
                //     }
                // },
                async loadPermasalahan(page = 1) {
                    try {
                        // alert('load masalah')
                        let url = `/api/form-pengisian?page=${page}&per_page=${this.perPagePermasalahan}&pengamat_valid=1&upi_valid=1&has_permasalahan=1`;

                        if (this.filterDi) url += `&di_id=${this.filterDi}`;
                        if (this.filterTanggalAwal) url += `&tanggal_awal=${this.filterTanggalAwal}`;
                        if (this.filterTanggalAkhir) url += `&tanggal_akhir=${this.filterTanggalAkhir}`;

                        let res = await axios.get(url);
                        console.log(res.data);

                        this.permasalahans = res.data.data;
                        this.paginationPermasalahan = {
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
                async loadData(page = 1) {
                    try {
                        let url = `/api/form-pengisian?page=${page}&per_page=${this.perPage}&di_id=${this.filterDi}`;

                        if (this.filterTanggalAwal) url += `&tanggal_awal=${this.filterTanggalAwal}`;
                        if (this.filterTanggalAkhir) url += `&tanggal_akhir=${this.filterTanggalAkhir}`;
                        if (this.filterUpiValid != "") url += `&upi_valid=${this.filterUpiValid}`;

                        let res = await axios.get(url);
                        console.log(res.data);
                        this.filteredItems = res.data.data;
                        this.is_filtered = true
                        this.is_loading = true;
                        this.pagination = {
                            current: res.data.current_page,
                            last: res.data.last_page,
                            total: res.data.total,
                        };

                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.is_loading = false;
                    }
                },
                async loadDashboard() {
                    try {
                        const diIds = this.upi.daerah_irigasis.map(di => di.id);
                        console.log(diIds);

                        let allData = [];
                        for (let id of diIds) {
                            let url = `/api/form-pengisian?di_id=${id}&pengamat_valid=1`;

                            if (this.filterTanggalAwal) url += `&tanggal_awal=${this.filterTanggalAwal}`;
                            if (this.filterTanggalAkhir) url += `&tanggal_akhir=${this.filterTanggalAkhir}`;

                            let res = await axios.get(url);
                            console.log(res);

                            allData = allData.concat(res.data);

                        }
                        console.log(allData);
                        this.rekapItems = allData;
                        this.chartPerDI();
                        this.chartPerItem();

                    } catch (e) {
                        console.error(e);
                    }
                },
                showForm(form) {
                    this.item = form;
                    // console.log(this.item);

                    const modalEl = document.getElementById('formLTTModal');
                    this.modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
                    this.modalInstance.show();
                },
                async validasi(formId) {
                    alert(formId)
                    if (!confirm("Yakin validasi form ini?")) return;
                    try {
                        let res = await axios.post(`/api/upi/validasi/${formId}`);
                        // console.log(res);

                        this.forms = this.forms.map(f => {
                            if (f.id === formId) {
                                f.validasi = {
                                    ...f.validasi,
                                    upi_valid: true
                                };
                            }
                            return f;
                        });
                        this.applyFilter()
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
                    this.upi = null;
                    localStorage.removeItem("upi");

                    this.kode = "";
                    this.forms = [];
                },
                applyFilter() {
                    this.loadData(1)
                },
                applyFilterPermasalahan() {
                    this.loadPermasalahan(1)
                },
                applyFilterDashboard() {
                    this.loadDashboard()
                },
                resetFilter() {
                    this.filterDi = ''
                    this.filterTanggalAwal = ''
                    this.filterTanggalAkhir = ''
                    this.filteredItems = []
                    this.permasalahans = []
                    this.loadDashboard()
                },
                syncTanggal() {
                    this.filterTanggalAkhir = this.filterTanggalAwal;
                },
                loadUpi() {
                    let data = localStorage.getItem("upi");
                    if (data) {
                        this.upi = JSON.parse(data);
                    }
                },
                chartPerDI() {
                    const labels = Object.keys(this.rekapPerDaerahIrigasi);
                    const dataTotal = Object.values(this.rekapPerDaerahIrigasi).map(r => r.total);

                    if (this.chartDI) this.chartDI.destroy();
                    this.chartDI = new Chart(document.getElementById('chartDI'), {
                        type: 'bar',
                        data: {
                            labels,
                            datasets: [{
                                label: 'Total Luas (ha)',
                                data: dataTotal
                            }]
                        }
                    });
                },
                chartPerItem() {
                    const rekap = this.rekapPerDaerahIrigasi; // fungsi yg sudah dibuat
                    const labels = Object.keys(rekap);

                    const dataPadi = Object.values(rekap).map(r => r.padi);
                    const dataPalawija = Object.values(rekap).map(r => r.palawija);
                    const dataLainnya = Object.values(rekap).map(r => r.lainnya);

                    if (this.chartItem) this.chartItem.destroy();

                    this.chartItem = new Chart(document.getElementById('chartItem'), {
                        type: 'bar',
                        data: {
                            labels,
                            datasets: [{
                                    label: 'Padi (ha)',
                                    data: dataPadi,
                                    backgroundColor: 'rgba(75, 192, 192, 0.6)'
                                },
                                {
                                    label: 'Palawija (ha)',
                                    data: dataPalawija,
                                    backgroundColor: 'rgba(255, 205, 86, 0.6)'
                                },
                                {
                                    label: 'Lainnya (ha)',
                                    data: dataLainnya,
                                    backgroundColor: 'rgba(201, 90, 90, 0.6)'
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'top'
                                },
                                title: {
                                    display: true,
                                    text: 'Rekap Luas Per Daerah Irigasi'
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Luas (ha)'
                                    }
                                }
                            }
                        }
                    });
                },
            },
            computed: {
                rekapPerDaerahIrigasi() {
                    const rekap = {};

                    this.rekapItems.forEach(i => {
                        const di = i.daerah_irigasi;
                        if (!di) return;

                        // ambil nama DI induk jika ada, kalau tidak pakai nama sendiri
                        const namaDI = di.parent_id ?
                            (di.parent?.nama || 'Tidak Ada DI') :
                            (di.nama || 'Tidak Ada DI');

                        if (!rekap[namaDI]) {
                            rekap[namaDI] = {
                                baku: 0,
                                potensial: 0,
                                fungsional: 0,
                                padi: 0,
                                palawija: 0,
                                lainnya: 0,
                                total: 0
                            };
                        }

                        // gunakan data luas dari DI sekarang
                        rekap[namaDI].baku += parseFloat(di.luas_baku ?? 0);
                        rekap[namaDI].potensial += parseFloat(di.luas_potensial ?? 0);
                        rekap[namaDI].fungsional += parseFloat(di.luas_fungsional ?? 0);

                        rekap[namaDI].padi += parseFloat(i.luas_padi ?? 0);
                        rekap[namaDI].palawija += parseFloat(i.luas_palawija ?? 0);
                        rekap[namaDI].lainnya += parseFloat(i.luas_lainnya ?? 0);
                        rekap[namaDI].total +=
                            parseFloat(i.luas_padi ?? 0) +
                            parseFloat(i.luas_palawija ?? 0) +
                            parseFloat(i.luas_lainnya ?? 0);
                    });

                    return rekap;
                }

            },
            mounted() {
                this.loadUpi();
                this.loadDashboard()
            }
        }).mount("#app");
    </script>
</body>

</html>