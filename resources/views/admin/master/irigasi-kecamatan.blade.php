@extends('admin.template')

@section('content')
<div id="app" v-cloak class="container mt-4">
    <div class="card h-100">
        <div class="card-header">
            <h5 class="mb-0">Data Desa dan Kecamatan</h5>
        </div>
        <div class="card-body">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-bold">Daerah Irigasi (Induk)</label>
                            <select class="form-select" v-model="filterDI" @change="checkChild">
                                <option value="">-- Pilih Daerah Irigasi --</option>
                                <option v-for="d in daerahIrigasis" :value="d.id">@{{ d.nama }}</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-4" v-if="isChild">
                            <label class="form-label fw-bold">Wilayah</label>
                            <select class="form-select" v-model="filterDIChild">
                                <option value="">-- Pilih Wilayah --</option>
                                <option v-for="d in daerahIrigasisChild" :value="d.id">@{{ d.nama }}</option>
                            </select>
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
        </div>
        <div v-if="is_filtered">
            <div class="card">

                <div class="card-body">

                    <div class="d-flex justify-content-between mb-2">
                        <h6 class="mb-0">Data Kecamatan</h6>
                        <button class="btn btn-sm btn-success" @click="openTambahKecamatan">
                            + Tambah Kecamatan
                        </button>
                    </div>

                    <div class="table-responsive" v-if="items.length > 0">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kecamtan</th>
                                    <th>Desa</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item,index) in items" :key="item.id">
                                    <td>@{{index+1}}</td>
                                    <td>@{{ item.nama }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info"
                                            @click="openDesaModal(item)">
                                            Desa (@{{ item.desas_count ?? 0 }})
                                        </button>
                                    </td>

                                    <td>
                                        <button class="btn btn-sm btn-warning me-1"
                                            @click="editKecamatan(item)">
                                            Edit
                                        </button>

                                        <button class="btn btn-sm btn-danger me-1"
                                            @click="hapusKecamatan(item.id)">
                                            Hapus
                                        </button>
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
                </div>
            </div>
        </div>

    </div>
    <!-- Modal Kecamatan -->
    <div class="modal fade" id="modalKecamatan" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        @{{ formKecamatan.id ? 'Edit Kecamatan' : 'Tambah Kecamatan' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Nama Kecamatan</label>
                        <input type="text" class="form-control" v-model="formKecamatan.nama">
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary" @click="simpanKecamatan">Simpan</button>
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="formLTTModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Data Desa Kecamatan: @{{ selectedKecamatan?.nama }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="d-flex justify-content-between mb-2">
                        <button class="btn btn-sm btn-success" @click="openTambahDesa">
                            + Tambah Desa
                        </button>
                    </div>

                    <!-- FORM TAMBAH/EDIT -->
                    <div v-if="showFormDesa" class="card mb-3">
                        <div class="card-body">
                            <div class="mb-2">
                                <label class="form-label">Nama Desa</label>
                                <input type="text" class="form-control" v-model="formDesa.nama">
                            </div>

                            <div class="d-flex gap-2">
                                <button class="btn btn-primary btn-sm" @click="simpanDesa">
                                    Simpan
                                </button>
                                <button class="btn btn-secondary btn-sm" @click="batalFormDesa">
                                    Batal
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- LIST DESA -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th width="50">No</th>
                                    <th>Nama Desa</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(desa, i) in desas" :key="desa.id">
                                    <td>@{{ i+1 }}</td>
                                    <td>@{{ desa.nama }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-warning me-2"
                                            @click="editDesa(desa)">Edit</button>
                                        <button class="btn btn-sm btn-danger"
                                            @click="hapusDesa(desa.id)">Hapus</button>
                                    </td>
                                </tr>
                                <tr v-if="desas.length === 0">
                                    <td colspan="3" class="text-center text-muted">
                                        Tidak ada data desa
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>


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
                masterPermasalahan: [],
                filterPermasalahan: '',
                isChild: false,
                filterDIChild: '', // ✅ tambahkan ini
                daerahIrigasisChild: [],
                diId: '',
                selectedKecamatan: null,
                desas: [],
                showFormDesa: false,
                formDesa: {
                    id: null,
                    nama: ''
                },

                formKecamatan: {
                    id: null,
                    nama: ''
                },
                modalKecamatanInstance: null,


            };
        },
        async mounted() {
            // await this.loadData();
            this.loadDI();

        },
        methods: {
            clearData() {
                this.isFilter = false
            },
            async loadDI() {
                let res = await axios.get('/api/koordinator-di?is_induk=1');
                console.log(res.data);
                this.daerahIrigasis = res.data;

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
            applyFilter() {
                let diId = this.isChild ? this.filterDIChild : this.filterDI
                // alert(diId);
                if (this.isChild)
                    this.selectedDI = this.daerahIrigasisChild.find(d => d.id === this.filterDIChild) || null;
                else
                    this.selectedDI = this.daerahIrigasis.find(d => d.id === this.filterDI) || null;
                if (!diId) {
                    alert("Pilih Daerah Irigasi terlebih dahulu")
                    return
                }
                this.diId = diId
                this.is_filtered = true
                this.is_loading = true;

                this.loadData(1);
            },
            async loadData(page = 1) {
                try {
                    // let url = `/api/form-pengisian?page=${page}&per_page=${this.perPage}&pengamat_valid=1&upi_valid=1&has_permasalahan=1`;
                    let url = `/api/irigasi-kecamatan?di_id=${this.diId}&page=${page}&per_page=${this.perPage}`;

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

            openTambahKecamatan() {
                this.formKecamatan = {
                    id: null,
                    nama: ''
                };

                const modalEl = document.getElementById('modalKecamatan');
                this.modalKecamatanInstance = new bootstrap.Modal(modalEl);
                this.modalKecamatanInstance.show();
            },

            editKecamatan(item) {
                this.formKecamatan = {
                    id: item.id,
                    nama: item.nama
                };

                const modalEl = document.getElementById('modalKecamatan');
                this.modalKecamatanInstance = new bootstrap.Modal(modalEl);
                this.modalKecamatanInstance.show();
            },

            async simpanKecamatan() {
                if (!this.formKecamatan.nama) {
                    alert('Nama kecamatan wajib diisi');
                    return;
                }

                try {
                    if (this.formKecamatan.id) {
                        // update
                        await axios.put(`/api/irigasi-kecamatan/${this.formKecamatan.id}`, {
                            nama: this.formKecamatan.nama
                        });
                    } else {
                        // insert (ikut DI)
                        await axios.post(`/api/irigasi-kecamatan`, {
                            nama: this.formKecamatan.nama,
                            daerah_irigasi_id: this.diId
                        });
                    }

                    this.modalKecamatanInstance.hide();
                    this.loadData(this.pagination.current);

                } catch (e) {
                    console.error(e);
                    alert('Gagal menyimpan kecamatan');
                }
            },

            async hapusKecamatan(id) {
                if (!confirm('Hapus kecamatan ini?')) return;

                try {
                    await axios.delete(`/api/irigasi-kecamatan/${id}`);
                    this.loadData(this.pagination.current);
                } catch (e) {
                    console.error(e);
                    alert('Gagal menghapus kecamatan');
                }
            },


            resetFilter() {
                this.filterDI = "";
                this.filterTanggalAwal = "";
                this.filterTanggalAkhir = "";
                this.items = [];
                this.is_filtered = false

            },

            async openDesaModal(kecamatan) {
                this.selectedKecamatan = kecamatan;
                this.showFormDesa = false;
                this.resetFormDesa();

                await this.loadDesa();

                const modalEl = document.getElementById('formLTTModal');
                this.modalInstance = new bootstrap.Modal(modalEl);
                this.modalInstance.show();
            },

            async loadDesa() {
                if (!this.selectedKecamatan) return;

                try {
                    const res = await axios.get(
                        `/api/irigasi-desa?kecamatan_id=${this.selectedKecamatan.id}`
                    );
                    this.desas = res.data.data ?? res.data;
                } catch (e) {
                    console.error(e);
                    alert('Gagal memuat data desa');
                }
            },

            openTambahDesa() {
                this.showFormDesa = true;
                this.resetFormDesa();
            },

            editDesa(desa) {
                this.showFormDesa = true;
                this.formDesa = {
                    id: desa.id,
                    nama: desa.nama
                };
            },

            batalFormDesa() {
                this.showFormDesa = false;
                this.resetFormDesa();
            },

            resetFormDesa() {
                this.formDesa = {
                    id: null,
                    nama: ''
                };
            },

            async simpanDesa() {
                if (!this.formDesa.nama) {
                    alert('Nama desa wajib diisi');
                    return;
                }

                try {
                    if (this.formDesa.id) {
                        // UPDATE
                        await axios.put(`/api/irigasi-desa/${this.formDesa.id}`, {
                            nama: this.formDesa.nama
                        });
                    } else {
                        // INSERT
                        await axios.post('/api/irigasi-desa', {
                            nama: this.formDesa.nama,
                            daerah_irigasi_kecamatan_id: this.selectedKecamatan.id
                        });
                    }

                    this.showFormDesa = false;
                    this.resetFormDesa();

                    await this.loadDesa(); // reload list desa
                    await this.loadData(); // reload list kecamatan (biar update count)

                } catch (e) {
                    console.error(e);
                    alert('Gagal menyimpan desa');
                }
            },

            async hapusDesa(id) {
                if (!confirm('Hapus desa ini?')) return;

                try {
                    await axios.delete(`/api/irigasi-desa/${id}`);
                    await this.loadDesa();
                    await this.loadData();
                } catch (e) {
                    console.error(e);
                    alert('Gagal menghapus desa');
                }
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