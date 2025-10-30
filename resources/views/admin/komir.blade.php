@extends('admin.template')

@section('content')
<div id="app" v-cloak class="container mt-4">
    <div class="card">
        <div class="card-body">

            <h3>Data komir</h3>

            <button class="btn btn-primary mb-3" @click="openModal()">Tambah komir</button>
            <div class="table-responsive">

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>No HP</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(komir,index) in komirs" :key="komir.id">
                            <td>@{{index+1}}</td>
                            <td>@{{ komir.nama }}</td>
                            <td>@{{ komir.no_hp }}</td>
                            <td>
                                <button class="btn btn-sm btn-success me-2" @click="sendKode(komir)">Kirim Kode</button>

                                <button class="btn btn-sm btn-warning me-2" @click="editkomir(komir)">Edit</button>
                                <button class="btn btn-sm btn-danger" @click="deletekomir(komir.id)">Hapus</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="komirModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@{{ editMode ? 'Edit komir' : 'Tambah komir' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label>Nama</label>
                        <input type="text" class="form-control" v-model="form.nama">
                    </div>
                    <div class="mb-2">
                        <label>No HP</label>
                        <input type="text" class="form-control" v-model="form.no_hp">
                    </div>
                    <div class="mb-2">
                        <label>Sesi</label>
                        <select class="form-select" v-model="form.sesi_id">
                            <option value="">Pilih Sesi</option>
                            <option v-for="s in sesis" :key="s.id" :value="s.id">@{{ s.nama }}</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" @click="savekomir">@{{ editMode ? 'Update' : 'Simpan' }}</button>
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
                komirs: [],
                sesis: [],
                form: {
                    id: null,
                    nama: '',
                    no_hp: '',
                    sesi_id: '',
                },
                editMode: false,
            }
        },
        mounted() {
            this.fetchMaster();
            this.fetchkomirs();
        },
        methods: {
            async fetchkomirs() {
                let res = await axios.get('/api/master/komir');
                this.komirs = res.data;
            },
            async fetchMaster() {
                this.sesis = (await axios.get('/api/master/sesi')).data;
            },

            openModal() {
                this.editMode = false;
                this.form = {
                    id: null,
                    nama: '',
                    no_hp: '',
                    sesi_id: '',
                };
                new bootstrap.Modal(document.getElementById('komirModal')).show();
            },

            editkomir(komir) {
                this.editMode = true;
                this.form = {
                    id: komir.id,
                    nama: komir.nama,
                    no_hp: komir.no_hp,
                    sesi_id: komir.sesi_id,
                };
                new bootstrap.Modal(document.getElementById('komirModal')).show();
            },
            async savekomir() {
                if (this.editMode) {
                    await axios.put(`/api/master/komir/${this.form.id}`, this.form);
                } else {
                    await axios.post('/api/master/komir', this.form);
                }
                bootstrap.Modal.getInstance(document.getElementById('komirModal')).hide();
                this.fetchkomirs();
            },
            async deletekomir(id) {
                if (confirm("Yakin hapus?")) {
                    await axios.delete(`/api/master/komir/${id}`);
                    this.fetchkomirs();
                }
            },
            async sendKode(p) {
                try {
                    let res = await axios.post(`/api/komir/${p.id}/send-kode`);
                    window.open(res.data.link, '_blank'); // buka WhatsApp link
                } catch (err) {
                    console.error(err);
                    alert('Gagal mengirim kode WA');
                }
            },
        }
    }).mount("#app");
</script>
@endpush