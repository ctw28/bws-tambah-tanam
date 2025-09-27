<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pengisian</title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="https://sda.pu.go.id/web/images/favicon.png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

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
        <form @submit.prevent="submitForm">
            <div class="card shadow-lg pb-2">
                <div class="card-body">

                    <div class="col-12 ">
                        <a href="/">
                            <span class="tf-icons bx bx-arrow-left"></span>&nbsp; Kembali ke halaman depan
                        </a>
                    </div>

                    <h3 class="my-3">Form Pengisian Pemantauan Luas Tambah Tanam</h3>
                    <!-- Lokasi -->

                    <div class="mb-3">
                        <label class="form-label">Kabupaten</label>
                        <select class="form-select" v-model="form.kabupaten_id" @change="checkKabupaten">
                            <option value="">-- Pilih --</option>
                            <option v-for="k in kabupaten" :value="k.id">@{{ k.nama }}</option>
                        </select>
                        <input v-if="form.daerah_irigasi_id === 'lain'" type="text" class="form-control mt-2"
                            placeholder="Isi manual daerah irigasi" v-model="form.daerah_irigasi_lain">
                    </div>
                    <!-- Daerah Irigasi -->
                    <div class="mb-3">
                        <label class="form-label">Daerah Irigasi</label>
                        <select class="form-select" v-model="form.daerah_irigasi_id" @change="checkIrigasi">
                            <option value="">-- Pilih --</option>
                            <option v-for="d in daerahIrigasi" :value="d.id">@{{ d.nama }}</option>
                        </select>
                    </div>
                    <!-- Saluran -->
                    <div class="mb-3">
                        <label class="form-label">Saluran</label>
                        <select class="form-select" v-model="form.saluran_id" @change="checkSaluran">
                            <option value="">-- Pilih --</option>
                            <option v-for="s in salurans" :value="s.id">@{{ s.nama }}</option>
                        </select>
                    </div>
                </div>
                <div v-if="showForm">
                    <div class="card shadow-lg p-4 mt-1">
                        <h3 class="mb-3">Detail Pengisian Petugas : @{{petugas_nama}}</h3>

                        <!-- Tanggal -->
                        <div class="mb-3">
                            <label class="form-label">Tanggal Pantau</label>
                            <input type="date" class="form-control" v-model="form.tanggal_pantau" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Kecamatan</label>
                                <input type="text" class="form-control" v-model="form.kecamatan">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Desa</label>
                                <input type="text" class="form-control" v-model="form.desa">
                            </div>
                        </div>

                        <!-- Saluran -->
                        <div class="mb-3">
                            <label class="form-label">Bangunan</label>
                            <select class="form-select" v-model="form.bangunan_id" @change="checkBangunan">
                                <option value="">-- Pilih --</option>
                                <option v-for="s in bangunans" :value="s.id">@{{ s.nama }}</option>
                            </select>
                        </div>
                        <!-- Petak -->
                        <div class="mb-3">
                            <label class="form-label">Petak</label>
                            <select class="form-select" v-model="form.petak_id" @change="checkPetak">
                                <option value="">-- Pilih --</option>
                                <option v-for="p in petaks" :value="p.id">@{{ p.nama }}</option>
                            </select>

                            <!-- Preview Gambar -->
                            <div v-if="form.petak_gambar" class="mt-3">
                                <p class="fw-bold">Gambar Petak:</p>
                                <img :src="form.petak_gambar" class="img-fluid rounded shadow" alt="Gambar Petak">
                            </div>
                        </div>



                        <!-- Koordinat -->
                        <div class="mb-3">
                            <label class="form-label">Koordinat</label>
                            <input type="text" class="form-control" v-model="form.koordinat"
                                placeholder="-3.98123, 122.5123">
                        </div>

                        <!-- Debit & Masa Tanam -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Debit Air (lt/det)</label>
                                <input type="number" step="0.01" class="form-control" v-model="form.debit_air">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Masa Tanam</label>
                                <select class="form-select" v-model="form.masa_tanam" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="I">I</option>
                                    <option value="II">II</option>
                                    <option value="III">III</option>
                                </select>
                            </div>
                        </div>

                        <!-- Luas -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Luas Padi (Ha)</label>
                                <input type="number" step="0.01" class="form-control" v-model="form.luas_padi">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Luas Palawija (Ha)</label>
                                <input type="number" step="0.01" class="form-control" v-model="form.luas_palawija">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Luas Lainnya (Ha)</label>
                                <input type="number" step="0.01" class="form-control" v-model="form.luas_lainnya">
                            </div>
                        </div>
                        <h4>Pemantauan Permasalahan</h4>
                        <div v-for="p in permasalahans" :key="p.id" class="mb-3">
                            <label class="form-label d-block">@{{ p.nama }}</label>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" :name="'permasalahan_'+p.id" value="ada"
                                    v-model="form.permasalahan[p.id].status">
                                <label class="form-check-label">Ada</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" :name="'permasalahan_'+p.id" value="tidak"
                                    v-model="form.permasalahan[p.id].status" checked>
                                <label class="form-check-label">Tidak Ada</label>
                            </div>

                            <!-- tampilkan keterangan kalau ada permasalahan -->
                            <textarea v-if="form.permasalahan[p.id].status === 'ada'"
                                v-model="form.permasalahan[p.id].keterangan" class="form-control mt-2" rows="2"
                                placeholder="Jelaskan permasalahannya..."></textarea>
                        </div>



                        <!-- Upload Foto pemantauan -->
                        <div class="mb-3">
                            <label class="form-label">Upload Foto Pemantauan (foto dengan Koordinat)</label>
                            <input type="file" class="form-control" @change="handleFile">
                        </div>
                        <div v-if="previewFoto" class="mb-3">
                            <h4>Foto Pemantauan</h4>
                            <img :src="previewFoto" alt="Preview Foto" class="img-fluid rounded" width="300">
                        </div>
                        <!-- Submit -->
                        <button type="button" class="btn btn-primary" @click="openForm">Submit</button>
                    </div>
                </div>
            </div>

        </form>

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
                                @{{ petugas_nama || '-' }}</div>
                            <div class="form-line"><span>Tanggal Pemantauan</span>:
                                @{{ formatTanggal(form?.tanggal_pantau) }}</div>
                            <div class="form-line"><span>Daerah Irigasi</span>:
                                @{{selectedDINama}}</div>
                            <div class="form-line"><span>Desa/Kelurahan</span>: @{{form.desa}}</div>
                            <div class="form-line"><span>Kecamatan</span>: @{{form.kecamatan}}</div>
                            <div class="form-line"><span>Kabupaten/Kota</span>:
                                @{{selectedKabupatenNama}}</div>
                            <div class="form-line"><span>Nama Saluran (Sekunder/Primer)</span>:
                                @{{selectedSaluranNama}}</div>
                            <div class="form-line"><span>Nama Bangunan Bagi/Sadap</span>:
                                @{{selectedBangunanNama}}</div>
                            <div class="form-line"><span>Kode/Nama Petak Layanan</span>:
                                @{{selectedPetakNama}}</div>
                            <div class="form-line"><span>Koordinat Bangunan Bagi/Sadap</span>: @{{form.koordinat}}
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
                                        <td>@{{ form.debit_air}}</td>
                                        <td>@{{ petak_luas}}</td>
                                        <td>@{{ form.luas_padi}}</td>
                                        <td>@{{ form.luas_palawija}}</td>
                                        <td>@{{ form.luas_lainnya}}</td>
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
                                <tbody v-for="(p,index) in form.permasalahan" :key="p.id">
                                    <tr>
                                        <td>@{{ index}}</td>
                                        <td>@{{ permasalahans.find(pm => pm.id == p.master_permasalahan_id)?.nama }}
                                        </td>
                                        <td class="text-center">
                                            <span v-if="p.status=='ada'">Ada</span>
                                        </td>
                                        <td>@{{ p.keterangan}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-if="previewFoto" class="mt-3">
                            <h4>Foto Pemantauan</h4>
                            <img :src="previewFoto" alt="Preview Foto" class="img-fluid rounded" width="300">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button class="btn btn-primary" @click="submitForm">Kirim Formulir</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.prod.js"></script>
    <script>
        const {
            createApp
        } = Vue;

        createApp({
            data() {
                return {
                    kabupaten: [],
                    daerahIrigasi: [],
                    salurans: [],
                    bangunans: [],
                    petaks: [],
                    permasalahans: [], // ambil dari API form_permasalahans
                    petugas: [], // ambil dari API petugas
                    showForm: false,
                    petugas_nama: '',
                    petak_luas: 0,
                    form: {
                        sesi_id: 1,
                        petugas_id: '',
                        tanggal_pantau: new Date().toISOString().slice(0, 10), // hasil: "2025-09-17"
                        kabupaten_id: '',
                        daerah_irigasi_id: '',
                        saluran_id: '',
                        bangunan_id: '',
                        petak_id: '',
                        kecamatan: '',
                        desa: '',
                        koordinat: '',
                        debit_air: 0,
                        masa_tanam: '',
                        luas_padi: 0,
                        luas_palawija: 0,
                        luas_lainnya: 0,
                        permasalahan: {},
                        foto_pemantauan: '',
                    },
                    previewFoto: null,

                }
            },
            computed: {
                selectedDINama() {
                    let di = this.daerahIrigasi.find(d => d.id == this.form.daerah_irigasi_id);
                    return di ? di.nama : '';
                },
                selectedKabupatenNama() {
                    let data = this.kabupaten.find(d => d.id == this.form.kabupaten_id);
                    return data ? data.nama : '';
                },
                selectedSaluranNama() {
                    let data = this.salurans.find(d => d.id == this.form.saluran_id);
                    return data ? data.nama : '';
                },
                selectedBangunanNama() {
                    let data = this.bangunans.find(d => d.id == this.form.bangunan_id);
                    return data ? data.nama : '';
                },
                selectedPetakNama() {
                    let data = this.petaks.find(d => d.id == this.form.petak_id);
                    return data ? data.nama : '';
                },
            },
            methods: {
                async checkKabupaten() {
                    if (this.form.kabupaten_id) {
                        let res = await axios.get(
                            `/api/master/daerah-irigasi?kabupaten_id=${this.form.kabupaten_id}`);
                        this.daerahIrigasi = res.data;
                    } else {
                        this.daerahIrigasi = [];
                    }
                    this.form.daerah_irigasi_id = '';
                    this.form.petugas_id = '';
                    this.form.saluran_id = '';
                    this.form.petak_id = '';
                    this.petugas = [];
                    this.salurans = [];
                    this.petaks = [];
                },
                async checkIrigasi() {
                    if (this.form.daerah_irigasi_id) {
                        let res = await axios.get(
                            `/api/master/saluran?daerah_irigasi_id=${this.form.daerah_irigasi_id}`);
                        this.salurans = res.data;
                    } else {
                        this.salurans = [];
                    }
                    console.log(this.salurans);

                    this.form.saluran_id = '';
                    this.form.petak_id = '';
                    this.bangunans = [];
                    this.petaks = [];
                },

                async checkSaluran() {
                    if (this.form.saluran_id) {
                        // minta kode dulu
                        const saluran = this.salurans.find(s => s.id === this.form.saluran_id);
                        console.log(saluran.petugas)
                        if (saluran && saluran.petugas) {
                            this.form.petugas_id = saluran.petugas[0].id
                            this.petugas_nama = saluran.petugas[0].nama
                        } else {
                            this.form.petugas_id = '';
                            this.petugas_nama = '';
                        }
                        const kode = prompt("Masukkan kode petugas:");
                        if (!kode) {
                            this.form.saluran_id = '';
                            this.form.petugas_id = '';
                            this.petugas_nama = '';
                            this.showForm = false;
                            return;
                        }

                        try {
                            // cek kode ke server
                            await axios.post('/api/petugas/validasi-kode', {
                                saluran_id: this.form.saluran_id,
                                petugas_id: this.form.petugas_id, // 🔹 sudah ada
                                kode: kode
                            });

                            // kalau sukses → ambil saluran
                            let res = await axios.get(
                                `/api/master/bangunan?saluran_id=${this.form.saluran_id}`);
                            this.bangunans = res.data;

                            // reset field turunan
                            this.form.bangunan_id = '';
                            this.form.petak_id = '';
                            this.form.kecamatan = '';
                            this.form.desa = '';
                            this.form.koordinat = '';
                            this.form.luas_padi = 0;
                            this.form.luas_palawija = 0;
                            this.form.luas_lainnya = 0;
                            this.form.debit_air = 0;
                            this.form.masa_tanam = '';
                            this.getMasterPermasalahan();

                            this.petaks = [];
                            this.showForm = true; // tampilkan form lanjutan
                        } catch (err) {
                            alert("Kode salah, atau pengguna sudah dinon aktifkan!, hubungi koordinator anda.");
                            // reset kembali dropdown dan field
                            this.form.saluran_id = '';

                            this.petaks = [];
                            this.bangunans = [];
                            this.showForm = false;
                        }
                    } else {
                        // kalau kembali ke "-- Pilih --"
                        this.petaks = [];
                        this.bangunans = [];

                        this.form.bangunan_id = '';
                        this.form.petak_id = '';
                        this.form.kecamatan = '';
                        this.form.desa = '';
                        this.form.koordinat = '';
                        this.form.luas_padi = 0;
                        this.form.luas_palawija = 0;
                        this.form.luas_lainnya = 0;
                        this.form.debit_air = 0;
                        this.form.masa_tanam = '';
                        this.getMasterPermasalahan();

                        this.showForm = false;
                        // alert('gg')
                    }
                },
                async checkBangunan() {
                    if (this.form.bangunan_id) {
                        let res = await axios.get(`/api/master/petak?bangunan_id=${this.form.bangunan_id}`);
                        this.petaks = res.data;
                    } else {
                        this.petaks = [];
                    }
                    this.form.petak_id = '';
                },
                checkPetak() {
                    // if (this.form.petak_id && this.form.petak_id !== 'lain') {
                    let petak = this.petaks.find(p => p.id == this.form.petak_id);
                    console.log(petak);

                    if (petak && petak.gambar_skema) {
                        this.form.foto_pemantauan = petak.gambar_skema;
                    } else {
                        this.form.foto_pemantauan = null;
                        this.petak_luas = petak.luas
                    }
                    // } else {
                    //     this.form.petak_gambar = null;
                    // }
                },
                async getKabupaten() {
                    let res = await axios.get('/api/master/kabupaten');
                    this.kabupaten = res.data;
                },
                async getMasterPermasalahan() {
                    // contoh fetch data dari API
                    axios.get('/api/master/permasalahan').then(res => {
                        this.permasalahans = res.data;

                        this.permasalahans.forEach(p => {
                            // Vue 3: langsung assign, tidak perlu $set
                            if (!this.form.permasalahan[p.id]) {
                                this.form.permasalahan[p.id] = {
                                    master_permasalahan_id: p.id,
                                    status: '',
                                    keterangan: ''
                                };

                            }
                        });
                    });
                },
                handleFile(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.form.foto_pemantauan = file;
                        this.previewFoto = URL.createObjectURL(file); // buat URL sementara
                    }
                },
                openForm() {
                    console.log(this.form);
                    if (!this.validateForm()) {
                        // alert('pastikan semua data terisi')
                        return; // stop kalau tidak valid
                    }
                    new bootstrap.Modal(document.getElementById('formLTTModal')).show();
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
                async submitForm() {
                    // Validasi sederhana

                    console.log(this.form);
                    if (!this.validateForm()) {
                        // alert('pastikan semua data terisi')
                        return; // stop kalau tidak valid
                    }
                    const konfirm = confirm(
                        'yakin simpan? pastikan semua data suah terisi dengan benar dan sesuai')
                    if (!konfirm) return
                    const formData = new FormData();
                    for (let key in this.form) {
                        if (key === 'permasalahan') {
                            formData.append(key, JSON.stringify(this.form[key]));
                        } else if (key === 'foto_pemantauan' && this.form[key]) {
                            formData.append(key, this.form[key]);
                        } else if (this.form[key] !== null && this.form[key] !== '') {
                            formData.append(key, this.form[key]);
                        }
                    }

                    try {
                        let res = await axios.post('/api/form-pengisian', formData, {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        });
                        console.log(res);
                        // return
                        alert('Data berhasil disimpan')
                        this.resetForm()
                        let modalEl = document.getElementById('formLTTModal');
                        let modal = bootstrap.Modal.getInstance(modalEl); // ambil instance yang sudah aktif
                        if (modal) {
                            modal.hide();
                        }
                    } catch (error) {
                        console.error('Error submitting form:', error.response ? error.response.data : error
                            .message);
                        const errorMessage = error.response && error.response.data.message ? error.response
                            .data
                            .message : 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.';
                        alert(errorMessage)
                    }
                },
                resetForm() {
                    this.form = {
                        sesi_id: 1,
                        petugas_id: '',
                        tanggal_pantau: new Date().toISOString().slice(0, 10), // tetap default hari ini
                        kabupaten_id: '',
                        daerah_irigasi_id: '',
                        saluran_id: '',
                        bangunan_id: '',
                        petak_id: '',
                        kecamatan: '',
                        desa: '',
                        koordinat: '',
                        debit_air: '',
                        masa_tanam: '',
                        luas_padi: 0,
                        luas_palawija: 0,
                        luas_lainnya: 0,
                        permasalahan: {},
                        foto_pemantauan: '',
                    }
                    this.previewFoto = null
                    this.showForm = false
                    this.getMasterPermasalahan();

                },
                async mintaKode() {
                    if (!this.form.petugas_id) return;

                    const kode = prompt("Masukkan kode petugas:");
                    if (!kode) {
                        this.form.petugas_id = '';
                        this.form.saluran_id = '';
                        return;
                    }

                    try {
                        await axios.post('/api/petugas/validasi-kode', {
                            petugas_id: this.form.petugas_id,
                            saluran_id: this.form.saluran_id,
                            kode: kode
                        });
                        alert("Kode benar, silakan isi form.");
                    } catch (err) {
                        alert("Kode salah!");
                        this.form.petugas_id = '';
                    }
                },
                validateForm() {
                    console.log(this.form.foto_pemantauan);

                    // cek semua key di form
                    for (let [key, value] of Object.entries(this.form)) {
                        if (key !== 'foto_pemantauan' && (value === '' || value === null)) {
                            alert(`Field ${key} wajib diisi!`);
                            return false;
                        }
                    }

                    // validasi khusus foto
                    if (this.form.foto_pemantauan === "-") {
                        alert("Foto pemantauan wajib diisi!");
                        return false;
                    }


                    return true; // valid
                },

            },
            mounted() {
                this.getKabupaten();
                this.getMasterPermasalahan();
            },
        }).mount('#app')
    </script>
</body>

</html>