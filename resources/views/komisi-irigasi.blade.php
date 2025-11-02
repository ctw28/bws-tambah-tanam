<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Komisi Irigasi</title>
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

        <!-- Input Kode Komir -->
        <div class="col-12 mb-3">
            <a href="/">
                <span class="tf-icons bx bx-arrow-left"></span>&nbsp; Kembali ke halaman depan
            </a>
        </div>
        <div v-if="!komir">
            <div class="card shadow-lg">
                <div class="card-body">
                    <h5 class="card-title mt-2">Masuk Komisi Irigasi</h5>
                    <div class="mb-3">
                        <label class="form-label">Masukkan Kode Komisi Irigasi</label>
                        <input type="text" v-model="kode" class="form-control" placeholder="Kode unik Komisi Irigasi">
                    </div>
                    <button class="btn btn-primary" @click="cekKomir">Masuk
                </div>
            </div>
        </div>

        <!-- Halaman Validasi -->
        <div v-else>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Halo, Komisi Irigasi</h4>
                <button class="btn btn-sm btn-danger" @click="logout">Keluar</button>
            </div>

            <div class="card shadow-lg">
                <div class="card-header">
                    <h5 class="mb-3">BASISDATA HASIL PEMANTAUAN LUAS TANAM</h5>

                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <button class="nav-link" :class="{ active: activeTab === 'dashboard' }" @click="activeTab = 'dashboard'">
                                📝 Hasil Pemantauan
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
                        <div v-if="isFilter">
                            <div class="card h-100">
                                <div class="card-body">

                                    <div class="user-profile-header d-flex flex-column flex-lg-row text-sm-start text-center mb-8">
                                        <div class="flex-grow-1 mt-2">
                                            <div class="user-profile-info">
                                                <h4 class="mb-2">Daerah Irigasi @{{selectedDI.nama}} - Kab. @{{selectedDI.kabupatens[0].nama}}</h4>

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
                                                    <div class="col d-flex">
                                                        <div class="me-3">
                                                            <span class="badge rounded-2 bg-label-secondary p-2"><i class="icon-base bx bx-bullseye icon-lg text-secondary"></i></span>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">@{{rekap.total_pengamat}}</h6>
                                                            <small>Pengamat</small>
                                                        </div>
                                                    </div>
                                                    <div class="col d-flex">
                                                        <div class="me-3">
                                                            <span class="badge rounded-2 bg-label-dark p-2"><i class="icon-base bx bx-user icon-lg text-dark"></i></span>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">@{{rekap.total_juru}}</h6>
                                                            <small>Juru</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <h5 class="mt-4">Informasi Luas Daerah Irigasi</h5>
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
                                                    <!-- <div class="col d-flex">
                                </div>
                                <div class="col d-flex">
                                </div> -->
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Ringkasan umum -->
                            <div class="card h-100 mt-3">
                                <div class="card-body">

                                    <div class="row g-3 mb-4">
                                        <h4 class="mt-4">Basisdata Hasil Pemantauan</h4>

                                        <!-- Total Laporan Juru -->
                                        <div class="col-lg-3 col-sm-6">
                                            <div class="card shadow-sm">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <div class="avatar me-4">
                                                            <span class="avatar-initial rounded bg-label-primary">
                                                                <i class="bx bx-file icon-lg"></i>
                                                            </span>
                                                        </div>
                                                        <h4 class="mb-0">@{{ filteredItems.length }}</h4>
                                                    </div>
                                                    <p class="mb-0 text-muted fw-semibold">Laporan Juru Tervalidasi</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-sm-6">
                                            <div class="card shadow-sm">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <div class="avatar me-4">
                                                            <span class="avatar-initial rounded bg-label-info">
                                                                <i class="bx bx-file icon-lg"></i>
                                                            </span>
                                                        </div>
                                                        <h4 class="mb-0">@{{ rekapLuasTotal.padi }} ha</h4>
                                                    </div>
                                                    <p class="mb-0 text-muted fw-semibold">Luas Tanam Padi</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-sm-6">
                                            <div class="card shadow-sm">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <div class="avatar me-4">
                                                            <span class="avatar-initial rounded bg-label-warning">
                                                                <i class="bx bx-file icon-lg"></i>
                                                            </span>
                                                        </div>
                                                        <h4 class="mb-0">@{{ rekapLuasTotal.palawija }} ha</h4>
                                                    </div>
                                                    <p class="mb-0 text-muted fw-semibold">Luas Tanam Palawija</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-sm-6">
                                            <div class="card shadow-sm">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <div class="avatar me-4">
                                                            <span class="avatar-initial rounded bg-label-success">
                                                                <i class="bx bx-file icon-lg"></i>
                                                            </span>
                                                        </div>
                                                        <h4 class="mb-0">@{{ rekapLuasTotal.lainnya }} ha</h4>
                                                    </div>
                                                    <p class="mb-0 text-muted fw-semibold">Luas Tanam Lainnya</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-sm-6">
                                            <div class="card shadow-sm">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <div class="avatar me-4">
                                                            <span class="avatar-initial rounded bg-label-dark">
                                                                <i class="bx bx-file icon-lg"></i>
                                                            </span>
                                                        </div>
                                                        <h4 class="mb-0">@{{ rekapLuasTotal.total }} ha</h4>
                                                    </div>
                                                    <p class="mb-0 text-muted fw-semibold">Luas Keseluruhan</p>
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                    <h4>Rekap Pengisian Luas Tanam</h4>
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Saluran</th>
                                                    <th>Luas Padi (ha)</th>
                                                    <th>Luas Palawija (ha)</th>
                                                    <th>Luas Lainnya (ha)</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(p, index) in rekapLuasTanam" :key="p.id">
                                                    <td>@{{ (pagination.current - 1) * perPage + index + 1 }}</td>
                                                    <td>
                                                        @{{ p.saluran}} - @{{ p.bangunan }} - @{{ p.petak }}<br>
                                                        <small>Terakhir update : @{{ p.tanggal_update!='-'? formatTanggalIndo(p.tanggal_update) : 'belum ada update'}}</small>
                                                    </td>
                                                    <td>@{{ p.padi }}</td>
                                                    <td>@{{ p.palawija }}</td>
                                                    <td>@{{ p.lainnya }}</td>
                                                    <td>@{{ p.total }}</td>
                                                </tr>
                                                <tr v-if="rekapLuasTanam.length === 0">
                                                    <td colspan="4" class="text-center text-muted">Belum ada permasalahan</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <!-- Pilih jumlah data per halaman -->
                                            <div class="d-flex align-items-center gap-2">
                                                <select v-model="perPage" @change="loadRekapPengisian(1)" class="form-select form-select" style="width: auto;">
                                                    <option value="10">10</option>
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
                                                        <a class="page-link" href="#" @click.prevent="loadRekapPengisian(pagination.current - 1)">Prev</a>
                                                    </li>

                                                    <li v-for="page in pagination.last" :key="page" class="page-item" :class="{ active: page === pagination.current }">
                                                        <a class="page-link" href="#" @click.prevent="loadRekapPengisian(page)">@{{ page }}</a>
                                                    </li>

                                                    <li class="page-item" :class="{ disabled: pagination.current === pagination.last }">
                                                        <a class="page-link" href="#" @click.prevent="loadRekapPengisian(pagination.current + 1)">Next</a>
                                                    </li>
                                                </ul>
                                            </nav>
                                        </div>
                                    </div>
                                    <h4 class="mt-4">Permasalahan</h4>
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Juru</th>
                                                    <th>Permasalahan</th>
                                                    <th>Foto</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(p, index) in latestIssues" :key="p.id">
                                                    <td>@{{ index + 1 }}</td>
                                                    <td>
                                                        @{{ p.form_pengisian?.petugas?.nama || '-' }} <br>
                                                        <span class="badge bg-success">
                                                            @{{ p.form_pengisian?.daerah_irigasi?.nama ?? '-' }}
                                                        </span> <br>
                                                        @{{ p.form_pengisian?.saluran?.nama || '-' }} <br> @{{ p.form_pengisian?.bangunan?.nama || '-' }} - @{{ p.form_pengisian?.petak?.nama || '-' }}<br>
                                                        @{{ formatTanggalIndo(p.created_at) }}
                                                    </td>
                                                    <td>@{{ p.master_permasalahan?.id }}. @{{ p.master_permasalahan?.nama }} : @{{p.keterangan}}</td>
                                                    <!-- <td>@{{ p.created_at }}</td> -->
                                                    <td> <img v-if="p.foto_permasalahan" :src="`/storage/${p.foto_permasalahan}`" width="100">
                                                    </td>
                                                </tr>
                                                <tr v-if="latestIssues.length === 0">
                                                    <td colspan="4" class="text-center text-muted">Belum ada permasalahan</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <h4 class="mt-5">📈 Informasi Grafis Jenis Tanaman</h4>
                                    <canvas id="chartItem" height="100"></canvas>
                                </div>
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
                                                    v-for="s in daerahIrigasis"
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
                                    <select v-model="perPagePermasalahan" @change="loadPermasalahan(1)" class="form-select form-select" style="width: auto;">
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
                    perPagePermasalahan: 25, // default
                    is_filtered: false,
                    is_loading: false,
                    activeTab: 'dashboard', // default tab yang aktif saat halaman dibuka,
                    rekapItems: [],
                    chartDI: null,
                    chartItem: null,
                    isFilter: false,
                    rekap: [],
                    rekapLuasTanam: [],
                    latestIssues: [],
                    rekapLuasTotal: [],
                    filterDI: '',

                }
            },
            methods: {
                loadKomir() {
                    let data = localStorage.getItem("komir");
                    console.log("Data dari localStorage:", data);

                    if (data) {
                        try {
                            this.komir = JSON.parse(data);
                        } catch (e) {
                            console.error("Gagal parse JSON komir:", e);
                            this.komir = null; // atau objek default
                        }
                    } else {
                        this.komir = null; // default kosong
                    }
                },
                async cekKomir() {
                    try {
                        let res = await axios.post("/api/komir/validasi-kode", {
                            kode: this.kode
                        });
                        console.log(res);
                        this.komir = res.data.komis;
                        localStorage.setItem("komir", JSON.stringify(res.data.komis));
                    } catch (e) {
                        alert("Kode Komir tidak valid!");
                    }
                },
                async loadPermasalahan(page = 1) {
                    try {
                        // alert('load masalah')
                        let url = `/api/form-pengisian?page=${page}&per_page=${this.perPagePermasalahan}&pengamat_valid=1&has_permasalahan=1`;

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
                        let url = `/api/form-pengisian?page=all&di_id=${this.filterDI}`
                        if (this.filterTanggalAwal) url += `&tanggal_awal=${this.filterTanggalAwal}`;
                        if (this.filterTanggalAkhir) url += `&tanggal_akhir=${this.filterTanggalAkhir}`;

                        let res = await axios.get(url);
                        this.items = res.data.data;
                        this.filteredItems = res.data;
                        console.log(this.filteredItems);
                        this.loadRekap()

                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.is_loading = false;
                    }
                },
                async loadRekap() {
                    let url = `/api/master/rekap-data?di_id=${this.filterDI}`
                    axios.get(url).then(res => {
                        console.log(res);
                        this.rekap = res.data
                    });

                    axios.get(`/api/latest-issues?di_id=${this.filterDI}`).then(res => {
                        this.latestIssues = res.data
                        console.log(this.latestIssues);
                    });
                    this.loadRekapPengisian(1)

                },
                async loadRekapPengisian(page = 1) {
                    // alert(page)
                    let url = `/api/rekap-petak?di_id=${this.filterDI}&page=${page}&per_page=${this.perPage}`
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

                    url = `/api/rekap-di?di_id=${this.filterDI}`
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
                logout() {
                    this.komir = null;
                    localStorage.removeItem("komir");

                    this.kode = "";
                    this.forms = [];
                },
                applyFilter() {
                    this.selectedDI = this.daerahIrigasis.find(d => d.id === this.filterDI) || null;
                    this.loadData()
                    this.isFilter = true

                },
                applyFilterPermasalahan() {
                    this.loadPermasalahan(1)
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

                chartPerItem() {
                    const rekap = this.rekapLuasTotal; // contoh: { padi: 123, palawija: 45, lainnya: 12, total: 180 }
                    console.log(rekap);

                    if (this.chartItem) this.chartItem.destroy();

                    this.chartItem = new Chart(document.getElementById('chartItem'), {
                        type: 'bar',
                        data: {
                            labels: ['Padi', 'Palawija', 'Lainnya'], // label kategori
                            datasets: [{
                                label: 'Luas (ha)',
                                data: [rekap.padi, rekap.palawija, rekap.lainnya],
                                backgroundColor: [
                                    'rgba(75, 192, 192, 0.6)',
                                    'rgba(255, 205, 86, 0.6)',
                                    'rgba(201, 90, 90, 0.6)'
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                title: {
                                    display: true,
                                    text: 'Rekap Luas Tanam (ha)'
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
                    let res = await axios.get('/api/master/daerah-irigasi?page=all&kabupaten_id=9');
                    console.log(res.data.data);
                    this.daerahIrigasis = res.data.data;
                },

            },
            computed: {
                rekapPerDaerahIrigasi() {
                    const rekap = {};

                    this.filteredItems.forEach(i => {
                        const di = i.daerah_irigasi;
                        if (!di) return;

                        // ambil nama DI induk jika ada, kalau tidak pakai nama sendiri
                        const namaDI = di.parent_id ?
                            (di.parent?.nama || 'Tidak Ada DI') :
                            (di.nama || 'Tidak Ada DI');

                        if (!rekap[namaDI]) {
                            rekap[namaDI] = {
                                padi: 0,
                                palawija: 0,
                                lainnya: 0,
                                total: 0
                            };
                        }

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
                this.loadKomir();

                // this.loadDashboard();
                this.loadDI()
            }
        }).mount("#app");
    </script>
</body>

</html>