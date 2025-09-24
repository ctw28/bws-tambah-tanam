<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login JWT</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.prod.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>

<body class="bg-gray-100">
    <div id="app" class="max-w-md mx-auto mt-20 p-6 bg-white shadow rounded">
        <h2 class="text-2xl font-bold mb-4">Login</h2>

        <div v-if="error" class="bg-red-100 text-red-700 p-2 mb-4 rounded">
            @{{ error }}
        </div>

        <form @submit.prevent="login">
            <div class="mb-3">
                <label>Email</label>
                <input type="email" v-model="form.email" class="border rounded w-full p-2">
            </div>

            <div class="mb-3">
                <label>Password</label>
                <input type="password" v-model="form.password" class="border rounded w-full p-2">
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                Login
            </button>
        </form>

        <!-- <div v-if="user" class="mt-6 p-4 bg-green-100 rounded">
            <h3 class="font-bold">User Info</h3>
            <pre>@{{ user }}</pre>
            <button @click="logout" class="bg-red-500 text-white px-3 py-1 rounded mt-2">Logout</button>
        </div> -->
    </div>

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