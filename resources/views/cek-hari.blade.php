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
                <th>Nama DI</th>
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
                <td>@{{ row.nama_di }}</td>
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
                filterDi: ''
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
            }
        }
    }).mount('#app');
</script>
@endpush