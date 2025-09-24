<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Form Pemantauan LTT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        font-family: "Times New Roman", serif;
        background: #fff;
        color: #000;
        font-size: 14pt;
    }

    .a4 {
        width: 210mm;
        min-height: 297mm;
        padding: 20mm;
        margin: auto;
        background: white;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    }

    @media print {
        .a4 {
            box-shadow: none;
            margin: 0;
            width: auto;
            min-height: auto;
        }

        .no-print {
            display: none;
        }
    }

    .logo {
        width: 100%;
        max-height: 120px;
        object-fit: contain;
        margin-bottom: 10px;
    }

    h4 {
        text-align: center;
        text-transform: uppercase;
        font-weight: bold;
        margin: 10px 0 20px;
    }

    .form-line {
        margin-bottom: 8px;
    }

    .form-line span {
        display: inline-block;
        min-width: 250px;
    }
    </style>
</head>

<body>
    <div id="app">
        <div class="a4">
            <!-- Logo -->
            <div class="text-center">
                <img src="logo.jpg" alt="Logo" class="logo">
            </div>

            <!-- Judul -->
            <h4>FORM PEMANTAUAN LUAS TAMBAH TANAM (LTT)</h4>

            <!-- Identitas -->
            <div class="mb-3">
                <div class="form-line"><span>Nama Petugas OP</span>: @{{ item.petugas ? item.petugas.nama : '-' }}</div>
                <div class="form-line"><span>Tanggal Pemantauan</span>: @{{ formatTanggal(item.tanggal_pantau) }}</div>
                <div class="form-line"><span>Daerah Irigasi</span>: DI
                    @{{ item.daerah_irigasi ? item.daerah_irigasi.nama : '-' }}</div>
                <div class="form-line"><span>Desa/Kelurahan</span>: @{{item.desa}}</div>
                <div class="form-line"><span>Kecamatan</span>: @{{item.kecamatan}}</div>
                <div class="form-line"><span>Kabupaten/Kota</span>: @{{item.kabupaten ? item.kabupaten.nama : '-'}}
                </div>
                <div class="form-line"><span>Nama Saluran (Sekunder/Primer)</span>:
                    @{{item.saluran ? item.saluran.nama : '-'}}</div>
                <div class="form-line"><span>Nama Bangunan Bagi/Sadap</span>:
                    @{{item.bangunan ? item.bangunan.nama : '-'}}</div>
                <div class="form-line"><span>Kode/Nama Petak Layanan</span>: @{{item.petak ? item.petak.nama : '-'}}
                </div>
                <div class="form-line"><span>Koordinat Bangunan Bagi/Sadap</span>: @{{item.koordinat}}</div>
            </div>

            <!-- Tabel -->
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

            <!-- Tombol Cetak -->
            <div class="text-center mt-4 no-print">
                <button class="btn btn-primary" onclick="window.print()">Cetak</button>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
    const {
        createApp
    } = Vue;

    createApp({
        data() {
            return {
                item: {}, // data asli
            }
        },
        mounted() {
            this.loadData();
        },
        methods: {
            async loadData() {
                try {
                    let res = await axios.get('/api/form-pengisian?id=2');
                    this.item = res.data[0];
                    console.log(this.item);

                } catch (e) {
                    console.error(e);
                }
            },

            formatTanggal(tgl) {
                if (!tgl) return '-';

                // Format ke 17 September 2025
                return new Date(tgl).toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric'
                });
            },

        }
    }).mount('#app');
    </script>
</body>

</html>