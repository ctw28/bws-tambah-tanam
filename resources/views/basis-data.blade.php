<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Basisdata Hasil Pemantauan </title>
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

        <div class="col-12 mb-3">
            <a href="/">
                <span class="tf-icons bx bx-arrow-left"></span>&nbsp; Kembali ke halaman depan
            </a>
        </div>
        <div class="card shadow-lg">
            <div class="card-header">
                <h5 class="mb-0">Basis Data Hasil Pemantauan LTT</h5>
            </div>
            <div class="card-body">
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <div class="row g-2 align-items-end">
                            <!-- Pilih DI Induk -->
                            <div class="col-12 col-md-3">
                                <label class="form-label fw-bold">Daerah Irigasi (Induk)</label>
                                <select class="form-select" v-model="filterDI" @change="checkChild">
                                    <option value="">-- Pilih Daerah Irigasi --</option>
                                    <option v-for="d in daerahIrigasis" :value="d.id">@{{ d.nama }}</option>
                                </select>
                            </div>

                            <!-- Pilih DI Anak -->
                            <div class="col-12 col-md-3" v-if="isChild">
                                <label class="form-label fw-bold">Wilayah</label>
                                <select class="form-select" v-model="filterDIChild">
                                    <option value="">-- Pilih Wilayah --</option>
                                    <option v-for="d in daerahIrigasisChild" :value="d.id">@{{ d.nama }}</option>
                                </select>
                            </div>

                            <!-- <div class="col-12 col-md-3">

                                <select v-model="filterTahun" class="form-select">
                                    <option value="">-- Pilih Tahun --</option>
                                    <option value="2025">2025</option>
                                    <option value="2026">2026</option>
                                </select>
                            </div> -->
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Tanggal Mulai</label>
                                <input type="date" v-model="filterTanggalAwal" class="form-control">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold">Tanggal Selesai</label>
                                <input type="date" v-model="filterTanggalAkhir" class="form-control">
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
                <div v-if="isFilter">
                    <div class="card h-100">
                        <div class="card-body">

                            <div class="user-profile-header d-flex flex-column flex-lg-row text-sm-start text-center mb-8">
                                <div class="flex-grow-1 mt-2">
                                    <div class="user-profile-info">
                                        <h3>DATA TEKNIS</h3>
                                        <h4 v-if="!isChild" class="mb-2">Daerah Irigasi @{{selectedDI.nama}} - Kab. @{{selectedDI.kabupatens[0].nama}}</h4>
                                        <h4 v-if="isChild" class="mb-2">Daerah Irigasi @{{selectedIndukDI.nama}} Wilayah @{{selectedDI.nama}}</h4>
                                        <h5 class="mt-4">Luas Daerah Irigasi</h5>
                                        <div class="row mt-4">
                                            <div class="col d-flex">
                                                <div class="me-3">
                                                    <span class="badge rounded-2 bg-label-warning p-2"><i class="icon-base bx bx-water icon-lg text-warning"></i></span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">@{{ selectedDI.luas_baku }} ha</h6>
                                                    <small>Luas Baku</small>
                                                </div>
                                            </div>
                                            <div class="col d-flex">
                                                <div class="me-3">
                                                    <span class="badge rounded-2 bg-label-info p-2"><i class="icon-base bx bx-water icon-lg text-info"></i></span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">@{{ selectedDI.luas_potensial }} ha</h6>
                                                    <small>Luas Potensial</small>
                                                </div>
                                            </div>
                                            <div class="col d-flex">
                                                <div class="me-3">
                                                    <span class="badge rounded-2 bg-label-success p-2"><i class="icon-base bx bx-water icon-lg text-success"></i></span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">@{{ selectedDI.luas_fungsional }} ha</h6>
                                                    <small>Luas Fungsional</small>
                                                </div>
                                            </div>
                                        </div>
                                        <h5 class="mt-4">Saluran, Juru dan Pengamat</h5>

                                        <div class="row mt-4">
                                            <div class="col d-flex">
                                                <div class="me-3">
                                                    <span class="badge rounded-2 bg-label-warning p-2"><i class="icon-base bx bx-git-branch icon-lg text-warning"></i></span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">@{{rekap.total_saluran}}</h6>
                                                    <small>Saluran</small>
                                                </div>
                                            </div>
                                            <div class="col d-flex">
                                                <div class="me-3">
                                                    <span class="badge rounded-2 bg-label-success p-2"><i class="icon-base bx bx-building icon-lg text-success"></i></span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">@{{rekap.total_bangunan}}</h6>
                                                    <small>Bangunan</small>
                                                </div>
                                            </div>
                                            <div class="col d-flex">
                                                <div class="me-3">
                                                    <span class="badge rounded-2 bg-label-info p-2"><i class="icon-base bx bx-traffic-cone icon-lg text-info"></i></span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">@{{rekap.total_petak}}</h6>
                                                    <small>Petak</small>
                                                </div>
                                            </div>
                                            <!-- Pengamat -->
                                            <div class="col d-flex mt-3 mt-md-0">
                                                <div class="me-3">
                                                    <span class="badge rounded-2 bg-label-secondary p-2">
                                                        <i class="icon-base bx bx-bullseye icon-lg text-secondary"></i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">@{{rekap.total_pengamat}}</h6>
                                                    <small>Pengamat</small>
                                                </div>
                                            </div>

                                            <!-- Juru -->
                                            <div class="col d-flex mt-3 mt-md-0">
                                                <div class="me-3">
                                                    <span class="badge rounded-2 bg-label-dark p-2">
                                                        <i class="icon-base bx bx-user icon-lg text-dark"></i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">@{{rekap.total_juru}}</h6>
                                                    <small>Juru</small>
                                                </div>
                                            </div>


                                        </div>
                                        <h5 class="mt-4">Kecamatan dan Desa</h5>

                                        <div class="row mt-4">
                                            <div class="col d-flex">
                                                <div class="me-3">
                                                    <span class="badge rounded-2 bg-label-primary p-2"><i class="icon-base bx bx-building icon-lg text-primary"></i></span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">@{{rekap.total_kecamatan}}</h6>
                                                    <small>Kecamatan</small>
                                                </div>
                                            </div>
                                            <div class="col d-flex">
                                                <div class="me-3">
                                                    <span class="badge rounded-2 bg-label-info p-2"><i class="icon-base bx bx-building icon-lg text-info"></i></span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">@{{rekap.total_desa}}</h6>
                                                    <small>Desa</small>
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                    <div class="card mb-3 mt-3">
                        <div class="card-body table-responsive">

                            <h5 class="fw-bold mb-3">Rekapitulasi Luas Tambah Tanam (LTT)</h5>
                            <p v-if="skMasaTanam">
                                SK Masa Tanam :
                                SK @{{ skMasaTanam.sk_dari }} No @{{ skMasaTanam.no_sk }} tahun @{{ skMasaTanam.tahun_sk }} — Tanggal: @{{ formatTanggal(skMasaTanam.tanggal_terbit_sk) }}

                            </p>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Masa Tanam (MT)</th>
                                        <th>Periode Waktu</th>
                                        <th>Total Luas</th>
                                        <th>Padi</th>
                                        <th>Palawija</th>
                                        <th>Lainnya</th>
                                        <th>Detail</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- <tr v-for="(row, i) in topPerMt" :key="i"> -->
                                    <tr v-for="(row, index) in topPerMt" :key="index">
                                        <td>@{{ index + 1 }}</td>
                                        <td>MT @{{ row.masa_tanam }}</td>
                                        <td> @{{ bulanIndo(row.bulan_mulai) }} - @{{ bulanIndo(row.bulan_selesai) }}</td>
                                        <td>@{{ formatAngka(row.total_luas) }}</td>
                                        <td>@{{ formatAngka(row.padi) }}</td>
                                        <td>@{{ formatAngka(row.palawija) }}</td>
                                        <td>@{{ formatAngka(row.lainnya) }}</td>
                                        <td><button class="btn btn-primary btn-sm" @click="openDetail(row.daerah_irigasi_id, row.tanggal_minggu)">Detail</button></td>
                                    </tr>

                                    <tr v-if="topPerMt.length === 0">
                                        <td colspan="8" class="text-center text-muted">
                                            Belum ada data
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>

                    <!-- Ringkasan umum -->
                    <div class="card h-100 mt-3">
                        <div class="card-body">

                            <h4>Informasi Permasalahan</h4>

                            <table class="table table-bordered w-100">
                                <thead>
                                    <tr class="text-center">
                                        <th>No</th>
                                        <th>Permasalahan</th>
                                        <th>Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, i) in rekapPermasalahan" :key="i">
                                        <td class="text-center ">@{{ i+1 }}</td>
                                        <td class="">@{{ item.nama }}</td>
                                        <td class="text-center font-bold">@{{ item.total }}</td>
                                    </tr>
                                    <tr class="fw-bold bg-light">
                                        <td colspan="2" class="text-center">Total</td>
                                        <td class="text-center">@{{ totalKeseluruhan }}</td>
                                    </tr>

                                </tbody>
                            </table>

                            <h4 class="mt-5">📈 Informasi Grafis</h4>
                            <div class="col-12">

                                <canvas id="chartItem" height="100"></canvas>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- MODAL DETAIL -->
        <div class="modal fade" id="detailModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">
                            Detail Pengisian - @{{ selectedTanggal }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body table-responsive">

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Padi</th>
                                    <th>Palawija</th>
                                    <th>Lainnya</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(row, i) in detailItems" :key="row.id">
                                    <td>@{{ i+1 }}</td>
                                    <td>@{{ row.tanggal_pantau }}</td>
                                    <td>@{{ row.luas_padi }}</td>
                                    <td>@{{ row.luas_palawija }}</td>
                                    <td>@{{ row.luas_lainnya }}</td>
                                    <td>
                                        @{{ Number(row.luas_padi) + Number(row.luas_palawija) + Number(row.luas_lainnya) }}
                                    </td>
                                </tr>
                                <!-- TOTAL KESELURUHAN -->
                                <tr v-if="detailItems.length > 0" class="fw-bold table-success">
                                    <td colspan="2" class="text-end">TOTAL</td>
                                    <td>@{{ formatAngka(totalDetail.padi) }}</td>
                                    <td>@{{ formatAngka(totalDetail.palawija) }}</td>
                                    <td>@{{ formatAngka(totalDetail.lainnya) }}</td>
                                    <td>@{{ formatAngka(totalDetail.total) }}</td>
                                </tr>

                                <tr v-if="detailItems.length === 0">
                                    <td colspan="6" class="text-center text-muted">
                                        Tidak ada data
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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
                    komir: null,
                    forms: [],
                    item: {},
                    modalInstance: null,
                    daerahIrigasis: [],
                    filteredItems: [],
                    permasalahans: [],
                    filterDi: '',
                    filterDi: '',
                    filterTanggalAwal: '',
                    filterTanggalAkhir: '',
                    pagination: {
                        current: 1,
                        last: 1,
                        total: 0,
                    },
                    perPage: 10, // default,
                    paginationPermasalahan: {
                        current: 1,
                        last: 1,
                        total: 0,
                    },
                    is_filtered: false,
                    is_loading: false,
                    activeTab: 'dashboard', // default tab yang aktif saat halaman dibuka,
                    rekapItems: [],
                    chartDI: null,
                    chartItem: null,
                    filterDI: '',
                    isFilter: false,
                    rekap: [],
                    rekapLuasTotal: [],
                    rekapLuasTanam: [],
                    isChild: false,
                    filterDIChild: '', // ✅ tambahkan ini
                    rekapPermasalahan: [],
                    diId: '',
                    daerahIrigasisChild: [],
                    selectedIndukDI: '',
                    rekapMasaTanam: [], // ✅ penampung data hasil API
                    isLoadingRekap: false,
                    filterTahun: new Date().getFullYear(), // default otomatis
                    maxPerMt: {},
                    topPerMt: [],
                    detailItems: [],
                    selectedTanggal: '',
                    detailModal: null,
                    totalDetail: {
                        padi: 0,
                        palawija: 0,
                        lainnya: 0,
                        total: 0
                    },
                    skMasaTanam: ''

                }
            },
            methods: {
                clearData() {
                    this.isFilter = false
                    this.rekap = []
                    this.rekapLuasTanam = []
                    this.rekapLuasTotal = []
                    // Reset
                    this.maxPerMt = {};
                    this.topPerMt = [];
                },
                async checkChild() {
                    this.clearData()
                    this.selectedDI = ''
                    if (!this.filterDI) {
                        this.isChild = false
                        this.filterDIChild = ''
                        return
                    }

                    let res = await axios.get(`/api/master/daerah-irigasi?id=${this.filterDI}`)
                    let di = res.data
                    console.log(di);


                    if (di.children && di.children.length > 0) {
                        this.daerahIrigasisChild = di.children
                        console.log(this.daerahIrigasisChild);

                        this.isChild = true
                        this.filterDIChild = ''

                    } else {
                        this.isChild = false
                        this.filterDIChild = ''
                    }
                },
                async loadData(diId) {
                    try {
                        let url = `/api/form-pengisian?page=all&di_id=${diId}`;
                        if (this.filterTanggalAwal) url += `&tanggal_awal=${this.filterTanggalAwal}`;
                        if (this.filterTanggalAkhir) url += `&tanggal_akhir=${this.filterTanggalAkhir}`;

                        let res = await axios.get(url);
                        this.items = res.data.data;
                        this.filteredItems = res.data;
                        console.log(this.filteredItems);
                        this.loadRekap(diId)

                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.is_loading = false;
                    }
                },

                async loadRekap(diId) {
                    let url = `/api/master/rekap-data?di_id=${diId}`
                    axios.get(url).then(res => {
                        console.log(res);
                        this.rekap = res.data
                    });


                    this.loadRekapPengisian(1)


                },
                async loadRekapPengisian(page = 1) {
                    // alert(page)
                    let url = `/api/rekap-petak?di_id=${this.diId}&page=${page}&per_page=${this.perPage}`
                    if (this.filterTanggalAwal) url += `&tanggal_awal=${this.filterTanggalAwal}`;
                    if (this.filterTanggalAkhir) url += `&tanggal_akhir=${this.filterTanggalAkhir}`;

                    axios.get(url).then(res => {
                        console.log(res.data);
                        this.rekapLuasTanam = res.data.data
                        this.pagination = {
                            current: res.data.current_page,
                            last: res.data.last_page,
                            total: res.data.total,
                        };
                    });

                    url = `/api/rekap-di?di_id=${this.diId}`
                    if (this.filterTanggalAwal) url += `&tanggal_awal=${this.filterTanggalAwal}`;
                    if (this.filterTanggalAkhir) url += `&tanggal_akhir=${this.filterTanggalAkhir}`;

                    axios.get(url).then(res => {
                        const data = res.data.total_luas || {};

                        // Ubah nilai ke number (hilangkan titik ribuan, ubah koma jadi titik)
                        const parseNumber = (val) => {
                            if (!val) return 0;
                            return parseFloat(val.replace(/\./g, '').replace(',', '.')) || 0;
                        };

                        this.rekapLuasTotal = {
                            padi: parseNumber(data.padi),
                            palawija: parseNumber(data.palawija),
                            lainnya: parseNumber(data.lainnya),
                            total: parseNumber(data.total),
                        };

                        console.log("Data konversi:", this.rekapLuasTotal);

                        // Panggil chart setelah data siap
                        this.chartPerItem();
                    });

                },
                bulanIndo(angka) {
                    const bulan = [
                        "", "Januari", "Februari", "Maret", "April",
                        "Mei", "Juni", "Juli", "Agustus", "September",
                        "Oktober", "November", "Desember"
                    ];
                    return bulan[angka];
                },

                formatTanggalIndo(tanggal) {
                    const options = {
                        timeZone: "Asia/Makassar",
                        day: "2-digit",
                        month: "long",
                        year: "numeric",
                    };
                    return new Date(tanggal).toLocaleString("id-ID", options);
                },

                showForm(form) {
                    this.item = form;
                    // console.log(this.item);

                    const modalEl = document.getElementById('formLTTModal');
                    this.modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
                    this.modalInstance.show();
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
                formatNumber(val) {
                    if (!val) return '0';
                    return parseFloat(val).toLocaleString('id-ID', {
                        minimumFractionDigits: 2
                    });
                },
                logout() {
                    this.komir = null;
                    localStorage.removeItem("komir");

                    this.kode = "";
                    this.forms = [];
                },
                applyFilter() {
                    let diId = this.isChild ? this.filterDIChild : this.filterDI
                    // alert(diId);
                    if (this.isChild) {
                        this.selectedDI = this.daerahIrigasisChild.find(d => d.id === this.filterDIChild) || null;
                        this.selectedIndukDI = this.daerahIrigasis.find(d => d.id === this.filterDI) || null;

                    } else {
                        this.selectedDI = this.daerahIrigasis.find(d => d.id === this.filterDI) || null;

                    }
                    if (!diId) {
                        alert("Pilih Daerah Irigasi terlebih dahulu")
                        return
                    }
                    this.diId = diId
                    this.loadData(diId)
                    this.loadRekapPermasalahan(diId)
                    this.loadRekapMasaTanamDi(diId)
                    this.isFilter = true
                },
                // async loadRekapMasaTanamDi(diId) {
                //     if (!diId) return;

                //     this.isLoadingRekap = true;

                //     try {
                //         const res = await axios.get(`/api/rekap-masa-tanam`, {
                //             params: {
                //                 di_id: diId,
                //                 tahun: this.filterTahun
                //             }
                //         });

                //         this.rekapMasaTanam = res.data;

                //         console.log('Rekap masa tanam:', this.rekapMasaTanam);

                //     } catch (err) {
                //         console.error(err);
                //         alert('Gagal memuat rekap masa tanam');
                //     } finally {
                //         this.isLoadingRekap = false;
                //     }
                // },
                async loadRekapMasaTanamDi(diId) {
                    if (!diId || !this.filterTanggalAwal || !this.filterTanggalAkhir) {
                        alert('Lengkapi filter dulu');
                        return;
                    }

                    let url = `/api/rekap-mingguan?di_id=${diId}&tanggal_mulai=${this.filterTanggalAwal}&tanggal_selesai=${this.filterTanggalAkhir}`;
                    const res = await axios.get(url);
                    this.items = res.data.rekap;
                    console.log(res.data);
                    this.skMasaTanam = res.data.masaTanamSk
                    // Reset
                    this.maxPerMt = {};
                    this.topPerMt = [];

                    // Hitung nilai tertinggi per MT
                    this.items.forEach(row => {
                        const mt = row.masa_tanam;
                        const total = Number(row.total_luas);

                        if (!this.maxPerMt[mt]) {
                            this.maxPerMt[mt] = total;
                        } else {
                            if (total > this.maxPerMt[mt]) {
                                this.maxPerMt[mt] = total;
                            }
                        }
                    });

                    // Ambil hanya data tertinggi per MT
                    Object.keys(this.maxPerMt).forEach(mt => {
                        const row = this.items.find(r =>
                            r.masa_tanam === mt &&
                            Number(r.total_luas) === this.maxPerMt[mt]
                        );

                        if (row) {
                            this.topPerMt.push(row);
                        }
                    });


                    // hitung max per masa tanam
                    this.items.forEach(row => {
                        const mt = row.masa_tanam;

                        if (!this.maxPerMt[mt]) {
                            this.maxPerMt[mt] = Number(row.total_luas);
                        } else {
                            if (Number(row.total_luas) > this.maxPerMt[mt]) {
                                this.maxPerMt[mt] = Number(row.total_luas);
                            }
                        }
                    });

                },

                applyFilterPermasalahan() {
                    this.loadPermasalahan(1)
                },
                resetFilter() {
                    this.filterDI = ''
                    this.filterTanggalAwal = ''
                    this.filterTanggalAkhir = ''
                    this.filteredItems = []
                    this.permasalahans = []
                    this.loadDashboard()
                },
                syncTanggal() {
                    this.filterTanggalAkhir = this.filterTanggalAwal;
                },

                chartPerItem() {
                    const data = this.topPerMt;
                    if (!data || data.length === 0) return;

                    if (this.chartItem) this.chartItem.destroy();

                    const ctx = document.getElementById('chartItem');
                    if (!ctx) return;

                    // Label sumbu X
                    const labels = data.map(item => `MT ${item.masa_tanam}`);

                    // Data per kategori
                    const padi = data.map(item => item.padi);
                    const palawija = data.map(item => item.palawija);
                    const lainnya = data.map(item => item.lainnya);

                    this.chartItem = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels,
                            datasets: [{
                                    label: 'Padi',
                                    data: padi,
                                    backgroundColor: 'rgba(75, 192, 192, 0.7)'
                                },
                                {
                                    label: 'Palawija',
                                    data: palawija,
                                    backgroundColor: 'rgba(255, 205, 86, 0.7)'
                                },
                                {
                                    label: 'Lainnya',
                                    data: lainnya,
                                    backgroundColor: 'rgba(201, 90, 90, 0.7)'
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                },
                                title: {
                                    display: true,
                                    text: 'Luas Tanam per Masa Tanam'
                                },
                                tooltip: {
                                    callbacks: {
                                        label(context) {
                                            const value = context.parsed.y.toLocaleString('id-ID', {
                                                minimumFractionDigits: 2
                                            });
                                            return `${context.dataset.label}: ${value} ha`;
                                        }
                                    }
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



                async loadDI() {
                    // let res = await axios.get('/api/master/daerah-irigasi?page=all&kabupaten_id=9');
                    let res = await axios.get('/api/master/daerah-irigasi?page=all&&kabupaten_id=9&is_induk=1');

                    console.log(res.data.data);
                    this.daerahIrigasis = res.data.data;
                },
                async loadRekapPermasalahan(diId) {
                    let url = `/api/rekap-permasalahan?pengamat_valid=1`;
                    if (this.filterDI) url += `&di_id=${diId}`;
                    if (this.filterTanggalAwal) url += `&tanggal_awal=${this.filterTanggalAwal}`;
                    if (this.filterTanggalAkhir) url += `&tanggal_akhir=${this.filterTanggalAkhir}`;

                    let res = await axios.get(url);

                    this.rekapPermasalahan = res.data.data;
                    this.totalKeseluruhan = res.data.total_keseluruhan;
                },
                formatAngka(val) {
                    return Number(val).toLocaleString('id-ID', {
                        minimumFractionDigits: 2
                    });
                },
                async openDetail(diId, tanggal) {
                    this.selectedTanggal = tanggal;

                    let url = `/api/rekap-mingguan-detail?di_id=${diId}&tanggal=${tanggal}`;
                    const res = await axios.get(url);

                    this.detailItems = res.data;

                    // reset total
                    let padi = 0;
                    let palawija = 0;
                    let lainnya = 0;

                    // hitung total
                    this.detailItems.forEach(row => {
                        padi += Number(row.luas_padi);
                        palawija += Number(row.luas_palawija);
                        lainnya += Number(row.luas_lainnya);
                    });

                    let total = padi + palawija + lainnya;

                    this.totalDetail = {
                        padi: padi,
                        palawija: palawija,
                        lainnya: lainnya,
                        total: total
                    };

                    // buka modal
                    const modalEl = document.getElementById('detailModal');
                    this.detailModal = new bootstrap.Modal(modalEl);
                    this.detailModal.show();
                },

            },
            mounted() {
                // this.loadDashboard();
                this.loadDI()

            }
        }).mount("#app");
    </script>
</body>

</html>