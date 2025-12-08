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

        .hover-bg:hover {
            background-color: #f8f9fa;
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
        <div v-if="!juru">
            <div class="card shadow-lg">
                <div class="card-body">
                    <h5 class="card-title mt-2">Masuk Juru</h5>
                    <div class="mb-3">
                        <label class="form-label">Masukkan Kode Juru</label>
                        <input type="text" v-model="kode" class="form-control" placeholder="Kode unik juru">
                    </div>
                    <button class="btn btn-primary" @click="cekJuru">Masuk
                </div>
            </div>
        </div>
        <div v-else>

            <form @submit.prevent="submitForm">

                <div class="card shadow-lg">
                    <div class="card-body text-center">


                        <h3 class="mb-3">Form Pengisian Pemantauan Luas Tambah Tanam</h3>
                        <!-- Lokasi -->
                        <h4 class="mb-3">Selamat datang, Juru - @{{petugas_nama}}</h4>
                        <button type="button" class="btn btn-outline-danger btn-sm" @click="keluarJuru">
                            <i class="bx bx-log-out"></i> Keluar
                        </button>
                    </div>
                </div>
                <div class="card shadow-lg pb-2 mt-2">
                    <div class="card-body">
                        <h5 class="my-3">I. Pilih Saluran, Lokasi dan Tanggal Pantau</h5>
                        <div class="mb-3">

                            <label class="form-label">Saluran <span class="text-danger">*</span></label>
                            <select class="form-select" v-model="form.saluran_id" @change="checkSaluran">
                                <option value="">-- Pilih --</option>
                                <option
                                    v-for="s in salurans"
                                    :key="s.id"
                                    :value="s.id">
                                    @{{ s.nama }} - DI @{{ s.daerah_irigasi.nama }} - Kab. @{{ s.daerah_irigasi.kabupatens[0].nama }}
                                </option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Bangunan <span class="text-danger">*</span></label>
                            <select class="form-select" v-model="form.bangunan_id" @change="checkBangunan"
                                :class="{ 'is-invalid': submitted && (!form.bangunan_id) }">
                                <option value="">-- Pilih --</option>
                                <option v-for="s in bangunans" :value="s.id">@{{ s.nama }}</option>
                            </select>
                            <div class="invalid-feedback">Bangunan wajib diisi.</div>

                        </div>
                        <!-- Petak -->
                        <div class="mb-3">
                            <label class="form-label">Petak <span class="text-danger">*</span></label>
                            <select class="form-select" v-model="form.petak_id" @change="checkPetak"
                                :class="{ 'is-invalid': submitted && (!form.petak_id) }">
                                <option value="">-- Pilih --</option>
                                <option v-for="p in petaks" :value="p.id">@{{ p.nama }}</option>
                            </select>
                            <div class="invalid-feedback">Petak wajib diisi.</div>

                            <!-- Preview Gambar -->
                            <div v-if="form.petak_id && previewPetak" class="mt-3">
                                <p class="fw-bold">Gambar Petak : </p>
                                <img :src="`/storage/${previewPetak}`"
                                    class="img-fluid rounded shadow"
                                    alt="Gambar Petak">
                                <small class="text-muted d-block mt-2">
                                    @{{ infoGambarSkema }}
                                </small>
                            </div>
                            <div v-else-if="form.petak_id && !previewPetak" class="mt-3">
                                <p class="fw-bold">Gambar Petak : - </p>
                                <small class="text-muted d-block mt-2">
                                    @{{ infoGambarSkema }}
                                </small>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Kecamatan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" v-model="form.kecamatan"
                                    :class="{ 'is-invalid': submitted && (!form.kecamatan || form.kecamatan.trim() === '') }">
                                <div class="invalid-feedback">Kecamtan wajib diisi.</div>

                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Desa <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" v-model="form.desa"
                                    :class="{ 'is-invalid': submitted && (!form.desa || form.desa.trim() === '') }">
                                <div class="invalid-feedback">Desa wajib diisi.</div>

                            </div>
                        </div>



                        <button
                            type="button"
                            class="btn btn-primary"
                            :disabled="!isReadyToContinue"
                            @click="showForm">
                            Lanjut <i class="bx bx-arrow-right"></i>
                        </button>

                    </div>
                </div>
                <div v-if="is_show_form">

                    <!-- Debit & Masa Tanam -->
                    <div class="card shadow-lg p-4 mt-1" id="showForm">

                        <h5 class="my-3">II. Pemantauan luas Tambah Tanam</h5>

                        <div class="row mb-3">
                            <!-- <div class="col-md-6">
                                <label class="form-label">Debit Air (lt/det)</label>
                                <input type="number" step="0.01" class="form-control" v-model="form.debit_air">
                            </div> -->
                            <div class="mb-3">
                                <label class="form-label">
                                    Tanggal Pantau <span class="text-danger">*</span>
                                </label>

                                <input type="date"
                                    class="form-control"
                                    v-model="form.tanggal_pantau"
                                    @change="validasiHariMinggu"
                                    :min="minDate"
                                    :max="maxDate"
                                    required>

                                <small class="text-muted">
                                    Hanya bisa memilih hari Minggu
                                </small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Masa Tanam (MT)<span class="text-danger">*</span></label>
                                <select class="form-select" v-model="form.masa_tanam"
                                    :class="{ 'is-invalid': submitted && (!form.masa_tanam || form.masa_tanam.trim() === '') }">
                                    <option value="">-- Pilih --</option>
                                    <option value="I">I</option>
                                    <option value="II">II</option>
                                    <option value="III">III</option>
                                </select>
                                <div class="invalid-feedback">Masa Tanam wajib diisi.</div>

                            </div>
                        </div>

                        <!-- Luas -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Luas Padi (Ha) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" v-model="form.luas_padi">
                                <small class="text-muted">Isi dengan luas komulatif padi</small>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Luas Palawija (Ha) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" v-model="form.luas_palawija">
                                <small class="text-muted">Isi dengan luas komulatif palawija</small>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Luas Lainnya (Ha) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" v-model="form.luas_lainnya">
                                <small class="text-muted">Isi dengan luas komulatif lainnya</small>
                            </div>

                        </div>
                        <!-- Upload Foto pemantauan -->
                        <div class="mb-3">
                            <label class="form-label">Upload Foto Pemantauan (foto dengan Koordinat) <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" @change="handleFile"
                                :class="{ 'is-invalid': submitted && (!form.foto_pemantauan || form.foto_pemantauan == '-') }">
                            <div class="invalid-feedback">Foto Pemantauan wajib diisi.</div>

                        </div>
                        <div v-if="previewFoto" class="mb-3">
                            <h4>Foto Pemantauan</h4>
                            <img :src="previewFoto" alt="Preview Foto" class="img-fluid rounded" width="300">
                        </div>
                    </div>
                    <div class="card shadow-lg p-4 mt-1">

                        <h5 class="my-3">III. Kelembagaan</h5>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">P3A</label>

                                <!-- input pencarian -->
                                <input type="text" class="form-control" v-model="p3aSearch"
                                    placeholder="Ketik minimal 3 huruf untuk mencari P3A..."
                                    @input="searchP3A">

                                <!-- dropdown hasil pencarian -->
                                <div v-if="showP3ASearch && p3aResults.length"
                                    class="border rounded mt-1 bg-white position-absolute w-50 shadow-sm"
                                    style="z-index: 1000;">
                                    <div v-for="p in p3aResults" :key="p.id"
                                        class="p-2 hover-bg"
                                        @click="selectP3A(p)"
                                        style="cursor:pointer;">
                                        @{{ p.nama }}
                                    </div>
                                </div>

                                <!-- daftar P3A terpilih -->
                                <div class="mt-2">
                                    <span v-for="(p, i) in form.p3a" :key="p.id"
                                        class="badge bg-primary me-2">
                                        @{{ p.nama }}
                                        <button type="button" class="btn-close btn-close-white btn-sm ms-1"
                                            style="font-size: .6rem;"
                                            @click="removeP3A(i)">
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="card shadow-lg p-4 mt-1">

                        <h5 class="my-3">IV. Pemantauan Permasalahan</h5>

                        <div v-for="(p,index) in permasalahans" :key="p.id" class="mb-4 pb-3">
                            <!-- Nama permasalahan -->
                            <label class="form-label d-block">@{{ index + 1 }}. @{{ p.nama }}</label>

                            <!-- Pilihan Ada / Tidak Ada -->
                            <div class="form-check form-check-inline">
                                <input
                                    class="form-check-input"
                                    type="radio"
                                    :name="'permasalahan_'+p.id"
                                    value="ada"
                                    v-model="form.permasalahan[p.id].status">
                                <label class="form-check-label">Ada</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input
                                    class="form-check-input"
                                    type="radio"
                                    :name="'permasalahan_'+p.id"
                                    value="tidak"
                                    v-model="form.permasalahan[p.id].status" checked>
                                <label class="form-check-label">Tidak Ada</label>
                            </div>

                            <!-- Keterangan wajib jika ada permasalahan -->
                            <textarea
                                v-if="form.permasalahan[p.id].status === 'ada'"
                                v-model="form.permasalahan[p.id].keterangan"
                                class="form-control mt-2"
                                rows="2"
                                placeholder="Jelaskan permasalahannya..."
                                :class="{'is-invalid':submitted &&(form.permasalahan[p.id].status === 'ada' && (!form.permasalahan[p.id].keterangan ||form.permasalahan[p.id].keterangan.trim() === ''))}"></textarea>
                            <div class="invalid-feedback">
                                Keterangan wajib diisi jika ada permasalahan.
                            </div>

                            <!-- Upload Foto (hanya muncul jika status 'ada') -->
                            <div v-if="form.permasalahan[p.id].status === 'ada'" class="mt-3">
                                <label class="form-label">
                                    Upload Foto Permasalahan <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="file"
                                    class="form-control"
                                    @change="handleFile($event, p.id)"
                                    :class="{'is-invalid':submitted &&form.permasalahan[p.id].status === 'ada' &&!form.permasalahan[p.id].foto_permasalahan}">
                                <div class="invalid-feedback">
                                    Foto permasalahan wajib diisi jika ada permasalahan.
                                </div>
                                <div v-if="form.permasalahan[p.id].foto_permasalahanPreview" class="mt-3">
                                    <img
                                        :src="form.permasalahan[p.id].foto_permasalahanPreview"
                                        alt="Preview Foto"
                                        class="img-fluid rounded"
                                        width="300">
                                </div>

                            </div>
                        </div>




                        <!-- Submit -->
                        <button type="button" class="btn btn-primary" @click="openForm">Review</button>
                    </div>

            </form>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="formLTTModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Review Form Pemantauan LTT</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <!-- Identitas -->
                        <div class="p-3 rounded shadow-sm bg-light mb-4">
                            <div class="mb-2">
                                <small class="text-muted d-block">Nama Petugas OP</small>
                                <span class="fw-semibold">@{{ petugas_nama || '-' }}</span>
                            </div>

                            <div class="mb-2">
                                <small class="text-muted d-block">Tanggal Pemantauan</small>
                                <span class="fw-semibold">@{{ formatTanggal(form?.tanggal_pantau) }}</span>
                            </div>

                            <div class="mb-2">
                                <small class="text-muted d-block">Daerah Irigasi</small>
                                <span class="fw-semibold">@{{ selectedDINama }}</span>
                            </div>

                            <div class="mb-2">
                                <small class="text-muted d-block">Desa/Kelurahan</small>
                                <span class="fw-semibold">@{{ form.desa }}</span>
                            </div>

                            <div class="mb-2">
                                <small class="text-muted d-block">Kecamatan</small>
                                <span class="fw-semibold">@{{ form.kecamatan }}</span>
                            </div>

                            <div class="mb-2">
                                <small class="text-muted d-block">Kabupaten/Kota</small>
                                <span class="fw-semibold">@{{ selectedKabupatenNama }}</span>
                            </div>

                            <div class="mb-2">
                                <small class="text-muted d-block">Nama Saluran (Sekunder/Primer)</small>
                                <span class="fw-semibold">@{{ selectedSaluranNama }}</span>
                            </div>

                            <div class="mb-2">
                                <small class="text-muted d-block">Nama Bangunan Bagi/Sadap</small>
                                <span class="fw-semibold">@{{ selectedBangunanNama }}</span>
                            </div>

                            <div class="mb-2">
                                <small class="text-muted d-block">Kode/Nama Petak Layanan</small>
                                <span class="fw-semibold">@{{ selectedPetakNama }}</span>
                            </div>
                            <!-- Tabel LTT -->
                            <hr class="mt-2">

                            <h5 class="fw-bold mt-3">Pemantauan Luas Tambah Tanam (LTT)</h5>
                            <div class="mb-2">
                                <small class="text-muted d-block">Luas Komulatif Padi</small>
                                <span class="fw-semibold">@{{ form.luas_padi }} ha</span>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted d-block">Luas Komulatif Palawija</small>
                                <span class="fw-semibold">@{{ form.luas_palawija }} ha</span>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted d-block">Luas Komulatif Lainnya</small>
                                <span class="fw-semibold">@{{ form.luas_lainnya }} ha</span>
                            </div>
                            <div v-if="previewFoto" class="mt-3">
                                <h4>Foto Pemantauan</h4>
                                <img :src="previewFoto" alt="Preview Foto" class="img-fluid rounded" width="300">
                            </div>
                            <!-- <div class="table-responsive mb-4">
                                <table class="table table-bordered text-center align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th rowspan="2">Luas Petak Skema (Ha)</th>
                                            <th colspan="3">Pemantauan Luas Tambah Tanam (Ha)</th>
                                        </tr>
                                        <tr>
                                            <th>Padi</th>
                                            <th>Palawija</th>
                                            <th>Lainnya</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>@{{ petak_luas }}</td>
                                            <td>@{{ form.luas_padi }}</td>
                                            <td>@{{ form.luas_palawija }}</td>
                                            <td>@{{ form.luas_lainnya }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div> -->
                            <hr class="mt-2">

                            <h5 class="fw-bold mt-3">Kelembagaan P3A</h5>

                            <div class="mb-3">
                                <template v-if="form.p3a && form.p3a.length">
                                    <span v-for="p in form.p3a" :key="p.id" class="badge bg-primary me-1 mb-1">
                                        @{{ p.nama }}
                                    </span>
                                </template>
                                <p v-else class="text-muted fst-italic">Data P3A tidak ada / tidak diisi.</p>
                            </div>


                            <!-- Tabel Permasalahan -->
                            <hr class="mt-2">

                            <h5 class="fw-bold">Permasalahan di Lapangan</h5>

                            <div class="mb-2" v-for="(p, index) in form.permasalahan" :key="p.id">
                                <span class="fw-semibold">
                                    <div v-if="p.status=='ada'">
                                        @{{ index }}. @{{ permasalahans.find(pm => pm.id == p.master_permasalahan_id)?.nama }} <br>
                                        <span class="text-danger fw-bold">
                                            Ada
                                        </span> <br>
                                        Keterangan : @{{ p.keterangan || '-' }}<br>
                                        Foto permasalahan :
                                        <img :src="p.foto_permasalahanPreview" alt="Preview Foto Permasalahan" class="img-fluid rounded" width="300">
                                    </div>

                                </span>
                            </div>


                        </div>

                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button class="btn btn-primary" @click="submitForm">Kirim Formulir</button>
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
                        juru: false,
                        kode: '',
                        kabupaten: [],
                        daerahIrigasi: [],
                        salurans: [],
                        bangunans: [],
                        petaks: [],
                        permasalahans: [], // ambil dari API form_permasalahans
                        petugas: [], // ambil dari API petugas
                        is_show_form: false,
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
                            koordinat: '-',
                            debit_air: 0,
                            masa_tanam: '',
                            luas_padi: 0,
                            luas_palawija: 0,
                            luas_lainnya: 0,
                            permasalahan: {},
                            p3a: [], // ✅ ubah ke array
                            foto_pemantauan: '',
                        },
                        previewPetak: null,
                        infoGambarSkema: '',
                        previewFoto: null,
                        submitted: false,
                        p3aSearch: '',
                        p3aResults: [],
                        showP3ASearch: false,
                        searchTimeout: null,
                        selectedKabupatenNama: '',
                        selectedDINama: ''

                    }
                },
                computed: {
                    isReadyToContinue() {
                        return (
                            this.form.saluran_id &&
                            this.form.bangunan_id &&
                            this.form.petak_id &&
                            this.form.kecamatan &&
                            this.form.desa
                        );
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
                    validasiHariMinggu() {
                        if (!this.form.tanggal_pantau) return;

                        let tgl = new Date(this.form.tanggal_pantau);

                        if (tgl.getDay() !== 0) {
                            // Simpan tanggal asli agar bisa tampil di info
                            const tanggalAsli = this.form.tanggal_pantau;

                            // Geser ke Minggu terdekat berikutnya
                            while (tgl.getDay() !== 0) {
                                tgl.setDate(tgl.getDate() + 1);
                            }

                            this.form.tanggal_pantau = tgl.toISOString().slice(0, 10);

                            alert(`Tanggal yang Anda pilih (${tanggalAsli}) bukan hari Minggu. \nPengisian akan dialihkan ke tanggal Minggu terdekat: ${this.form.tanggal_pantau}`);
                        }
                    },

                    async searchP3A() {
                        // minimal 3 huruf
                        if (this.p3aSearch.length < 3) {
                            this.p3aResults = [];
                            this.showP3ASearch = false;
                            return;
                        }

                        // debounce agar tidak spam API
                        clearTimeout(this.searchTimeout);
                        this.searchTimeout = setTimeout(async () => {
                            try {
                                let res = await axios.get(`/api/master/p3a?search=${this.p3aSearch}&per_page=all`);
                                this.p3aResults = res.data.data || res.data; // bisa paginate atau full
                                this.showP3ASearch = true;
                            } catch (err) {
                                console.error(err);
                            }
                        }, 400);
                    },
                    selectP3A(p) {
                        // hindari duplikat
                        if (!this.form.p3a.some(item => item.id === p.id)) {
                            this.form.p3a.push(p);
                        }
                        this.p3aSearch = '';
                        this.p3aResults = [];
                        this.showP3ASearch = false;
                    },
                    removeP3A(index) {
                        this.form.p3a.splice(index, 1);
                    },
                    async cekJuru() {
                        try {
                            let res = await axios.post('/api/petugas/validasi-kode', {
                                kode: this.kode
                            });
                            alert("Kode benar, silakan isi form.");
                            console.log(res);

                            this.juru = true
                            this.petugas_nama = res.data.petugas.nama
                            this.form.petugas_id = res.data.petugas.id
                            localStorage.setItem("juru", JSON.stringify(res.data.petugas));
                            this.loadData()
                        } catch (err) {
                            alert("Kode salah!");
                            this.form.petugas_id = '';
                        }
                    },
                    async loadData() {
                        this.loadSaluran()
                        // alert('data terisi')
                    },
                    async loadSaluran() {
                        const juru = JSON.parse(localStorage.getItem("juru"));
                        console.log(juru.id);

                        let res = await axios.get('/api/master/petugas', {
                            params: {
                                petugas_id: juru.id
                            }
                        });

                        console.log(res.data);

                        // Karena hasil dari controller adalah array (->get()), ambil data pertama
                        if (res.data.length > 0) {
                            this.salurans = res.data[0].salurans;
                        } else {
                            this.salurans = [];
                        }
                    },

                    showForm() {
                        if (!this.isReadyToContinue) return; // aman-amanin kalau ditekan manual
                        this.is_show_form = true; // tampilkan form lanjutan
                        this.$nextTick(() => {
                            // Tunggu DOM update, lalu scroll ke elemen dengan id="showForm"
                            const target = document.getElementById("showForm");
                            if (target) {
                                target.scrollIntoView({
                                    behavior: "smooth", // animasi halus
                                    block: "start" // posisikan di atas layar
                                });
                            }
                        });
                    },
                    async checkSaluran() {
                        let res = await axios.get(
                            `/api/master/bangunan?saluran_id=${this.form.saluran_id}`);
                        console.log(res.data);

                        this.bangunans = res.data;

                        // reset field turunan
                        const selected = this.salurans.find(s => s.id === this.form.saluran_id);

                        if (selected) {
                            this.form.daerah_irigasi_id = selected.daerah_irigasi.id;
                            this.form.kabupaten_id = selected.daerah_irigasi.kabupatens[0].id;
                            this.selectedKabupatenNama = selected.daerah_irigasi.kabupatens[0].nama
                            this.selectedDINama = selected.daerah_irigasi.nama
                        } else {
                            this.form.daerah_irigasi_id = '';
                            this.form.kabupaten_id = '';
                        }
                        this.form.bangunan_id = '';
                        this.form.petak_id = '';
                        this.form.kecamatan = '';
                        this.form.desa = '';
                        this.form.koordinat = '-';
                        this.form.luas_padi = 0;
                        this.form.luas_palawija = 0;
                        this.form.luas_lainnya = 0;
                        this.form.debit_air = 0;
                        this.form.masa_tanam = '';
                        this.getMasterPermasalahan();

                        this.petaks = [];

                    },
                    async checkBangunan() {
                        if (this.form.bangunan_id) {
                            let res = await axios.get(`/api/master/petak?bangunan_id=${this.form.bangunan_id}`);
                            this.petaks = res.data;
                        } else {
                            this.petaks = [];
                        }
                        // reset petak & preview
                        this.form.petak_id = '';
                        this.previewPetak = null;
                        this.infoGambarSkema = ''; // kosongkan pesan juga
                    },

                    checkPetak() {
                        let petak = this.petaks.find(p => p.id == this.form.petak_id);
                        console.log("Petak dipilih:", petak);

                        if (petak && petak.gambar_skema) {
                            this.previewPetak = petak.gambar_skema;
                            this.infoGambarSkema = 'Apabila gambar petak tidak sesuai, silahkan isi di PERMASALAHAN LAINNYA dan hubungi koordinator untuk perubahan.';
                        } else {
                            this.previewPetak = null;
                            this.infoGambarSkema = 'Gambar petak belum tersedia. Silahkan isi di PERMASALAHAN LAINNYA dan hubungi koordinator untuk penginputan gambar petak.';
                        }
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
                                        keterangan: '',
                                        foto_permasalahan: ''
                                    };

                                }
                            });
                        });
                    },
                    handleFile(event, id = null) {
                        const file = event.target.files[0];
                        if (!file) return;

                        if (id === null) {
                            // 👉 Jika tanpa ID → berarti upload foto pemantauan utama
                            this.form.foto_pemantauan = file;
                            this.previewFoto = URL.createObjectURL(file);
                        } else {
                            // 👉 Jika ada ID → berarti upload foto permasalahan
                            this.form.permasalahan[id].foto_permasalahan = file;
                            this.form.permasalahan[id].foto_permasalahanPreview = URL.createObjectURL(file);
                        }
                    },
                    openForm() {
                        console.log(this.form);
                        if (!this.validateForm()) {
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
                        if (!this.validateForm()) {
                            return;
                        }

                        const konfirm = confirm('Yakin simpan? Pastikan semua data sudah benar.');
                        if (!konfirm) return;

                        const formData = new FormData();

                        // --- data utama ---
                        for (let key in this.form) {
                            if (key === 'permasalahan' || key === 'foto_pemantauan') continue;
                            if (this.form[key] !== null && this.form[key] !== '') {
                                formData.append(key, this.form[key]);
                            }
                        }

                        // --- foto pemantauan utama ---
                        if (this.form.foto_pemantauan) {
                            formData.append('foto_pemantauan', this.form.foto_pemantauan);
                        }

                        // --- data permasalahan ---
                        // gunakan urutan index agar bisa dibaca backend (misalnya permasalahan[0], permasalahan[1])
                        let index = 0;
                        for (let id in this.form.permasalahan) {
                            const p = this.form.permasalahan[id];
                            formData.append(`permasalahan[${index}][master_permasalahan_id]`, p.master_permasalahan_id);
                            formData.append(`permasalahan[${index}][status]`, p.status);
                            formData.append(`permasalahan[${index}][keterangan]`, p.keterangan || '');

                            if (p.foto_permasalahan instanceof File) {
                                formData.append(`permasalahan[${index}][foto_permasalahan]`, p.foto_permasalahan);
                            }
                            index++;
                        }
                        // --- data p3a ---
                        if (Array.isArray(this.form.p3a)) {
                            this.form.p3a.forEach((p, index) => {
                                formData.append(`p3a[${index}][p3a_id]`, p.id); // ✅ gunakan p.id, bukan p.p3a_id
                            });
                        }


                        console.log(this.form.p3a);

                        // --- kirim ke backend ---
                        try {
                            const res = await axios.post('/api/form-pengisian', formData, {
                                headers: {
                                    'Content-Type': 'multipart/form-data'
                                }
                            });

                            alert('Data berhasil disimpan');
                            location.reload(); // 🔄 reload halaman setelah berhasil

                        } catch (error) {
                            console.error('Error submitting form:', error.response ? error.response.data : error.message);
                            const errorMessage = error.response?.data?.message || 'Terjadi kesalahan saat menyimpan data.';
                            alert(errorMessage);
                        }
                    },
                    resetForm() {
                        this.form = {
                            sesi_id: 1,
                            tanggal_pantau: new Date().toISOString().slice(0, 10), // tetap default hari ini
                            kabupaten_id: '',
                            daerah_irigasi_id: '',
                            saluran_id: '',
                            bangunan_id: '',
                            petak_id: '',
                            kecamatan: '',
                            desa: '',
                            koordinat: '-',
                            debit_air: 0,
                            masa_tanam: '',
                            luas_padi: 0,
                            luas_palawija: 0,
                            luas_lainnya: 0,
                            permasalahan: {},
                            foto_pemantauan: '',
                        }
                        this.previewFoto = null
                        this.is_show_form = false
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
                        this.submitted = true; // tandai sudah dicoba submit

                        console.log(this.form.foto_pemantauan);

                        const optionalFields = ['foto_pemantauan', 'p3a', 'permasalahan'];

                        for (let [key, value] of Object.entries(this.form)) {
                            if (!optionalFields.includes(key) && (value === '' || value === null)) {
                                alert(`Field ${key} wajib diisi!`);
                                return false;
                            }
                        }


                        // validasi khusus foto
                        if (this.form.foto_pemantauan === "") {
                            alert("Foto pemantauan wajib diisi!");
                            return false;
                        }
                        if (this.form.debit_air === "" || this.form.debit_air === "0") {
                            return true;
                        }


                        // validasi permasalahan
                        for (let [id, per] of Object.entries(this.form.permasalahan)) {

                            if (per.status === 'ada' && (!per.keterangan || per.keterangan.trim() === '')) {
                                alert(`Keterangan permasalahan wajib diisi jika ada!`);
                                return false;
                            }
                            if (per.status === 'ada' && (!per.foto_permasalahan)) {
                                alert(`Foto permasalahan wajib diisi jika ada!`);
                                return false;
                            }
                        }
                        return true; // valid
                    },
                    loadJuru() {
                        let juru = localStorage.getItem("juru");
                        if (juru) {
                            const data = JSON.parse(juru);
                            this.petugas_nama = data.nama;
                            this.form.petugas_id = data.id;
                            this.juru = true

                            this.loadData()

                        }
                    },
                    keluarJuru() {
                        if (confirm('Yakin ingin keluar dari akun juru ini?')) {
                            localStorage.removeItem('juru');
                            this.juru = null;
                            alert('Anda telah keluar.');
                        }
                    }

                },
                mounted() {
                    this.loadJuru();

                    // this.getKabupaten();
                    // this.getMasterPermasalahan();
                },
            }).mount('#app')
        </script>
</body>

</html>