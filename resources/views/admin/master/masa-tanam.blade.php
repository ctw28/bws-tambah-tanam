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

                <div class="col-md-5">
                    <label class="form-label fw-bold">Daerah Irigasi</label>
                    <select class="form-select" v-model="filter.di" @change="hideFilter">
                        <option value="">-- Pilih Daerah Irigasi --</option>
                        <option v-for="d in daerahIrigasis" :value="d.id">
                            @{{ d.nama }}
                        </option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Tahun</label>
                    <select class="form-control" v-model="filter.tahun" @change="hideFilter">
                        <option value="2025">2025</option>
                        <option value="2026">2026</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <button class="btn btn-primary" @click="loadData()">
                        Filter
                    </button>
                </div>



            </div>
        </div>
    </div>


    <!-- ================= CARD DATA ================= -->
    <!-- Area SK singkat (menampilkan SK untuk DI+Tahun, bisa diubah) -->
    <div v-if="isFiltered">

        <div class="card">
            <div class="card-body">

                <div class="col-12 mt-3">
                    <div class="card p-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div><strong>SK Masa Tanam :</strong> DI @{{ selectedDINama }} @{{ filter.tahun ? ' - ' + filter.tahun : '' }}</div>
                                <div v-if="skData">
                                    @{{ skData.nama_sk }}
                                </div>
                                <div v-else class="text-muted">Belum ada SK .</div>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-outline-primary me-2" :disabled="!isFiltered" @click="openSKModal">Edit SK</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                        <tr class="text-center align-middle">
                            <th width="60">No</th>
                            <th>Nama <br> Masa Tanam</th>
                            <th>Bulan Mulai - Selesai</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in items" :key="item.id">
                            <td>@{{ index + 1 }}</td>
                            <td>@{{ item.nama }}</td>
                            <td>@{{ namaBulan(item.bulan_mulai) }} - @{{ namaBulan(item.bulan_selesai) }}</td>
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

            </div>
        </div>
    </div>

    <!-- MODAL SK -->
    <div class="modal fade" id="modalSK" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">SK untuk @{{ selectedDINama }} @{{ filter.tahun }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label fw-bold">Nama SK</label>
                        <input type="text" class="form-control" v-model="skForm.nama_sk">
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" @click="saveSK">Simpan SK</button>
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

                    <div class="mb-3">
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
                is_loading: false,
                form: {
                    id: null,
                    daerah_irigasi_id: '',
                    tahun: '',
                    nama: '',
                    bulan_mulai: '',
                    bulan_selesai: '',
                    // HAPUS field SK dari form masa tanam — SK disimpan terpisah
                },

                // SK data per DI+Tahun
                skData: null,
                skForm: {
                    id: '',
                    nama_sk: '',
                    tahun_sk: '',
                },

                modalInstance: null,
                modalSKInstance: null,

                filter: {
                    di: '',
                    tahun: '2025'
                },
                isFiltered: false
            };
        },

        mounted() {
            this.loadDI();
        },

        computed: {
            selectedDINama() {
                const d = this.daerahIrigasis.find(x => x.id === this.filter.di);
                return d ? d.nama : '';
            }
        },

        methods: {
            hideFilter() {

                this.isFiltered = false;
                this.items = [];
                this.skData = null;
            },
            resetFilter() {
                this.isFiltered = false;
                this.items = [];
                this.skData = null;
            },

            async loadDI() {
                let res = await axios.get('/api/master/daerah-irigasi?page=all&&kabupaten_id=9');
                this.daerahIrigasis = res.data.data;
            },
            async loadSK() {
                if (!this.filter.di || !this.filter.tahun) return;

                const res = await axios.get('/api/masa-tanam-sk', {
                    params: {
                        daerah_irigasi_id: this.filter.di,
                        tahun_sk: this.filter.tahun
                    }
                });
                console.log(res.data);
                this.skData = res.data

                if (res.data) {
                    this.skForm.id = res.data.id;
                    this.skForm.nama_sk = res.data.nama_sk;
                } else {
                    this.skForm.id = null;
                    this.skForm.nama_sk = '';
                }
            },

            openSKModal() {
                if (!this.filter.di || !this.filter.tahun) {
                    alert('Pilih Daerah Irigasi dan Tahun terlebih dahulu.');
                    return;
                }

                // isi skForm dari skData kalau ada
                if (this.skData) {
                    this.skForm = {
                        id: this.skData.id,
                        tahun_sk: this.filter.tahun || '',
                        nama_sk: this.skData.nama_sk || '',
                    };
                } else {
                    this.skForm = {
                        id: null,
                        tahun_sk: this.filter.tahun || '',
                        nama_sk: '',
                    };
                }


                const modal = document.getElementById('modalSK');
                this.modalSKInstance = new bootstrap.Modal(modal);
                this.modalSKInstance.show();
            },

            async saveSK() {
                try {
                    if (!this.filter.di || !this.filter.tahun) {
                        alert('Pilih DI dan Tahun dulu.');
                        return;
                    }

                    const payload = {
                        daerah_irigasi_id: this.filter.di,
                        nama_sk: this.skForm.nama_sk,
                        tahun_sk: this.filter.tahun, // kalau memang ini yg Anda pakai
                    };
                    // Jika sudah ada ID → Update
                    if (this.skForm.id) {
                        await axios.put(`/api/masa-tanam-sk/${this.skForm.id}`, payload);
                    } else {
                        // Jika belum ada ID → Create
                        await axios.post('/api/masa-tanam-sk', payload);
                    }

                    this.modalSKInstance.hide();
                    await this.loadSK();
                    alert('SK tersimpan.');
                } catch (err) {
                    console.error(err);
                    alert('Gagal menyimpan SK.');
                }
            },

            async loadData() {
                if (!this.filter.di || !this.filter.tahun) {
                    alert('Silakan pilih Daerah Irigasi dan Tahun dulu');
                    return;
                }

                try {
                    this.is_loading = true;
                    this.isFiltered = true;
                    // pastikan juga load SK
                    await this.loadSK();

                    const params = new URLSearchParams({
                        daerah_irigasi_id: this.filter.di,
                        tahun: this.filter.tahun
                    });

                    const res = await axios.get(`/api/masa-tanam?${params.toString()}`);

                    this.items = res.data;
                    console.log(this.items);

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
                if (!this.filter.di || !this.filter.tahun) {
                    alert('Pilih Daerah Irigasi dan Tahun di filter dulu!');
                    return;
                }
                this.resetForm();
                // pastikan form.tahun diisi dari filter (SK per tahun)
                this.form.tahun = this.filter.tahun;
                this.form.daerah_irigasi_id = this.filter.di;
                this.showModal();
            },

            edit(item) {
                // form tetap tidak mengandung SK fields
                this.form = {
                    id: item.id,
                    daerah_irigasi_id: item.daerah_irigasi_id,
                    tahun: item.tahun,
                    nama: item.nama,
                    bulan_mulai: item.bulan_mulai,
                    bulan_selesai: item.bulan_selesai
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
                    if (!this.filter.di || !this.filter.tahun) {
                        alert('Pilih Daerah Irigasi dan Tahun terlebih dahulu di filter atas!');
                        return;
                    }

                    if (!this.form.nama || !this.form.bulan_mulai || !this.form.bulan_selesai) {
                        alert('Lengkapi data!');
                        return;
                    }

                    // pastikan gunakan DI dan Tahun dari filter (prevent mismatch)
                    this.form.daerah_irigasi_id = this.filter.di;
                    this.form.tahun = this.filter.tahun;

                    if (this.form.id) {
                        await axios.put(`/api/masa-tanam/${this.form.id}`, this.form);
                    } else {
                        await axios.post('/api/masa-tanam', this.form);
                    }

                    this.modalInstance.hide();
                    this.loadData();

                } catch (err) {
                    console.error(err);
                    alert('Gagal menyimpan data');
                }
            },

            async hapus(id) {
                if (!confirm('Yakin ingin menghapus data ini?')) return;

                try {
                    await axios.delete(`/api/masa-tanam/${id}`);
                    this.loadData();
                } catch (err) {
                    console.error(err);
                    alert('Gagal menghapus data');
                }
            },

            formatTanggal(tgl) {
                if (!tgl) return '-';
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