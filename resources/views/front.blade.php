<!DOCTYPE html>

<!-- =========================================================
* Sneat - Bootstrap 5 HTML Admin Template - Pro | v1.0.0
==============================================================

* Product Page: https://themeselection.com/products/sneat-bootstrap-html-admin-template/
* Created by: ThemeSelection
* License: You must have a valid license purchased in order to legally use the theme for your project.
* Copyright ThemeSelection (https://themeselection.com)

=========================================================
 -->
<!-- beautify ignore:start -->
<html
    lang="en"
    class="light-style customizer-hide"
    dir="ltr"
    data-theme="theme-default"
    data-assets-path="{{asset('/')}}assets/"
    data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Halaman Utama</title>

    <meta name="description" content="" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="https://sda.pu.go.id/web/images/favicon.png" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <!-- Icons. Uncomment required icon fonts -->
    <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>
    <!-- Core CSS -->
    <link rel="stylesheet" href="{{asset('/')}}assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{asset('/')}}assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{asset('/')}}assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{asset('/')}}assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="{{asset('/')}}assets/vendor/css/pages/page-auth.css" />
    <!-- Helpers -->
    <script src="{{asset('/')}}assets/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{asset('/')}}assets/js/config.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.prod.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        /* Overlay (background hitam transparan) */
        #popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.75);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            visibility: hidden;
            opacity: 0;
            transition: opacity 0.3s ease, visibility 0.3s;
        }

        /* Tampilkan popup */
        #popup-overlay.show {
            visibility: visible;
            opacity: 1;
        }

        /* Kotak popup ukuran besar (modal XXL) */
        #popup-box {
            background-color: #fff;
            border-radius: 20px;
            padding: 30px;
            width: 90%;
            max-width: 900px;
            text-align: center;
            position: relative;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
            animation: fadeInUp 0.4s ease;
        }

        #popup-box img {
            width: 100%;
            /* max-height: 400px; */
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        #popup-box h2 {
            margin-bottom: 10px;
            font-size: 28px;
            color: #333;
            font-family: Arial, sans-serif;
        }

        #popup-box p {
            font-size: 16px;
            color: #555;
            line-height: 1.6;
        }

        #popup-box button {
            margin-top: 25px;
            padding: 10px 24px;
            background-color: #007bff;
            border: none;
            color: #fff;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
        }

        #popup-box button:hover {
            background-color: #0056b3;
        }

        /* Tombol close (X) */
        #popup-close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 26px;
            color: #999;
            cursor: pointer;
            font-weight: bold;
        }

        #popup-close:hover {
            color: #333;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        btn-outline-dark {
            background-color: white !important;
            color: #000 !important;
            border-color: #000 !important;
        }

        .btn-outline-dark:hover {
            background-color: #f8f9fa !important;
            /* putih keabu */
            color: #000 !important;
            /* tetap hitam */
            border-color: #000 !important;
        }
    </style>
</head>

<!-- <body style="
  background: url('background.jpeg') no-repeat center center;
  background-size: cover;
"> -->

<body>
    <!-- Content -->

    <div class="container-xxl" id="app">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <!-- Register -->
                <div class="card"
                    style="
        background: url('pu-batik.jpeg') no-repeat center center;
        background-size: 100% 100%;
     ">
                    <div class="card-body text-center">
                        <!-- Logo -->
                        <div class="mb-3">
                            <img src="{{asset('/logo-pu.jpg')}}" alt="" width="80" class="mb-2">
                            <h2 class="mt-3">SIPEMALUTTAJIR</h2>
                        </div>
                        <!-- /Logo -->
                        <!-- <h5 class="mb-3">SILAHKAN PILIH SEBAGAI : </h5> -->
                        <div class="col-12">
                            <a href="{{route('form')}}" class="btn btn-lg btn-outline-dark w-100" style="background-color: white;">
                                <span class="tf-icons bx bx-form"></span>&nbsp; Juru Irigasi
                            </a>
                        </div>
                        <div class="col-12 mt-2">
                            <a href="{{route('pengamat')}}" class="btn btn-lg btn-outline-dark w-100" style="background-color: white;">
                                <span class="tf-icons bx bx-layers-alt"></span>&nbsp; Pengamat Irigasi
                            </a>
                        </div>
                        <div class="col-12 mt-2">
                            <a href="{{route('upi')}}" class="btn btn-lg btn-outline-dark w-100" style="background-color: white;">
                                <span class="tf-icons bx bx-pie-chart-alt"></span>&nbsp; Unit Pengelola Irigasi (UPI)
                            </a>
                        </div>

                        <div class="col-12 mt-2">
                            <a href="{{route('login-form')}}" class="btn btn-lg btn-outline-dark w-100" style="background-color: white;">
                                <span class="tf-icons bx bx-file-report"></span>&nbsp; Sekretariat BBWS/BWS
                            </a>
                        </div>
                        <div class="col-12 mt-2">
                            <a href="{{route('komisi-irigasi')}}" class="btn btn-lg btn-outline-dark w-100" style="background-color: white;">
                                <span class="tf-icons bx bx-user"></span>&nbsp; Komisi Irigasi
                            </a>
                        </div>
                        <div class="col-12 mt-2">
                            <a href="{{route('basis-data')}}" class="btn btn-lg btn-outline-dark w-100" style="background-color: white;">
                                <span class="tf-icons bx bx-globe"></span>&nbsp; Basisdata Hasil Pemantauan
                            </a>
                        </div>
                        <div class="text-center mt-3">
                            <a href="#" id="btnTentang">
                                <span class="tf-icons bx bx-info"></span>&nbsp; Apa sih sipemaluttajir?
                            </a>
                        </div>
                    </div>
                </div>
                <!-- /Register -->
            </div>
        </div>
    </div>

    <!-- / Content -->


    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="{{asset('/')}}assets/vendor/libs/jquery/jquery.js"></script>
    <script src="{{asset('/')}}assets/vendor/libs/popper/popper.js"></script>
    <script src="{{asset('/')}}assets/vendor/js/bootstrap.js"></script>
    <script src="{{asset('/')}}assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="{{asset('/')}}assets/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->
    <script src="{{asset('/')}}assets/js/main.js"></script>

    <!-- Page JS -->

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Pop-up -->
    <!-- Pop-up XXL -->
    <div id="popup-overlay">
        <div id="popup-box">
            <span id="popup-close">&times;</span>
            <img src="info.jpeg" alt="Gambar Popup">
            <button id="closePopup">Tutup</button>
            <label style="display:block; margin-top:10px;">
                <input type="checkbox" id="dontShowAgain"> Jangan tampilkan lagi
            </label>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const popup = document.getElementById("popup-overlay");
            const closeBtn = document.getElementById("closePopup");
            const closeX = document.getElementById("popup-close");
            const dontShowAgain = document.getElementById("dontShowAgain");
            const btnTentang = document.getElementById("btnTentang");

            // Cek apakah user sudah memilih "jangan tampilkan lagi"
            let skipPopup = localStorage.getItem("skipPopup");

            // Jika belum pernah memilih, tampilkan popup otomatis
            if (!skipPopup) {
                popup.classList.add("show");
            }

            // Fungsi menutup popup
            const closePopup = () => {
                popup.classList.remove("show");
                if (dontShowAgain.checked) {
                    localStorage.setItem("skipPopup", "true");
                }
            };

            closeBtn.addEventListener("click", closePopup);
            closeX.addEventListener("click", closePopup);

            // Tombol Tentang — selalu tampilkan popup
            btnTentang.addEventListener("click", () => {
                popup.classList.add("show");
                // Reset agar popup muncul otomatis lagi saat halaman dibuka berikutnya
                localStorage.removeItem("skipPopup");
            });
        });
    </script>


</body>

</html>