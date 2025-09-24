@extends('admin.template')

@section('content')
<div id="app" v-cloak class="container-fluid">
    <div class="card mb-3">
        <div class="card-body">
            <h3>Kelola Saluran & Bangunan</h3>

            <!-- Pilih DI -->
            <label>Pilih Daerah Irigasi</label>
            <select v-model="selectedDI" @change="fetchSaluran" class="form-control">
                <option value="">-- Pilih DI --</option>
                <option v-for="di in daerahIrigasi" :value="di.id">
                    @{{ di.nama }}
                </option>
            </select>
        </div>
    </div>
    <div v-if="selectedDI" class="card mb-3">
        <div class="card-body">
            <!-- Tombol Tambah Saluran -->
            <button class="btn btn-primary btn-sm mb-3" @click="showAddSaluranModal">
                + Tambah Saluran
            </button>

            <!-- Tabel Saluran -->
            <h5>Saluran di @{{ getDINama }}</h5>
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>Kelola</th>
                        <th>Nama Saluran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="saluran in salurans" :key="saluran.id">
                        <td><button class="btn btn-sm btn-info" @click="fetchBangunan(saluran)">
                                Kelola Bangunan
                            </button></td>
                        <td>@{{ saluran.nama }}</td>
                        <td>
                            <button class="btn btn-sm btn-warning me-2" @click="showEditSaluranModal(saluran)">
                                Edit
                            </button>
                            <button class="btn btn-sm btn-danger" @click="deleteSaluran(saluran.id)">
                                Hapus
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- Modal Tambah Saluran -->
        <div class="modal fade" id="modalAddSaluran" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form @submit.prevent="storeSaluran">
                        <div class="modal-header">
                            <h5>Tambah Saluran</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label>Nama Saluran</label>
                                <input type="text" v-model="newSaluran.nama" class="form-control" required>
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
        <!-- Modal Edit Saluran -->
        <div class="modal fade" id="modalEditSaluran" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form @submit.prevent="updateSaluran">
                        <div class="modal-header">
                            <h5>Edit Saluran</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label>Nama Saluran</label>
                                <input type="text" v-model="editSaluran.nama" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div v-if="selectedSaluran" class="card mb-3">
        <div class="card-body">
            <!-- Tabel Bangunan -->
            <h5>Bangunan pada Saluran: @{{ selectedSaluran.nama }}</h5>
            <button class="btn btn-primary btn-sm mb-2" @click="openBangunanForm()">+ Tambah Bangunan</button>

            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>Kelola</th>
                        <th>Nama Bangunan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="b in bangunans" :key="b.id">
                        <td><button class="btn btn-sm btn-info" @click="lihatPetak(b)">Kelola Petak</button></td>
                        <td>@{{ b.nama }}</td>
                        <td>
                            <button class="btn btn-sm btn-warning me-2" @click="editBangunan(b)">Edit</button>
                            <button class="btn btn-sm btn-danger" @click="deleteBangunan(b.id)">Hapus</button>

                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Modal Form Bangunan -->
            <div class="modal fade" id="bangunanModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form @submit.prevent="saveBangunan">
                            <div class="modal-header">
                                <h5 class="modal-title">@{{ bangunanForm.id ? 'Edit Bangunan' : 'Tambah Bangunan' }}
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Nama Bangunan</label>
                                    <input type="text" v-model="bangunanForm.nama" class="form-control" required>
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

    <div v-if="selectedBangunan" class="card">
        <div class="card-body">
            <h5>Petak untuk Bangunan: @{{ selectedBangunan.nama }}</h5>
            <button class="btn btn-primary btn-sm mb-2" @click="showPetakForm()">Tambah Petak</button>
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Luas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="p in petaks" :key="p.id">
                        <td>@{{ p.nama }}</td>
                        <td>@{{ p.luas }}</td>
                        <td>
                            <button class="btn btn-sm btn-warning me-2" @click="showPetakForm(p)">Edit</button>
                            <button class="btn btn-sm btn-danger" @click="deletePetak(p.id)">Hapus</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- Modal Petak -->
        <div v-if="petakFormVisible" class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,0.5)">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">@{{ petakForm.id ? 'Edit' : 'Tambah' }} Petak</h5>
                        <button type="button" class="btn-close" @click="petakFormVisible=false"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Nama</label>
                            <input type="text" class="form-control" v-model="petakForm.nama">
                        </div>
                        <div class="mb-3">
                            <label>Luas</label>
                            <input type="number" class="form-control" v-model="petakForm.luas">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" @click="petakFormVisible=false">Batal</button>
                        <button class="btn btn-primary" @click="savePetak">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/vue@2"></script>

<script>
    new Vue({
        el: "#app",
        data: {
            daerahIrigasi: [],
            selectedDI: "",
            salurans: [],
            bangunans: [],
            petaks: [],
            selectedSaluran: "",
            selectedBangunan: "",
            bangunanForm: {
                id: null,
                nama: "",
                saluran_id: null
            },
            newSaluran: {
                nama: ""
            }, // untuk input saluran baru
            editSaluran: {
                id: null,
                nama: ""
            }, // edit
            petakForm: {
                id: null,
                nama: "",
                luas: 0,
                gambar_skema: '-',
                bangunan_id: null
            },
            petakFormVisible: false

        },
        computed: {
            getDINama() {
                let di = this.daerahIrigasi.find(d => d.id == this.selectedDI);
                return di ? di.nama : '';
            }
        },
        mounted() {
            this.fetchDI();
        },
        methods: {
            async fetchDI() {
                let res = await axios.get("/api/master/daerah-irigasi");
                this.daerahIrigasi = res.data;
            },
            async fetchSaluran() {
                if (!this.selectedDI) return;
                let res = await axios.get(`/api/master/saluran?daerah_irigasi_id=${this.selectedDI}`);
                this.salurans = res.data;
                this.bangunans = [];
                this.petaks = [];
                this.selectedSaluran = ''
                this.selectedBangunan = ''
            },
            async fetchBangunan(saluran) {
                this.selectedSaluran = saluran;
                let res = await axios.get(`/api/master/bangunan?saluran_id=${saluran.id}`);
                this.bangunans = res.data;
                this.selectedBangunan = ''

            },
            async kelolaPetak(bangunan) {
                this.selectedBangunan = bangunan;
                let res = await axios.get(`/api/master/petak?bangunan_id=${bangunan.id}`);
                this.petaks = res.data;
                $('#modalPetak').modal('show');
            },
            showAddSaluranModal() {
                this.newSaluran = {
                    nama: ""
                };
                $('#modalAddSaluran').modal('show');
            },
            async storeSaluran() {
                try {
                    await axios.post("/api/master/saluran", {
                        nama: this.newSaluran.nama,
                        daerah_irigasi_id: this.selectedDI
                    });
                    $('#modalAddSaluran').modal('hide');
                    this.fetchSaluran(); // refresh list saluran
                } catch (err) {
                    console.error(err);
                    alert("Gagal menyimpan saluran");
                }
            },
            // === Edit ===
            showEditSaluranModal(saluran) {
                this.editSaluran = {
                    id: saluran.id,
                    nama: saluran.nama
                };
                $('#modalEditSaluran').modal('show');
            },
            async updateSaluran() {
                try {
                    await axios.put(`/api/master/saluran/${this.editSaluran.id}`, {
                        nama: this.editSaluran.nama,
                        daerah_irigasi_id: this.selectedDI
                    });
                    $('#modalEditSaluran').modal('hide');
                    this.fetchSaluran();
                } catch (err) {
                    console.error(err);
                    alert("Gagal update saluran");
                }
            },
            // === Hapus ===
            async deleteSaluran(id) {
                if (!confirm("Yakin ingin hapus saluran ini?")) return;
                try {
                    await axios.delete(`/api/master/saluran/${id}`);
                    this.fetchSaluran();
                } catch (err) {
                    console.error(err);
                    alert("Gagal hapus saluran");
                }
            },
            openBangunanForm() {
                this.bangunanForm = {
                    id: null,
                    nama: '',
                    saluran_id: this.selectedSaluran.id
                };
                $('#bangunanModal').modal('show');
            },
            editBangunan(b) {
                this.bangunanForm = {
                    id: b.id,
                    nama: b.nama,
                    saluran_id: this.selectedSaluran.id
                };
                $('#bangunanModal').modal('show');
            },
            async saveBangunan() {
                try {
                    if (this.bangunanForm.id) {
                        await axios.put(`/api/master/bangunan/${this.bangunanForm.id}`, this.bangunanForm);
                    } else {
                        await axios.post(`/api/master/bangunan`, this.bangunanForm);
                    }
                    $('#bangunanModal').modal('hide');
                    this.fetchBangunan(this.selectedSaluran);
                } catch (err) {
                    console.error(err);
                    alert("Gagal simpan bangunan");
                }
            },
            async deleteBangunan(id) {
                if (!confirm("Yakin ingin hapus bangunan ini?")) return;
                try {
                    await axios.delete(`/api/master/bangunan/${id}`);
                    this.fetchBangunan(this.selectedSaluran);
                } catch (err) {
                    console.error(err);
                    alert("Gagal hapus bangunan");
                }
            },
            async lihatPetak(b) {
                this.selectedBangunan = b;
                let res = await axios.get(`/api/master/petak?bangunan_id=${b.id}`);
                this.petaks = res.data;
            },
            showPetakForm(p = null) {
                console.log(p);

                this.petakForm = p ? {
                    ...p
                } : {
                    bangunan_id: this.selectedBangunan.id,
                    nama: "",
                    luas: "",
                    gambar_skema: '-'
                };
                this.petakFormVisible = true;
                console.log(this.petakForm);

            },
            async savePetak() {
                console.log(this.petakForm);

                if (this.petakForm.id) {
                    await axios.put(`/api/master/petak/${this.petakForm.id}`, this.petakForm);
                } else {
                    await axios.post(`/api/master/petak`, this.petakForm);
                }
                this.petakFormVisible = false;
                this.lihatPetak(this.selectedBangunan);
            },
            async deletePetak(id) {
                if (!confirm("Yakin ingin hapus petak ini?")) return;
                await axios.delete(`/api/master/petak/${id}`);
                this.lihatPetak(this.selectedBangunan);
            }


        }
    });
</script>
@endpush