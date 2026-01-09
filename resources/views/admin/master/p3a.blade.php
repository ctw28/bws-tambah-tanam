@extends('admin.template')

@section('content')
<div id="app" v-cloak class="container mt-4">

    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">P3A (Perkumpulan Petani Pemakai Air)</h5>
            <!-- <button class="btn btn-primary" @click="openForm()">+ Tambah P3A</button> -->
        </div>
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
                    <select class="form-select" v-model="filterDIChild" @change="clearData">
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
                    <button class="btn btn-secondary btn w-100" @click="resetFilter" v-if="isFilter">Reset</button>
                </div>
            </div>


            <div v-if="!isFilter" class="alert alert-info mt-4 text-center">
                Silakan pilih <b>Daerah Irigasi</b> lalu klik <b>Filter</b> untuk menampilkan data.
            </div>
            <div v-else>
                <div
                    class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3 mt-3">

                    <!-- SEARCH (KIRI) -->
                    <input type="text" v-model="search" class="form-control w-25" placeholder="Cari nama P3A..."
                        @keyup.enter="fetchP3A(1)">

                    <!-- TOMBOL TAMBAH (KANAN) -->
                    <button class="btn btn-primary" v-if="isFilter" @click="openForm()">
                        + Tambah P3A
                    </button>

                </div>


                <!-- Tabel daftar P3A -->
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>DI</th>
                                <th>Nama P3A</th>
                                <th>Keterangan</th>
                                <th width="150">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(p, index) in p3as.data" :key="p.id">
                                <td>@{{ (p3as.from || 0) + index }}</td>
                                <td>
                                    <span v-if="!p.daerah_irigasi_id" class="badge bg-danger">
                                        Belum ada DI
                                    </span>
                                    <span v-else class="badge bg-success">
                                        @{{ p.daerah_irigasi.nama }}
                                    </span>
                                </td>
                                <td>@{{ p.nama }}</td>
                                <td>@{{ p.keterangan || '-' }}</td>

                                <td class="text-nowrap text-md-wrap">
                                    <button class="btn btn-sm btn-warning me-2" @click="editP3A(p)">Edit</button>
                                    <button class="btn btn-sm btn-danger" @click="deleteP3A(p.id)">Hapus</button>
                                </td>
                            </tr>
                            <tr v-if="!p3as.data || !p3as.data.length">
                                <td colspan="4" class="text-center text-muted">Belum ada data</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3">

                    <!-- INFO -->
                    <div class="text-muted">
                        Halaman <b>@{{ p3as.current_page }}</b>
                        dari <b>@{{ p3as.last_page }}</b>
                        (Total: <b>@{{ p3as.total }}</b> data)
                    </div>

                    <!-- PER PAGE -->
                    <select v-model="perPage" class="form-select d-inline-block w-auto" @change="fetchP3A(1)">
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>

                </div>

                <!-- Pagination -->
                <nav v-if="p3as.data && p3as.last_page > 1" class="mt-3">
                    <ul class="pagination justify-content-center">

                        <!-- Tombol Sebelumnya -->
                        <li class="page-item" :class="{ disabled: !p3as.prev_page_url }">
                            <a class="page-link" href="#" @click.prevent="fetchP3A(p3as.current_page - 1)">
                                «
                            </a>
                        </li>

                        <!-- Nomor halaman dinamis -->
                        <li v-for="page in pagesToShow" :key="page" class="page-item"
                            :class="{ active: page === p3as.current_page, disabled: page === '...'}">
                            <a v-if="page !== '...'" class="page-link" href="#" @click.prevent="fetchP3A(page)">
                                @{{ page }}
                            </a>
                            <span v-else class="page-link">...</span>
                        </li>

                        <!-- Tombol Selanjutnya -->
                        <li class="page-item" :class="{ disabled: !p3as.next_page_url }">
                            <a class="page-link" href="#" @click.prevent="fetchP3A(p3as.current_page + 1)">
                                »
                            </a>
                        </li>

                    </ul>
                </nav>

            </div>
        </div>
    </div>

    <!-- Modal Form Tambah/Edit -->
    <div class="modal fade" id="p3aModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form @submit.prevent="saveP3A">
                    <div class="modal-header">
                        <h5 class="modal-title">@{{ form.id ? 'Edit P3A' : 'Tambah P3A' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">

                        <!-- MODE TAMBAH -->
                        <div class="mb-3" v-if="formMode === 'create'">
                            <label class="form-label">Daerah Irigasi</label>
                            <input type="text" class="form-control" :value="selectedDINama" readonly>
                        </div>

                        <!-- MODE EDIT & DI KOSONG -->
                        <div class="mb-3" v-else-if="isDIEmpty">
                            <label class="form-label">Daerah Irigasi <span class="text-danger">*</span></label>
                            <select class="form-select" v-model="form.daerah_irigasi_id" required>
                                <option value="">-- Pilih Daerah Irigasi --</option>
                                <option v-for="di in daerahIrigasis" :value="di.id">
                                    @{{ di.nama }}
                                </option>
                            </select>
                        </div>

                        <!-- MODE EDIT & DI SUDAH ADA -->
                        <div class="mb-3" v-else>
                            <label class="form-label">Daerah Irigasi</label>
                            <input type="text" class="form-control" :value="selectedDINama" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama P3A <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="form.nama" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea class="form-control" v-model="form.keterangan" rows="3"
                                placeholder="Opsional"></textarea>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
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
            p3as: {
                data: []
            },
            daerahIrigasis: [],
            selectedDI: '',
            isDIEmpty: false,
            filterDI: '',
            search: '',
            perPage: 25,
            form: {
                id: null,
                nama: '',
                keterangan: '',
                daerah_irigasi_id: null,
            },
            formMode: 'create', // create | edit
            isChild: false,
            filterDIChild: '', // ✅ tambahkan ini
            selectedIndukDI: '',
            daerahIrigasisChild: [],
            activeDI: null,
            is_loading: false,
            isFilter: false,


        };
    },

    methods: {
        // async fetchP3A(page = 1) {
        //     try {
        //         let res = await axios.get(
        //             `/api/master/p3a?page=${page}&per_page=${this.perPage}&search=${this.search}`);
        //         this.p3as = res.data;
        //     } catch (err) {
        //         console.error(err);
        //         alert("Gagal memuat data P3A");
        //     }
        // },
        openForm() {
            if (!this.selectedDI) {
                alert('Pilih Daerah Irigasi terlebih dahulu');
                return;
            }

            this.formMode = 'create';
            this.isDIEmpty = false;

            this.form = {
                id: null,
                nama: '',
                keterangan: '',
                daerah_irigasi_id: this.selectedDI, // 🔒 DIKUNCI dari filter
            };

            new bootstrap.Modal(document.getElementById('p3aModal')).show();
        },
        editP3A(p) {
            this.formMode = 'edit';

            this.form = {
                id: p.id,
                nama: p.nama,
                keterangan: p.keterangan,
                daerah_irigasi_id: p.daerah_irigasi_id,
            };

            this.isDIEmpty = !p.daerah_irigasi_id;

            new bootstrap.Modal(document.getElementById('p3aModal')).show();
        },

        async saveP3A() {
            try {
                if (this.form.id) {
                    await axios.put(`/api/master/p3a/${this.form.id}`, this.form);
                } else {
                    await axios.post('/api/master/p3a', this.form);
                }
                this.fetchP3A(this.p3as.current_page);
                bootstrap.Modal.getInstance(document.getElementById('p3aModal')).hide();
            } catch (err) {
                console.error(err);
                alert("Gagal menyimpan data");
            }
        },
        async deleteP3A(id) {
            if (confirm("Yakin ingin menghapus P3A ini?")) {
                try {
                    await axios.delete(`/api/master/p3a/${id}`);
                    this.fetchP3A(this.p3as.current_page);
                } catch (err) {
                    console.error(err);
                    alert("Gagal menghapus data");
                }
            }
        },
        async fetchDI() {
            // let res = await axios.get('/api/master/daerah-irigasi?page=all&&kabupaten_id=9');
            let res = await axios.get('/api/master/daerah-irigasi?page=all&is_induk=1');

            this.daerahIrigasis = res.data.data;
        },
        clearData() {
            this.isFilter = false
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

            // JIKA PUNYA CHILD
            if (this.isChild) {

                if (!this.filterDIChild) {
                    alert("Pilih Wilayah terlebih dahulu")
                    return
                }

                this.activeDI = this.filterDIChild
                this.selectedDI = this.filterDIChild // 🔥 TAMBAH INI

            }

            // JIKA TIDAK PUNYA CHILD
            else {

                if (!this.filterDI) {
                    alert("Pilih Daerah Irigasi terlebih dahulu")
                    return
                }

                this.activeDI = this.filterDI
                this.selectedDI = this.filterDI // 🔥 TAMBAH INI
            }

            this.fetchP3A(1)
            this.isFilter = true
        },

        async fetchP3A(page = 1) {
            const res = await axios.get('/api/master/p3a', {
                params: {
                    page,
                    per_page: this.perPage,
                    search: this.search,
                    daerah_irigasi_id: this.activeDI,
                }
            });
            this.p3as = res.data;
        },
        resetFilter() {
            // kosongkan filter tanggal
            this.filterDI = ''
            this.isFilter = false
        },

    },
    computed: {
        selectedDINama() {
            if (!Array.isArray(this.daerahIrigasis)) return '-';

            const id = this.formMode === 'create' ?
                this.selectedDI :
                this.form.daerah_irigasi_id;

            const di = this.daerahIrigasis.find(d => d.id == id);
            return di ? di.nama : '-';
        },
        pagesToShow() {
            const total = this.p3as.last_page;
            const current = this.p3as.current_page;
            if (!total) return [];

            // Jika halaman < 7, tampilkan semua
            if (total <= 7) {
                return Array.from({
                    length: total
                }, (_, i) => i + 1);
            }

            // Jika halaman banyak, tampilkan rentang dinamis
            const pages = [];
            if (current > 3) pages.push(1);
            if (current > 4) pages.push('...');
            for (let i = current - 2; i <= current + 2; i++) {
                if (i > 0 && i <= total) pages.push(i);
            }
            if (current < total - 3) pages.push('...');
            if (current < total - 2) pages.push(total);
            return pages;
        }
    },

    mounted() {
        this.fetchDI();
        // this.fetchP3A();
    }
}).mount("#app");
</script>
@endpush