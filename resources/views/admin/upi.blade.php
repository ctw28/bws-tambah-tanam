@extends('admin.template')

@section('content')
<div id="app" v-cloak class="container mt-4">
    <div class="card">
        <div class="card-body">

            <h3>Data UPI</h3>

            <button class="btn btn-primary mb-3" @click="openModal()">Tambah UPI</button>
            <div class="table-responsive">

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Daerah Irigasi</th>
                            <th>Nama</th>
                            <th>No HP</th>
                            <th>Sesi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(upi,index) in upis" :key="upi.id">
                            <td>@{{index+1}}</td>
                            <td>
                                <span v-for="di in upi.daerah_irigasis" class="badge bg-info me-1">@{{ di.nama }}</span>
                            </td>
                            <td>@{{ upi.nama }}</td>
                            <td>@{{ upi.no_hp }}</td>
                            <td>@{{ upi.sesi?.nama }}</td>
                            <td>
                                <button class="btn btn-sm btn-success me-2" @click="sendKode(upi)">Kirim Kode</button>

                                <button class="btn btn-sm btn-warning me-2" @click="editUpi(upi)">Edit</button>
                                <button class="btn btn-sm btn-danger" @click="deleteUpi(upi.id)">Hapus</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="upiModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@{{ editMode ? 'Edit UPI' : 'Tambah UPI' }}</h5>
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
                        <label>Pilih DI</label>
                        <div v-for="di in daerahIrigasis" :key="di.id" class="form-check">
                            <input type="checkbox" class="form-check-input" :value="di.id"
                                v-model="form.daerah_irigasi_ids">
                            <label class="form-check-label">@{{ di.nama }}</label>
                        </div>
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
                    <button class="btn btn-primary" @click="saveUpi">@{{ editMode ? 'Update' : 'Simpan' }}</button>
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
                upis: [],
                daerahIrigasis: [],
                sesis: [],
                form: {
                    id: null,
                    nama: '',
                    no_hp: '',
                    sesi_id: '',
                    daerah_irigasi_ids: []
                },
                editMode: false,
            }
        },
        mounted() {
            this.fetchUpis();
            this.fetchMaster();
        },
        methods: {
            async fetchUpis() {
                let res = await axios.get('/api/master/upi');
                this.upis = res.data;
            },
            async fetchMaster() {
                this.daerahIrigasis = (await axios.get('/api/master/daerah-irigasi?per_page=all')).data;
                this.sesis = (await axios.get('/api/master/sesi')).data;
            },
            openModal() {
                this.editMode = false;
                this.form = {
                    id: null,
                    nama: '',
                    no_hp: '',
                    sesi_id: '',
                    daerah_irigasi_ids: []
                };
                new bootstrap.Modal(document.getElementById('upiModal')).show();
            },
            editUpi(upi) {
                this.editMode = true;
                this.form = {
                    id: upi.id,
                    nama: upi.nama,
                    no_hp: upi.no_hp,
                    sesi_id: upi.sesi_id,
                    daerah_irigasi_ids: upi.daerah_irigasis.map(d => d.id)
                };
                new bootstrap.Modal(document.getElementById('upiModal')).show();
            },
            async saveUpi() {
                if (this.editMode) {
                    await axios.put(`/api/master/upi/${this.form.id}`, this.form);
                } else {
                    await axios.post('/api/master/upi', this.form);
                }
                bootstrap.Modal.getInstance(document.getElementById('upiModal')).hide();
                this.fetchUpis();
            },
            async deleteUpi(id) {
                if (confirm("Yakin hapus?")) {
                    await axios.delete(`/api/master/upi/${id}`);
                    this.fetchUpis();
                }
            },
            async sendKode(p) {
                try {
                    let res = await axios.post(`/api/upi/${p.id}/send-kode`);
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