@extends('admin.template')

@section('content')
<div id="app" v-cloak class="container-fluid">
    <div class="card mb-3">
        <div class="card-body">
            <h3>Kelola Irigasi</h3>

            <!-- Select Daerah Irigasi -->
            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">Daerah Irigasi</label>
                <div class="col-sm-10">
                    <select v-model="selectedDI" @change="fetchSaluran" class="form-select">
                        <option value="">-- Pilih DI --</option>
                        <option v-for="di in daerahIrigasi" :value="di.id">@{{ di.nama }}</option>
                    </select>
                </div>
            </div>

            <!-- Select Saluran + Kelola -->

            <div class="mb-3 row" v-if="selectedDI">
                <label class="col-sm-2 col-form-label">
                    Saluran
                    <br>
                    <a href="#" @click.prevent="showKelolaSaluran" class="small text-primary">
                        <i class="bx bx-arrow-in-up-right-stroke-square"></i> Kelola Saluran
                    </a>
                </label>
                <div class="col-sm-8">
                    <select v-model="selectedSaluran" @change="fetchBangunan" class="form-select">
                        <option value="">-- Pilih Saluran --</option>
                        <option v-for="s in salurans" :key="s.id" :value="s.id">
                            @{{ s.nama }}
                        </option>
                    </select>
                </div>
            </div>


            <!-- Select Bangunan + Kelola -->
            <div class="mb-3 row" v-if="selectedSaluran">
                <label class="col-sm-2 col-form-label">
                    Bangunan
                    <br>
                    <a href="#" @click.prevent="showKelolaBangunan" class="small text-primary">
                        <i class="bx bx-arrow-in-up-right-stroke-square"></i> Kelola Bangunan
                    </a>
                </label>
                <div class="col-sm-8">
                    <select v-model="selectedBangunan" @change="fetchPetak" class="form-select">
                        <option value="">-- Pilih Bangunan --</option>
                        <option v-for="b in bangunans" :value="b.id">@{{ b.nama }}</option>
                    </select>
                </div>
            </div>

            <!-- Select Petak + Kelola -->
            <div v-if="selectedBangunan" class="mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h4>Data Petak</h4>
                        <!-- <button class="btn btn-primary btn-sm" @click="showPetakForm()">+ Tambah Petak</button> -->
                        <button class="btn btn-sm btn-primary" @click="showKelolaPetak">+ Tambah Petak</button>
                    </div>
                    <!-- <button class="btn btn-primary btn-sm mb-3" @click="showAddPetakForm">+ Tambah Petak</button> -->

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Petak</th>
                                    <th>Luas (Ha)</th>
                                    <th>Gambar Skema</th>
                                    <th style="width:150px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(p,index) in petaks" :key="p.id">
                                    <td>@{{ index+1 }}</td>
                                    <td>@{{ p.nama }}</td>
                                    <td>@{{ p.luas }}</td>
                                    <td class="text-center">
                                        <template v-if="p.gambar_skema">
                                            <a :href="'/storage/' + p.gambar_skema" target="_blank">
                                                <img :src="'/storage/' + p.gambar_skema" alt="Skema Petak"
                                                    class="img-thumbnail"
                                                    style="width: 180px; height: auto; cursor: pointer;">
                                            </a>
                                        </template>
                                        <span v-else>-</span>
                                    </td>

                                    <td>
                                        <button class="btn btn-warning btn-sm me-1"
                                            @click="showEditPetakForm(p)">Edit</button>
                                        <button class="btn btn-danger btn-sm" @click="deletePetak(p.id)">Hapus</button>
                                    </td>
                                </tr>
                                <tr v-if="petaks.length===0">
                                    <td colspan="4" class="text-center">Belum ada petak</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Kelola Saluran -->
    <div class="modal fade" id="kelolaSaluranModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Kelola Saluran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <!-- Tombol Tambah -->
                    <button class="btn btn-primary btn-sm mb-3" @click="showAddSaluranForm">+ Tambah Saluran</button>

                    <!-- Tabel Saluran -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Saluran</th>
                                    <th style="width:150px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(s,index) in salurans" :key="s.id">
                                    <td>@{{ index+1 }}</td>

                                    <td>@{{ s.nama }}</td>
                                    <td>
                                        <button class="btn btn-warning btn-sm me-1"
                                            @click="showEditSaluranForm(s)">Edit</button>
                                        <button class="btn btn-danger btn-sm"
                                            @click="deleteSaluran(s.id)">Hapus</button>
                                    </td>
                                </tr>
                                <tr v-if="salurans.length===0">
                                    <td colspan="2" class="text-center">Belum ada saluran</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Form Tambah/Edit -->
                    <div v-if="saluranFormVisible" class="mt-3 border p-3 rounded bg-light">
                        <h6>@{{ saluranForm.id ? 'Edit Saluran' : 'Tambah Saluran' }}</h6>
                        <div class="mb-2">
                            <label>Nama Saluran</label>
                            <input type="text" v-model="saluranForm.nama" class="form-control">
                        </div>
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-secondary btn-sm me-2"
                                @click="saluranFormVisible=false">Batal</button>
                            <button class="btn btn-primary btn-sm" @click="saveSaluran">Simpan</button>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Kelola Bangunan -->
    <div class="modal fade" id="kelolaBangunanModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Kelola Bangunan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <button class="btn btn-primary btn-sm mb-3" @click="showAddBangunanForm">+ Tambah Bangunan</button>

                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Bangunan</th>
                                <th style="width:150px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(b,index) in bangunans" :key="b.id">
                                <td>@{{ index+1 }}</td>

                                <td>@{{ b.nama }}</td>
                                <td>
                                    <button class="btn btn-warning btn-sm me-1"
                                        @click="showEditBangunanForm(b)">Edit</button>
                                    <button class="btn btn-danger btn-sm" @click="deleteBangunan(b.id)">Hapus</button>
                                </td>
                            </tr>
                            <tr v-if="bangunans.length===0">
                                <td colspan="2" class="text-center">Belum ada bangunan</td>
                            </tr>
                        </tbody>
                    </table>

                    <div v-if="bangunanFormVisible" class="mt-3 border p-3 bg-light rounded">
                        <h6>@{{ bangunanForm.id ? 'Edit Bangunan' : 'Tambah Bangunan' }}</h6>
                        <div class="mb-2">
                            <label>Nama Bangunan</label>
                            <input type="text" v-model="bangunanForm.nama" class="form-control">
                        </div>
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-secondary btn-sm me-2"
                                @click="bangunanFormVisible=false">Batal</button>
                            <button class="btn btn-primary btn-sm" @click="saveBangunan">Simpan</button>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal Kelola Petak -->
    <div class="modal fade" id="kelolaPetakModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Kelola Petak</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">



                    <!-- Form Tambah/Edit -->
                    <div v-if="petakFormVisible" class="mt-3 border p-3 rounded bg-light">
                        <h6>@{{ petakForm.id ? 'Edit Petak' : 'Tambah Petak' }}</h6>
                        <div class="mb-2">
                            <label>Nama Petak</label>
                            <input type="text" v-model="petakForm.nama" class="form-control">
                        </div>
                        <div class="mb-2">
                            <label>Luas (Ha)</label>
                            <input type="number" v-model="petakForm.luas" step="0.01" class="form-control">
                        </div>
                        <div class="mb-2">
                            <label>Gambar Skema</label>
                            <input type="file" @change="handlePetakFile" class="form-control">
                            <div v-if="petakForm.gambar_preview" class="mt-2">
                                <img :src="petakForm.gambar_preview" alt="Preview" class="img-thumbnail"
                                    style="max-height:150px;">
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-secondary btn-sm me-2" @click="petakFormVisible=false">Batal</button>
                            <button class="btn btn-primary btn-sm" @click="savePetak">Simpan</button>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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
            salurans: [],
            bangunans: [],
            petaks: [],
            selectedDI: "",
            selectedSaluran: "",
            selectedBangunan: "",
            selectedPetak: "",
            saluranFormVisible: false,
            saluranForm: {
                id: null,
                nama: ""
            },
            bangunanFormVisible: false,
            bangunanForm: {
                id: null,
                nama: "",
                saluran_id: null
            },
            petakFormVisible: false,
            petakForm: {
                id: null,
                nama: "",
                luas: "",
                gambar_skema: null,
                gambar_preview: null
            },


        },
        mounted() {
            this.fetchDI();
        },
        methods: {
            async fetchDI() {
                let res = await axios.get("/api/master/daerah-irigasi?per_page=all");
                this.daerahIrigasi = res.data;
            },
            async fetchSaluran() {
                this.selectedSaluran = "";
                this.selectedBangunan = "";
                this.selectedPetak = "";
                if (!this.selectedDI) return;
                let res = await axios.get(`/api/master/saluran?daerah_irigasi_id=${this.selectedDI}`);
                this.salurans = res.data;
            },
            async fetchBangunan() {
                this.selectedBangunan = "";
                this.selectedPetak = "";
                if (!this.selectedSaluran) return;
                let res = await axios.get(`/api/master/bangunan?saluran_id=${this.selectedSaluran}`);
                this.bangunans = res.data;
            },
            async fetchPetak() {
                this.selectedPetak = "";
                if (!this.selectedBangunan) return;
                let res = await axios.get(`/api/master/petak?bangunan_id=${this.selectedBangunan}`);
                this.petaks = res.data;
            },

            showKelolaSaluran() {
                $('#kelolaSaluranModal').modal('show');
            },
            showKelolaBangunan() {
                $('#kelolaBangunanModal').modal('show');
            },
            showKelolaPetak() {
                this.petakForm = {
                    id: null,
                    nama: "",
                    luas: "",
                    gambar_skema: "",
                    bangunan_id: this.selectedBangunan
                };
                this.petakFile = null;
                this.petakFormVisible = true;
                $('#kelolaPetakModal').modal('show');
            },
            showAddSaluranForm() {
                this.saluranForm = {
                    id: null,
                    nama: ""
                };
                this.saluranFormVisible = true;
            },
            showEditSaluranForm(s) {
                this.saluranForm = {
                    id: s.id,
                    nama: s.nama
                };
                this.saluranFormVisible = true;
            },
            async saveSaluran() {
                try {
                    if (this.saluranForm.id) {
                        await axios.put(`/api/master/saluran/${this.saluranForm.id}`, {
                            nama: this.saluranForm.nama,
                            daerah_irigasi_id: this.selectedDI
                        });
                    } else {
                        await axios.post(`/api/master/saluran`, {
                            nama: this.saluranForm.nama,
                            daerah_irigasi_id: this.selectedDI
                        });
                    }
                    this.saluranFormVisible = false;
                    this.fetchSaluran();
                } catch (err) {
                    console.error(err);
                    alert("Gagal menyimpan saluran");
                }
            },
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
            // === Bangunan ===
            showAddBangunanForm() {
                this.bangunanForm = {
                    id: null,
                    nama: "",
                    saluran_id: this.selectedSaluran
                };
                this.bangunanFormVisible = true;
            },
            showEditBangunanForm(b) {
                this.bangunanForm = {
                    id: b.id,
                    nama: b.nama,
                    saluran_id: this.selectedSaluran
                };
                this.bangunanFormVisible = true;
            },
            async saveBangunan() {
                console.log(this.bangunanForm);
                try {

                    if (this.bangunanForm.id) {
                        await axios.put(`/api/master/bangunan/${this.bangunanForm.id}`, this.bangunanForm);
                    } else {
                        await axios.post(`/api/master/bangunan`, this.bangunanForm);
                    }
                    this.bangunanFormVisible = false;
                    this.fetchBangunan(this.selectedSaluran);
                } catch (err) {
                    console.error(err);
                    alert("Gagal simpan bangunan");
                }
            },
            async deleteBangunan(id) {
                if (!confirm("Hapus bangunan ini?")) return;
                await axios.delete(`/api/master/bangunan/${id}`);
                this.fetchBangunan(this.selectedSaluran);
            },

            // === Petak ===
            showAddPetakForm() {
                this.petakForm = {
                    id: null,
                    nama: "",
                    luas: "",
                    gambar_skema: "",
                    bangunan_id: this.selectedBangunan
                };
                this.petakFile = null;
                this.petakFormVisible = true;
            },
            showEditPetakForm(p) {
                this.petakForm = {
                    ...p
                };
                this.petakFile = null;
                this.petakFormVisible = true;
                $('#kelolaPetakModal').modal('show');

            },
            handlePetakFile(e) {
                let file = e.target.files[0];
                if (file) {
                    this.petakForm.gambar_skema = file;
                    this.petakForm.gambar_preview = URL.createObjectURL(file);
                }
            },
            async savePetak() {
                try {
                    let formData = new FormData();
                    formData.append("nama", this.petakForm.nama);
                    formData.append("luas", this.petakForm.luas);
                    formData.append("bangunan_id", this.selectedBangunan); // pastikan ada relasi
                    if (this.petakForm.gambar_skema) {
                        formData.append("gambar_skema", this.petakForm.gambar_skema);
                    }

                    if (this.petakForm.id) {
                        formData.append("_method", "PUT");
                        await axios.post(`/api/master/petak/${this.petakForm.id}`, formData, {
                            headers: {
                                "Content-Type": "multipart/form-data"
                            }
                        });
                    } else {
                        await axios.post(`/api/master/petak`, formData, {
                            headers: {
                                "Content-Type": "multipart/form-data"
                            }
                        });
                    }

                    this.petakFormVisible = false;
                    this.fetchPetak();
                    $('#kelolaPetakModal').modal('hide');

                } catch (err) {
                    console.error(err);
                    alert("Gagal simpan petak");
                }
            },
            async deletePetak(id) {
                if (!confirm("Yakin hapus petak ini?")) return;
                try {
                    await axios.delete(`/api/master/petak/${id}`);
                    this.fetchPetak();
                } catch (err) {
                    console.error(err);
                    alert("Gagal hapus petak");
                }
            }



        }
    })
</script>
@endpush