@extends('admin.template')

@section('content')
<div id="app" v-cloak class="container mt-4">

    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daerah Irigasi</h5>
            <button class="btn btn-primary" @click="openForm()">+ Tambah DI</button>
        </div>
        <div class="card-body">
            <!-- Tabel daftar DI -->
            <!-- Filter -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="input-group w-25">
                    <input type="text" v-model="search" class="form-control" placeholder="Cari DI..." @keyup.enter="fetchDI(1)">
                </div>
                <div>
                    <select v-model="perPage" class="form-select d-inline-block w-auto" @change="fetchDI(1)">
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama DI</th>
                            <th>Induk</th>
                            <th>Kabupaten Terkait</th>
                            <th>Luas Baku</th>
                            <th>Luas Potensial</th>
                            <th>Luas Fungsional</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(di,index) in daerahIrigasis" :key="di.id">
                            <th>@{{index+1}}</th>
                            <td>@{{ di.nama }}</td>
                            <td>@{{ di.parent ? di.parent.nama : '-' }}</td>
                            <td>
                                <span v-for="kab in di.kabupatens" class="badge bg-info me-1">
                                    @{{ kab.nama }}
                                </span>
                            </td>
                            <td>@{{ di.luas_baku }}</td>
                            <td>@{{ di.luas_potensial }}</td>
                            <td>@{{ di.luas_fungsional }}</td>
                            <td class="text-nowrap text-md-wrap">
                                <button class="btn btn-sm btn-warning me-1" @click="editDI(di)">Edit</button>
                                <button class="btn btn-sm btn-danger" @click="deleteDI(di.id)">Hapus</button>
                            </td>


                        </tr>
                    </tbody>

                </table>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <small>
                        Menampilkan halaman @{{ pagination.current }} dari @{{ pagination.last }} |
                        Total: @{{ pagination.total }} data
                    </small>

                    <nav>
                        <ul class="pagination mb-0">
                            <li class="page-item" :class="{ disabled: pagination.current === 1 }">
                                <a class="page-link" href="#" @click.prevent="fetchDI(pagination.current - 1)">Prev</a>
                            </li>

                            <li v-for="page in pageNumbers" :key="page"
                                class="page-item"
                                :class="{ active: page === pagination.current }">
                                <a class="page-link" href="#" @click.prevent="fetchDI(page)">
                                    @{{ page }}
                                </a>
                            </li>

                            <li class="page-item" :class="{ disabled: pagination.current === pagination.last }">
                                <a class="page-link" href="#" @click.prevent="fetchDI(pagination.current + 1)">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>


            </div>
        </div>
    </div>

    <!-- Modal Form Tambah/Edit -->
    <div class="modal fade" id="diModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form @submit.prevent="saveDI">
                    <div class="modal-header">
                        <h5 class="modal-title">@{{ form.id ? 'Edit DI' : 'Tambah DI' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Nama DI -->
                        <div class="mb-3">
                            <label class="form-label">Nama Daerah Irigasi</label>
                            <input type="text" v-model="form.nama" class="form-control" required>
                        </div>
                        <!-- Parent DI -->
                        <div class="mb-3">
                            <label class="form-label">Parent DI (opsional)</label>
                            <select v-model="form.parent_id" class="form-select">
                                <option value="">-- Tanpa Parent (Induk) --</option>
                                <option v-for="di in daerahIrigasis" :value="di.id" :key="'parent-'+di.id">
                                    @{{ di.nama }}
                                </option>
                            </select>
                        </div>

                        <!-- Kabupaten -->
                        <div class="mb-3">
                            <label class="form-label">Kabupaten Terkait</label>
                            <div class="form-check" v-for="kab in kabupatens" :key="kab.id">
                                <input class="form-check-input" type="checkbox" :value="kab.id"
                                    v-model="form.kabupaten_ids" :id="'kab-'+kab.id">
                                <label class="form-check-label" :for="'kab-'+kab.id">
                                    @{{ kab.nama }}
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Luas Baku</label>
                            <input type="number" step="0.001" v-model="form.luas_baku" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Luas Potensial</label>
                            <input type="number" step="0.001" v-model="form.luas_potensial" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Luas Fungsional</label>
                            <input type="number" step="0.001" v-model="form.luas_fungsional" class="form-control" required>
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
                daerahIrigasis: [],
                kabupatens: [],
                pagination: {
                    current: 1,
                    last: 1,
                    total: 0,
                },
                perPage: 25,
                search: '',
                form: {
                    id: null,
                    nama: '',
                    luas_baku: 0,
                    luas_potensial: 0,
                    luas_fungsional: 0,
                    parent_id: '',
                    kabupaten_ids: []
                }
            }
        },
        computed: {
            // 🔹 Menampilkan maksimal 5 nomor halaman (misalnya 1 2 3 4 5)
            pageNumbers() {
                const total = this.pagination.last;
                const current = this.pagination.current;
                const delta = 2;
                const start = Math.max(1, current - delta);
                const end = Math.min(total, current + delta);

                const pages = [];
                for (let i = start; i <= end; i++) {
                    pages.push(i);
                }
                return pages;
            }
        },
        methods: {
            async fetchDI(page = 1) {
                try {
                    const res = await axios.get('/api/master/daerah-irigasi', {
                        params: {
                            page,
                            per_page: this.perPage,
                            search: this.search
                        }
                    });

                    this.daerahIrigasis = res.data.data;
                    this.pagination = {
                        current: res.data.current_page,
                        last: res.data.last_page,
                        total: res.data.total
                    };
                } catch (err) {
                    console.error(err);
                    alert("Gagal memuat data daerah irigasi");
                }
            },
            async fetchKabupatens() {
                const res = await axios.get('/api/master/kabupaten');
                this.kabupatens = res.data;
            },
            async saveDI() {
                try {
                    if (this.form.id) {
                        await axios.put(`/api/master/daerah-irigasi/${this.form.id}`, this.form);
                    } else {
                        await axios.post('/api/master/daerah-irigasi', this.form);
                    }
                    this.fetchDI(this.pagination.current);
                    bootstrap.Modal.getInstance(document.getElementById('diModal')).hide();
                } catch (err) {
                    console.error(err);
                    alert("Gagal menyimpan data");
                }
            },
            async deleteDI(id) {
                if (confirm("Yakin ingin menghapus DI ini?")) {
                    await axios.delete(`/api/master/daerah-irigasi/${id}`);
                    this.fetchDI(this.pagination.current);
                }
            },
            openForm() {
                this.form = {
                    id: null,
                    nama: '',
                    luas_baku: 0,
                    luas_potensial: 0,
                    luas_fungsional: 0,
                    parent_id: '',
                    kabupaten_ids: []
                };
                new bootstrap.Modal(document.getElementById('diModal')).show();
            },
            editDI(di) {
                this.form = {
                    id: di.id,
                    nama: di.nama,
                    luas_baku: di.luas_baku,
                    luas_potensial: di.luas_potensial,
                    luas_fungsional: di.luas_fungsional,
                    kabupaten_ids: di.kabupatens.map(k => k.id),
                    parent_id: di.parent ? di.parent.id : ''
                };
                new bootstrap.Modal(document.getElementById('diModal')).show();
            }
        },
        mounted() {
            this.fetchDI();
            this.fetchKabupatens();
        }
    }).mount("#app");
</script>
@endpush