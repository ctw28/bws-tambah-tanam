@extends('admin.template')

@section('content')
<div id="app" v-cloak class="container mt-4">
    <div class="card">
        <div class="card-body">

            <h3>Manajemen Pengamat</h3>

            <!-- Tombol tambah -->
            <button class="btn btn-primary mb-3" @click="openModal()">+ Tambah Pengamat</button>

            <!-- Tabel data -->
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>DI</th>
                        <th>Nama</th>
                        <th>Nomor HP</th>
                        <th>Sesi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item,index) in pengamats" :key="item.id">
                        <td>@{{index + 1}}</td>
                        <td>@{{ item.daerah_irigasi?.nama }}</td>
                        <td>@{{ item.nama }}</td>
                        <td>@{{ item.nomor_hp }}</td>
                        <td>@{{ item.sesi?.nama }}</td>
                        <td>
                            <button class="btn btn-sm btn-success me-2" @click="sendKode(item)">Kirim Kode</button>
                            <button class="btn btn-sm btn-warning me-2" @click="openModal(item)">Edit</button>
                            <button class="btn btn-sm btn-danger" @click="remove(item.id)">Hapus</button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Modal Tambah/Edit -->
            <div class="modal fade" id="pengamatModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form @submit.prevent="save">
                            <div class="modal-header">
                                <h5 class="modal-title">@{{ form.id ? 'Edit Pengamat' : 'Tambah Pengamat' }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label>Nama</label>
                                    <input type="text" class="form-control" v-model="form.nama" required>
                                </div>
                                <div class="mb-3">
                                    <label>Nomor HP</label>
                                    <input type="text" class="form-control" v-model="form.nomor_hp">
                                </div>
                                <div class="mb-3">
                                    <label>Sesi</label>
                                    <select class="form-control" v-model="form.sesi_id" required>
                                        <option v-for="s in sesis" :value="s.id">@{{ s.nama }}</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label>Pilih DI</label>
                                    <div v-for="di in daerahIrigasis" :key="di.id" class="form-check">
                                        <input type="radio" class="form-check-input" :value="di.id"
                                            v-model="form.daerah_irigasi_id">
                                        <label class="form-check-label">@{{ di.nama }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<!-- Vue + Axios -->
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<!-- Bootstrap Modal JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const {
        createApp
    } = Vue;

    createApp({
        data() {
            return {
                pengamats: [],
                sesis: [],
                daerahIrigasis: [],
                form: {
                    id: null,
                    nama: '',
                    nomor_hp: '',
                    sesi_id: '',
                    daerah_irigasi_id: ''
                },
                modal: null,
            }
        },
        mounted() {
            this.fetchPengamats();
            this.fetchSesis();
            this.fetchDI();
            this.modal = new bootstrap.Modal(document.getElementById('pengamatModal'));
        },
        methods: {
            async fetchPengamats() {
                let res = await axios.get('/api/pengamat');
                this.pengamats = res.data;
            },
            async fetchSesis() {
                let res = await axios.get('/api/master/sesi');
                this.sesis = res.data;
            },
            async fetchDI() {
                let res = await axios.get('/api/master/daerah-irigasi');
                this.daerahIrigasis = res.data;
            },
            openModal(item = null) {
                if (item) {
                    this.form = {
                        ...item
                    };
                } else {
                    this.form = {
                        id: null,
                        nama: '',
                        nomor_hp: '',
                        sesi_id: '',
                        daerah_irigasi_id: ''
                    };
                }
                this.modal.show();
            },
            async save() {
                try {
                    console.log(this.form.id);

                    if (this.form.id) {
                        await axios.put(`/api/master/pengamat/${this.form.id}`, this.form);
                    } else {
                        await axios.post('/api/master/pengamat', this.form);
                    }
                    this.modal.hide();
                    this.fetchPengamats();
                } catch (e) {
                    alert(e.response.data.message);
                    console.error(e.response?.data || e);
                }
            },
            async remove(id) {
                if (!confirm("Yakin hapus data ini?")) return;
                await axios.delete(`/api/master/pengamat/${id}`);
                this.fetchPengamats();
            },
            async sendKode(p) {
                try {
                    let res = await axios.post(`/api/pengamat/${p.id}/send-kode`);
                    window.open(res.data.link, '_blank'); // buka WhatsApp link
                } catch (err) {
                    console.error(err);
                    alert('Gagal mengirim kode WA');
                }
            },
        }
    }).mount('#app');
</script>
@endpush