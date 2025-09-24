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
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nama DI</th>
                        <th>Kabupaten Terkait</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="di in daerahIrigasis" :key="di.id">
                        <td>@{{ di.nama }}</td>
                        <td>
                            <span v-for="kab in di.kabupatens" class="badge bg-info me-1">
                                @{{ kab.nama }}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-warning me-2" @click="editDI(di)">Edit</button>
                            <button class="btn btn-sm btn-danger" @click="deleteDI(di.id)">Hapus</button>
                        </td>
                    </tr>
                </tbody>
            </table>
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
                token: localStorage.getItem("token"), // simpan di data()

                daerahIrigasis: [],
                kabupatens: [],
                form: {
                    id: null,
                    nama: '',
                    kabupaten_ids: []
                }
            }
        },
        methods: {
            async fetchDI() {
                // let res = await axios.get('/api/master/daerah-irigasi');
                let res = await axios.get('/api/master/daerah-irigasi');
                this.daerahIrigasis = res.data;
            },
            async fetchKabupatens() {
                let res = await axios.get('/api/master/kabupaten');
                this.kabupatens = res.data;
            },
            openForm() {
                this.form = {
                    id: null,
                    nama: '',
                    kabupaten_ids: []
                };
                new bootstrap.Modal(document.getElementById('diModal')).show();
            },
            editDI(di) {
                this.form = {
                    id: di.id,
                    nama: di.nama,
                    kabupaten_ids: di.kabupatens.map(k => k.id)
                };
                new bootstrap.Modal(document.getElementById('diModal')).show();
            },
            async saveDI() {
                try {
                    if (this.form.id) {
                        await axios.put('/api/master/daerah-irigasi/' + this.form.id, this.form);
                    } else {
                        await axios.post('/api/master/daerah-irigasi', this.form);
                    }
                    this.fetchDI();
                    bootstrap.Modal.getInstance(document.getElementById('diModal')).hide();
                } catch (err) {
                    console.error(err);
                    alert("Gagal menyimpan data");
                }
            },
            async deleteDI(id) {
                if (confirm("Yakin ingin menghapus DI ini?")) {
                    await axios.delete('/api/master/daerah-irigasi/' + id);
                    this.fetchDI();
                }
            }
        },
        mounted() {
            this.fetchDI();
            this.fetchKabupatens();
        }
    }).mount("#app");
</script>
@endpush