@extends('admin.template')

@section('content')
<div id="app" v-cloak class="container mt-4">

    <!-- ================= CARD FILTER ================= -->
    <div class="card mb-3">
        <div class="card-header">
            <h5 class="mb-0">Filter Data</h5>
        </div>
        <div class="card-body">
            <div class="row g-3 align-items-end">

                <div class="col-md-4">
                    <label class="form-label fw-bold">Daerah Irigasi</label>
                    <select class="form-select" v-model="filter.di" @change="resetFilter">
                        <option value="">-- Pilih Daerah Irigasi --</option>
                        <option v-for="d in daerahIrigasis" :value="d.id">
                            @{{ d.nama }}
                        </option>
                    </select>
                </div>

                <div class="col-md-2">
                    <button class="btn btn-primary w-100" @click="loadData(1)">
                        Tampilkan
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- ================= CARD DATA ================= -->
    <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Data Masa Tanam</h5>

            <button class="btn btn-primary btn-sm"
                :disabled="!isFiltered"
                @click="openModalTambah">
                + Tambah Masa Tanam
            </button>
        </div>

        <div class="card-body table-responsive">

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="60">No</th>
                        <th>Tahun</th>
                        <th>Daerah Irigasi</th>
                        <th>Nama Masa Tanam</th>
                        <th>Bulan Mulai</th>
                        <th>Bulan Selesai</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in items" :key="item.id">
                        <td>@{{ index + 1 }}</td>
                        <td>@{{ item.tahun }}</td>
                        <td>@{{ item.daerah_irigasi.nama }}</td>
                        <td>@{{ item.nama }}</td>
                        <td>@{{ namaBulan(item.bulan_mulai) }}</td>
                        <td>@{{ namaBulan(item.bulan_selesai) }}</td>
                        <td>
                            <button class="btn btn-sm btn-warning me-2" @click="edit(item)">Edit</button>
                            <button class="btn btn-sm btn-danger" @click="hapus(item.id)">Hapus</button>
                        </td>
                    </tr>

                    <tr v-if="items.length === 0">
                        <td colspan="7" class="text-center text-muted">Belum ada data</td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>Total: @{{ pagination.total }}</div>
                <div class="d-flex gap-1">
                    <button class="btn btn-sm btn-light"
                        :disabled="pagination.current === 1"
                        @click="loadData(pagination.current - 1)">
                        Prev
                    </button>

                    <button v-for="page in pagination.last"
                        :key="page"
                        class="btn btn-sm"
                        :class="page === pagination.current ? 'btn-primary' : 'btn-light'"
                        @click="loadData(page)">
                        @{{ page }}
                    </button>

                    <button class="btn btn-sm btn-light"
                        :disabled="pagination.current === pagination.last"
                        @click="loadData(pagination.current + 1)">
                        Next
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- ================= MODAL ================= -->
    <div class="modal fade" id="modalForm" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        @{{ form.id ? 'Edit Masa Tanam' : 'Tambah Masa Tanam' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Tahun</label>
                        <input type="number" class="form-control" v-model="form.tahun">
                    </div>

                    <div class="mb-2">
                        <label>Masa Tanam</label>
                        <select class="form-select" v-model="form.nama">
                            <option value="" disabled>Pilih Masa</option>
                            <option value="I">Masa Tanam I</option>
                            <option value="II">Masa Tanam II</option>
                            <option value="III">Masa Tanam III</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Bulan Mulai</label>
                        <select class="form-select" v-model="form.bulan_mulai">
                            <option value="">Pilih</option>
                            <option v-for="b in 12" :value="b">@{{ namaBulan(b) }}</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Bulan Selesai</label>
                        <select class="form-select" v-model="form.bulan_selesai">
                            <option value="">Pilih</option>
                            <option v-for="b in 12" :value="b">@{{ namaBulan(b) }}</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" @click="simpan">Simpan</button>
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
                daerahIrigasis: [],
                pagination: {
                    current: 1,
                    last: 1,
                    total: 0
                },
                perPage: 10,
                is_loading: false,

                form: {
                    id: null,
                    daerah_irigasi_id: '',
                    tahun: '',
                    nama: '',
                    bulan_mulai: '',
                    bulan_selesai: ''
                },

                modalInstance: null,
                filter: {
                    di: ''
                },
                isFiltered: false

            };
        },

        mounted() {
            this.loadDI();
            // this.loadData();
        },


        methods: {
            resetFilter() {
                this.isFiltered = false
                this.items = []
            },
            async loadDI() {
                // let res = await axios.get('/api/master/daerah-irigasi?page=all&kabupaten_id=9');
                let res = await axios.get('/api/master/daerah-irigasi?page=all&&kabupaten_id=9');

                console.log(res.data.data);
                this.daerahIrigasis = res.data.data;
            },
            async loadData(page = 1) {
                if (!this.filter.di) {
                    alert('Silakan pilih Daerah Irigasi dulu');
                    return;
                }

                try {
                    this.is_loading = true;
                    this.isFiltered = true
                    const params = new URLSearchParams({
                        page,
                        per_page: this.perPage,
                        daerah_irigasi_id: this.filter.di
                    });

                    const res = await axios.get(`/api/masa-tanam?${params.toString()}`);

                    this.items = res.data.data;
                    this.pagination.current = res.data.current_page;
                    this.pagination.last = res.data.last_page;
                    this.pagination.total = res.data.total;
                } catch (err) {
                    console.error(err);
                } finally {
                    this.is_loading = false;
                }
            },



            namaBulan(bulan) {
                const nama = [
                    '', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                ];
                return nama[bulan];
            },

            openModalTambah() {
                this.resetForm();
                this.showModal();
            },

            edit(item) {
                this.form = {
                    ...item
                };
                this.showModal();
            },

            showModal() {
                const modal = document.getElementById('modalForm');
                this.modalInstance = new bootstrap.Modal(modal);
                this.modalInstance.show();
            },

            resetForm() {
                this.form = {
                    id: null,
                    daerah_irigasi_id: '',
                    tahun: '',
                    nama: '',
                    bulan_mulai: '',
                    bulan_selesai: ''
                };
            },

            async simpan() {
                try {
                    if (!this.filter.di) {
                        alert('Pilih Daerah Irigasi terlebih dahulu di filter atas!');
                        return;
                    }

                    if (!this.form.tahun || !this.form.nama || !this.form.bulan_mulai || !this.form.bulan_selesai) {
                        alert('Lengkapi data!');
                        return;
                    }

                    // ✅ pakai DI dari filter, bukan dari select form
                    this.form.daerah_irigasi_id = this.filter.di;

                    if (this.form.id) {
                        await axios.put(`/api/masa-tanam/${this.form.id}`, this.form);
                    } else {
                        await axios.post('/api/masa-tanam', this.form);
                    }

                    this.modalInstance.hide();
                    this.loadData(this.pagination.current);

                } catch (err) {
                    console.error(err);
                    alert('Gagal menyimpan data');
                }
            },


            async hapus(id) {
                if (!confirm('Yakin ingin menghapus data ini?')) return;

                try {
                    await axios.delete(`/api/masa-tanam/${id}`);
                    this.loadData(this.pagination.current);
                } catch (err) {
                    console.error(err);
                    alert('Gagal menghapus data');
                }
            }
        }
    }).mount('#app');
</script>
@endpush