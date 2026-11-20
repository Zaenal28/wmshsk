import { defineStore } from 'pinia';
import api from '../services/api';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: JSON.parse(localStorage.getItem('user') || 'null'),
        token: localStorage.getItem('token') || null,
        loading: false,
        error: null,
    }),

    getters: {
        isAuthenticated: (state) => !!state.token,
        isSuperAdmin: (state) => state.user?.role === 'superadmin',
        isGudangPusat: (state) => state.user?.role === 'superadmin' || state.user?.role === 'gudang_pusat',
        isRuangDokter: (state) => state.user?.role === 'ruang_dokter',
        isGudangCabang: (state) => state.user?.role === 'gudang_cabang',
        isApotek: (state) => state.user?.role === 'apotek',
        userCabang: (state) => state.user?.cabang || 'Gudang Pusat',
    },

    actions: {
        async login(email, password) {
            this.loading = true;
            this.error = null;
            try {
                const response = await api.post('/login', { email, password });
                const { access_token, user } = response.data;

                this.token = access_token;
                this.user = user;

                localStorage.setItem('token', access_token);
                localStorage.setItem('user', JSON.stringify(user));

                return { success: true };
            } catch (err) {
                this.error = err.response?.data?.message || 'Gagal masuk. Periksa email & kata sandi Anda.';
                return { success: false, message: this.error };
            } finally {
                this.loading = false;
            }
        },

        async fetchMe() {
            if (!this.token) return;
            try {
                const response = await api.get('/me');
                this.user = response.data.user;
                localStorage.setItem('user', JSON.stringify(this.user));
            } catch (err) {
                this.logout();
            }
        },

        async logout() {
            try {
                if (this.token) {
                    await api.post('/logout');
                }
            } catch (e) {
                // Ignore error on logout
            } finally {
                this.token = null;
                this.user = null;
                localStorage.removeItem('token');
                localStorage.removeItem('user');
                window.location.href = '/login';
            }
        },
    },
});
