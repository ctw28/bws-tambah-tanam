@extends('admin.template')

@section('content')
<div id="app" v-cloak class="container mt-4">
    <div class="card h-100">
        <div class="card-header">
            <h5 class="mb-0">Daftar Permasalahan</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Tanggal Pantau</th>
                        <th>Nama Petugas</th>
                        <th>Permasalahan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in items" :key="item.id">
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
                    </tr>
                </tbody>
            </table>
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
            }
        }
    }).mount('#app');
</script>
@endpush