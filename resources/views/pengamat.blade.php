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
                <div class="card-body">
                    <h5 class="mb-3">Daftar Form Pengisian - @{{ pengamat.daerah_irigasi.nama }}</h5>
                    <!-- Filter tanggal -->
                    <div class="mb-3">
                        <div class="row g-2">
                            <!-- Input tanggal awal -->
                            <div class="col-6 col-md-3">
                                <input type="date" v-model="filterTanggalAwal" @change="syncTanggal"
                                    class="form-control" />
                            </div>
                            <!-- Input tanggal akhir -->
                            <div class="col-6 col-md-3">
                                <input type="date" v-model="filterTanggalAkhir" class="form-control" />
                            </div>
                            <!-- Tombol (hanya di layar md ke atas) -->
                            <div class="col-md-6 d-none d-md-flex gap-2">
                                <button class="btn btn-primary " @click="applyFilter">Filter</button>
                                <button class="btn btn-secondary" @click="resetFilter">Reset</button>
                            </div>
                        </div>

                        <!-- Tombol (khusus HP, tampil di bawah input tanggal) -->
                        <div class="d-flex gap-2 mt-2 d-md-none">
                            <button class="btn btn-primary " @click="applyFilter">Filter</button>
                            <button class="btn btn-secondary btn-sm" @click="resetFilter">Reset</button>
                        </div>
                    </div>
                    <div class="table-responsive">

                        <table class="table table-bordered table-sm">
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
                                    <td>@{{index+1}}</td>
                                    <td>@{{ formatTanggal(f.tanggal_pantau) }}</td>
                                    <td>@{{ f.petugas.nama }}</td>
                                    <td>@{{ f.saluran.nama }}</td>
                                    <td class="text-center">
                                        <span v-if="f.validasi && f.validasi.pengamat_valid">✅
                                            Valid</span>
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

                        <p v-if="forms.length === 0" class="text-muted text-center mt-2">Belum ada form pengisian untuk
                            divalidasi.</p>
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
                                            <th>Ketarngan</th>
                                        </tr>
                                    </thead>
                                    <tbody v-for="(p,index) in item.permasalahan" :key="p.id">
                                        <tr>
                                            <td>@{{ index}}</td>
                                            <td>@{{ p.master_permasalahan.nama }}
                                            </td>
                                            <td class="text-center">
                                                <span v-if="p.status==1">Ada</span>
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
                            <button v-if="!item.form_validasi || !item.form_validasi.pengamat_valid"
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
                    forms: [],
                    item: {},
                    modalInstance: null,
                    filteredItems: [],
                    filterTanggalAwal: new Date().toISOString().slice(0, 10),
                    filterTanggalAkhir: new Date().toISOString().slice(0, 10),
                }
            },
            methods: {
                async cekPengamat() {
                    try {
                        let res = await axios.post("/api/pengamat/validasi-kode", {
                            kode: this.kode
                        });
                        console.log(res);

                        this.pengamat = res.data.pengamat;
                        console.log(this.pengamat);
                        localStorage.setItem("pengamat", JSON.stringify(res.data.pengamat));


                        this.loadData()

                    } catch (e) {
                        alert("Kode pengamat tidak valid!");
                    }
                },
                async loadData() {
                    try {
                        let res = await axios.get(
                            `/api/form-pengisian?di_id=${this.pengamat.daerah_irigasi_id}`);
                        console.log(res);

                        this.forms = res.data;
                        this.filteredItems = res.data;
                    } catch (e) {
                        console.error(e);
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
                    if (!this.filterTanggalAwal || !this.filterTanggalAkhir) {
                        this.filteredItems = this.forms;
                    } else {
                        this.filteredItems = this.forms.filter(i =>
                            i.tanggal_pantau >= this.filterTanggalAwal &&
                            i.tanggal_pantau <= this.filterTanggalAkhir
                        );
                    }
                },
                resetFilter() {
                    this.filterTanggalAwal = new Date().toISOString().slice(0, 10);
                    this.filterTanggalAkhir = new Date().toISOString().slice(0, 10)
                    this.loadData()

                },
                syncTanggal() {
                    // kalau user pilih tanggal awal, otomatis set tanggal akhir sama
                    this.filterTanggalAkhir = this.filterTanggalAwal;
                },
                loadPengamat() {
                    let data = localStorage.getItem("pengamat");
                    if (data) {
                        this.pengamat = JSON.parse(data);
                        this.loadData()
                        // bisa optional: validasi token ke server
                    }
                },
            },
            mounted() {
                this.loadPengamat();
            }
        }).mount("#app");
    </script>
</body>

</html>