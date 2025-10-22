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

    <title>Login</title>

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

</head>

<body>
    <!-- Content -->

    <div class="container-xxl" id="app">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <!-- Register -->
                <div class="card">
                    <div class="card-body ">
                        <!-- Logo -->
                        <div class="text-center">
                            <img src="{{asset('/logo-pu.jpg')}}" alt="" width="80" class="mb-2">
                            <h2>SIPEMALUTAJIR</h2>
                        </div>
                        <!-- /Logo -->
                        <p class="mb-4 text-center">Login untuk masuk</p>

                        <div v-if="error" class="bg-red-100 text-red-700 p-2 mb-4 rounded">
                            @{{ error }}
                        </div>
                        <form @submit.prevent="login">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email or Username</label>
                                <input v-model="form.email"
                                    type="text"
                                    class="form-control"
                                    id="email"
                                    name="email-username"
                                    placeholder="Enter your email or username"
                                    autofocus />
                            </div>
                            <div class="mb-3 form-password-toggle">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="password">Password</label>

                                </div>
                                <div class="input-group input-group-merge">
                                    <input v-model="form.password"
                                        type="password"
                                        id="password"
                                        class="form-control"
                                        name="password"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        aria-describedby="password" />
                                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <button class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
                            </div>
                        </form>
                        <div class="col-12 text-center">
                            <a href="/" class="">
                                <span class="tf-icons bx bx-arrow-left"></span>&nbsp; Kembali ke halaman depan
                            </a>
                        </div>
                    </div>
                </div>
                <!-- /Register -->
            </div>
        </div>
    </div>

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
    <script>
        const {
            createApp
        } = Vue;

        createApp({
            data() {
                return {
                    form: {
                        email: '',
                        password: ''
                    },
                    token: null,
                    user: null,
                    error: null
                }
            },
            methods: {
                async login() {
                    try {
                        let res = await axios.post('/api/login', {
                            email: this.form.email,
                            password: this.form.password
                        });

                        // simpan token di localStorage
                        localStorage.setItem('token', res.data.access_token);

                        // redirect ke dashboard
                        window.location.href = "/admin/dashboard";
                    } catch (e) {
                        alert("Login gagal");
                    }
                },
                async logout() {
                    try {
                        await axios.post('/api/logout');
                        this.token = null;
                        this.user = null;
                        delete axios.defaults.headers.common['Authorization'];
                    } catch (e) {
                        this.error = 'Logout gagal';
                    }
                }
            }
        }).mount('#app');
    </script>
</body>

</html>