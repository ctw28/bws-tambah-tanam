@extends('admin.template')

@section('content')
<div id="app" v-cloak class="container mt-4">


    <!-- Tabel Petugas -->
    <div class="card shadow">
        <div class="card-body">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Kelola Petugas</h4>
                <button class="btn btn-primary" @click="openForm()">+ Tambah Petugas</button>
            </div>
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Saluran</th>
                        <th>Aktif</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(p, index) in petugas" :key="p.id">
                        <td>@{{ index + 1 }}</td>
                        <td>@{{ p.nama }}</td>
                        <td>
                            <ul class="mb-0">
                                <li v-for="s in p.salurans">@{{ s.nama }} (DI
                                    @{{ s.daerah_irigasi.nama }})</li>
                            </ul>
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" v-model="p.is_aktif"
                                    @change="toggleAktif(p)">
                            </div>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-success me-2" @click="sendKode(p)">Kirim Kode</button>
                            <button class="btn btn-sm btn-warning me-2" @click="editPetugas(p)">Edit</button>
                            <button class="btn btn-sm btn-danger" @click="deletePetugas(p.id)">Hapus</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal Form -->
        <div class="modal fade" id="petugasModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form @submit.prevent="savePetugas">
                        <div class="modal-header">
                            <h5 class="modal-title">@{{ form.id ? 'Edit Petugas' : 'Tambah Petugas' }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">

                            <!-- Nama -->
                            <div class="mb-3">
                                <label class="form-label">Nama Petugas</label>
                                <input type="text" v-model="form.nama" class="form-control" required>
                            </div>

                            <!-- Kode (hanya saat tambah) -->
                            <div class="mb-3">
                                <label class="form-label">No WA</label>
                                <input type="text" v-model="form.hp" class="form-control" required>
                            </div>

                            <!-- Saluran -->
                            <div class="mb-3">
                                <label class="form-label">Saluran Ditugaskan</label>
                                <div v-for="s in salurans" :key="s.id" class="form-check">
                                    <input type="checkbox" class="form-check-input" :id="'sal-'+s.id" :value="s.id"
                                        v-model="form.saluran_ids">
                                    <label :for="'sal-'+s.id" class="form-check-label">
                                        @{{ s.nama }} (DI: @{{ s.daerah_irigasi.nama }})
                                    </label>
                                </div>
                            </div>

                            <!-- Aktif -->
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" v-model="form.is_aktif"
                                    id="aktifSwitch">
                                <label class="form-check-label" for="aktifSwitch">Aktif</label>
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

</div>
@endsection
@push('scripts')

<!-- Vue + Axios -->
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script>
    const {
        createApp
    } = Vue;

    createApp({
        data() {
            return {
                petugas: [],
                salurans: [],
                form: {
                    id: null,
                    sesi_id: 1,
                    nama: '',
                    hp: '',
                    is_aktif: true,
                    saluran_ids: []
                }
            }
        },
        methods: {
            async fetchData() {
                let res = await axios.get('/api/petugas');
                console.log(res.data);

                // ubah tiap item is_aktif jadi boolean
                this.petugas = res.data.map(p => ({
                    ...p,
                    is_aktif: Boolean(p.is_aktif)
                }));
            },
            async fetchSalurans() {
                let res = await axios.get('/api/master/saluran');
                this.salurans = res.data;
            },
            async sendKode(p) {
                try {
                    let res = await axios.post(`/api/petugas/${p.id}/send-kode`);
                    window.open(res.data.link, '_blank'); // buka WhatsApp link
                } catch (err) {
                    console.error(err);
                    alert('Gagal mengirim kode WA');
                }
            },
            openForm() {
                this.form = {
                    id: null,
                    sesi_id: 1,
                    nama: '',
                    hp: '',
                    is_aktif: true,
                    saluran_ids: []
                };
                new bootstrap.Modal(document.getElementById('petugasModal')).show();
            },
            editPetugas(p) {
                this.form = {
                    id: p.id,
                    sesi_id: 1,
                    nama: p.nama,
                    hp: p.hp,
                    is_aktif: p.is_aktif,
                    saluran_ids: p.salurans.map(s => s.id)
                };
                new bootstrap.Modal(document.getElementById('petugasModal')).show();
            },
            async savePetugas() {
                try {
                    if (this.form.id) {
                        await axios.put(`/api/master/petugas/${this.form.id}`, this.form);
                    } else {
                        await axios.post('/api/master/petugas', this.form);
                    }
                    this.fetchData();
                    bootstrap.Modal.getInstance(document.getElementById('petugasModal')).hide();
                } catch (err) {
                    console.error(err);
                    alert('Gagal menyimpan data');
                }
            },
            async deletePetugas(id) {
                if (confirm('Yakin ingin menghapus petugas ini?')) {
                    await axios.delete(`/api/master/petugas/${id}`);
                    this.fetchData();
                }
            },
            async toggleAktif(p) {
                try {
                    await axios.put(`/api/master/petugas/${p.id}`, {
                        nama: p.nama,
                        is_aktif: p.is_aktif,
                        hp: p.hp
                    });
                } catch (err) {
                    console.error(err);
                    alert('Gagal update status aktif');
                }
            }
        },
        mounted() {
            this.fetchData();
            this.fetchSalurans();
        }
    }).mount('#app');
</script>
@endpush