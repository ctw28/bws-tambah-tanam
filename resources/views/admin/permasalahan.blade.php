@extends('admin.template')

@section('content')
<div id="app" v-cloak class="container mt-4">
    <div class="card h-100">
        <div class="card-header">
            <h5 class="mb-0">Daftar Permasalahan</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal Pantau</th>
                            <th>Nama Petugas</th>
                            <th>Permasalahan</th>
                            <th>Daerah Irigasi</th>
                            <th>Lihat Form</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item,index) in items" :key="item.id">
                            <td>@{{index+1}}</td>
                            <td>@{{ item.tanggal_pantau }}</td>
                            <td>@{{ item.petugas?.nama ?? '-' }}</td>
                            <td>@{{ item.daerah_irigasi?.nama ?? '-' }}
                                / @{{ item.bangunan?.nama ?? '-' }}
                                / @{{ item.petak?.nama ?? '-' }}
                            </td>
                            <td>
                                <ul class="mb-0">
                                    <li v-for="p in item.permasalahan" :key="p.id">
                                        @{{ p.master_permasalahan?.nama }} : @{{ p.keterangan }}
                                    </li>
                                </ul>
                            </td>
                            <td>
                                <button @click="showForm(item)" class="btn btn-primary btn-sm"><i
                                        class="menu-icon tf-icons bx bx-eye me-0"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="formLTTModal" tabindex="-1">
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
                    <!-- <button class="btn btn-danger me-auto" @click="hapus(item.id)"><i class="menu-icon tf-icons bx bx-trash"></i> Hapus</button> -->
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
            item: {},
            modalInstance: null
        };
    },
    async mounted() {
        await this.loadData();
    },
    methods: {
        async loadData() {
            let token = localStorage.getItem("token");

            let dis = await axios.get('/api/user-dis');
            console.log(dis.data);

            let items = [];
            let seen = new Set();

            for (let di of dis.data) {
                let url = di.has_upi ?
                    `/api/form-pengisian?di_id=${di.id}&pengamat_valid=1&upi_valid=1&has_permasalahan=1` :
                    `/api/form-pengisian?di_id=${di.id}&pengamat_valid=1&has_permasalahan=1`;
                console.log(url);

                let res = await axios.get(url);

                for (let d of res.data) {
                    if (!seen.has(d.id)) {
                        seen.add(d.id);
                        items.push(d);
                    }
                }
            }

            console.log(items);

            this.items = items;
            this.filteredItems = items;
        },
        showForm(form) {
            this.item = form;
            console.log(this.item);

            const modalEl = document.getElementById('formLTTModal');
            this.modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
            this.modalInstance.show();
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