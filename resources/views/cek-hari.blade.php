@extends('admin.template')

@section('content')
<div id="app" class="container mt-4" v-cloak>

    <h4>Cek Form Pengisian</h4>

    <!-- Filter DI -->
    <div class="row mb-3">
        <div class="col-md-4">
            <select v-model="filterDi" @change="loadData" class="form-select">
                <option value="">-- Semua DI --</option>
                <option v-for="di in daftarDI" :value="di.id">@{{ di.nama }}</option>
            </select>
        </div>
    </div>

    <table class="table table-bordered table-sm">
        <thead class="table-light">
            <tr>
                <th>No</th>
                <th>detail</th>
                <!-- <th>Nama DI</th> -->
                <th>Saluran/bangunan/petak</th>
                <th>Tanggal</th>
                <th>Hari</th>
                <th>Padi</th>
                <th>Palawija</th>
                <th>Lainnya</th>
                <th>Update ke Minggu</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(row, i) in items" :key="row.id" :class="row.is_minggu ? 'table-success' : ''">
                <td>@{{ i+1 }}</td>
                <td>
                    <button @click="showForm(row.data)" class="btn btn-sm btn-info">Detail</button>
                </td>
                <td>@{{ row.saluran }} / @{{ row.bangunan }} / @{{ row.petak }}</td>
                <td>@{{ row.tanggal_pantau }}</td>
                <td>
                    <span :class="row.is_minggu ? 'badge bg-success' : 'badge bg-danger'">
                        @{{ row.hari }}
                    </span>
                </td>
                <td>@{{ row.luas_padi }}</td>
                <td>@{{ row.luas_palawija }}</td>
                <td>@{{ row.luas_lainnya }}</td>
                <td v-if="!row.is_minggu">
                    <input type="date" v-model="row.tanggal_baru" class="form-control form-control-sm mb-1">
                    <button class="btn btn-sm btn-primary w-100" @click="updateTanggal(row)">Update</button>
                </td>

                <td v-else class="text-center text-muted">✅</td>
            </tr>
        </tbody>
    </table>
    <div class="modal fade" id="formLTTModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Form Pemantauan LTT</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <!-- Header -->
                    <h4 class="text-center mb-3">FORM PEMANTAUAN LUAS TAMBAH TANAM (LTT)</h4>

                    <!-- Identitas -->
                    <div class="mb-3">
                        <div class="form-line"><span>Nama Petugas OP</span>:
                            @{{ item.petugas ? item.petugas.nama : '-' }}</div>
                        <div class="form-line"><span>Tanggal Pemantauan</span>:
                            @{{ formatTanggal(item.tanggal_pantau) }}</div>
                        <div class="form-line"><span>Daerah Irigasi</span>: DI
                            @{{ item.daerah_irigasi ? item.daerah_irigasi.nama : '-' }}</div>
                        <div class="form-line"><span>Desa/Kelurahan</span>: @{{item.desa}}</div>
                        <div class="form-line"><span>Kecamatan</span>: @{{item.kecamatan}}</div>
                        <div class="form-line"><span>Kabupaten/Kota</span>:
                            @{{item.kabupaten ? item.kabupaten.nama : '-'}}
                        </div>
                        <div class="form-line"><span>Nama Saluran (Sekunder/Primer)</span>:
                            @{{item.saluran ? item.saluran.nama : '-'}}</div>
                        <div class="form-line"><span>Nama Bangunan Bagi/Sadap</span>:
                            @{{item.bangunan ? item.bangunan.nama : '-'}}</div>
                        <div class="form-line"><span>Kode/Nama Petak Layanan</span>:
                            @{{item.petak ? item.petak.nama : '-'}}
                        </div>
                        <div class="form-line"><span>Koordinat Bangunan Bagi/Sadap</span>: @{{item.koordinat}}
                        </div>
                    </div>

                    <!-- Tabel -->
                    <div class="table-responsive">

                        <table class="table table-bordered text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th rowspan="2">Debit Air (lt/detik)</th>
                                    <th rowspan="2">Luas Petak Skema (Ha)</th>
                                    <th colspan="3">Pemantauan Luas Tambah Tanam (LTT)</th>
                                </tr>
                                <tr>
                                    <th>Padi (Ha)</th>
                                    <th>Palawija (Ha)</th>
                                    <th>Lainnya (Ha)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td height="50px">@{{item.debit_air}}</td>
                                    <td>@{{item.petak ? item.petak.luas : '-'}}</td>
                                    <td>@{{item.luas_padi}}</td>
                                    <td>@{{item.luas_palawija}}</td>
                                    <td>@{{item.luas_lainnya}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="table-responsive">

                        <!-- Tabel -->
                        <table class="table table-bordered text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Pemantauan Permasalahan</th>
                                    <th>Ada/Tidak</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody v-for="(p,index) in item.permasalahan" :key="p.id">
                                <tr>
                                    <td>@{{ index+1}}</td>
                                    <td>@{{ p.master_permasalahan.nama }}
                                    </td>
                                    <td class="text-center">
                                        <span v-if="p.status==1">Ada</span>
                                        <span v-else="p.status==0">Tidak</span>
                                    </td>
                                    <td>@{{ p.keterangan}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <h4>Foto Pemantauan</h4>
                    <img v-if="modalInstance" :src="`/storage/${item.foto_pemantauan}`" width="100%">

                </div>

                <div class="modal-footer">
                    <button class="btn btn-danger me-auto" @click="hapus(item.id)"><i class="menu-icon tf-icons bx bx-trash"></i> Hapus</button>
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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
                daftarDI: [],
                filterDi: '',
                item: ''
            }
        },
        mounted() {
            this.loadDI();
            // this.loadData();
        },
        methods: {
            async loadDI() {
                let url = `/api/master/daerah-irigasi?page=all`;

                let res = await axios.get(url);
                this.daftarDI = res.data.data;
                console.log(res.data.data);

            },
            async loadData() {
                try {
                    let url = `/api/cekhari`;
                    if (this.filterDi) {
                        url += `?di_id=${this.filterDi}`;
                    }

                    const res = await axios.get(url);

                    // jika pakai pagination Laravel
                    this.items = res.data.data ?? res.data;

                } catch (e) {
                    console.error(e);
                }
            },

            async updateTanggal(row) {
                if (!row.tanggal_baru) {
                    alert('Pilih tanggal baru dulu');
                    return;
                }

                if (!confirm('Yakin mau update tanggal?')) return;

                try {
                    await axios.put(`/api/form-pengisian/update-tanggal/${row.id}`, {
                        tanggal_pantau: row.tanggal_baru
                    });

                    alert('Berhasil diupdate');
                    this.loadData();

                } catch (e) {
                    console.error(e);
                    alert('Gagal update tanggal');
                }
            },
            showForm(form) {
                this.item = form;
                console.log(this.item);

                const modalEl = document.getElementById('formLTTModal');
                this.modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
                this.modalInstance.show();
            },
            async hapus(id) {
                if (!confirm("Yakin ingin menghapus data ini?")) {
                    return;
                }

                try {
                    let res = await axios.delete(`/api/form-pengisian/${id}`);
                    console.log(res);

                    // Tutup modal setelah berhasil hapus
                    let modal = bootstrap.Modal.getInstance(document.getElementById('formLTTModal'));
                    modal.hide();

                    // Refresh data tabel
                    this.loadData();

                } catch (e) {
                    console.error(e);
                    alert("Gagal menghapus data!");
                }
            },
            formatTanggal(tgl) {
                if (!tgl) return '-';

                // Format ke 17 September 2025
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