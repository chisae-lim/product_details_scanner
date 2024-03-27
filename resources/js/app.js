import './bootstrap';

import 'admin-lte/plugins/bootstrap/js/bootstrap.bundle.min.js';
import 'admin-lte/dist/js/adminlte.min.js';

import { createApp } from 'vue';
import { createStore } from 'vuex';
import { createRouter, createWebHistory } from 'vue-router';
import routes from './routes.js';
import App from '../components/App.vue';
import { logUserOut, isAuthorized } from './modules/auth.js';

const store = createStore({
    state: {
        user: null,
        user_permissions: [],
        scales: [],
        units: [],
        brands: [],
        categories: [],
    },
    mutations: {
        refreshUser(state, user) {
            state.user = user;
        },
    },
    getters: {
        permittedUser: (state) => (id_perms, readOnly = true, abort = false) => {
            if (readOnly || state.user.act_status === 'ALLOWED') {
                const permissions = state.user.permissions;
                for (const permission of permissions) {
                    if (id_perms.includes(permission.id_permission) && permission.status === 'ENABLED') {
                        return permission.id_permission;
                    }
                }
            }
            return abort ? router.push({ name: 'notfound' }) : false;
        },
        forbadeUser(state) {
            return state.user.act_status === 'DENIED';
        }
    },
    actions: {
        async getUserPermissions(store) {
            LoadingModal();
            try {
                const res = await axios.get('/api/components/user/permissions');
                store.state.user_permissions = res.data;
                CloseModal();
            } catch (error) {
                ErrorModal(error);
            }
        },
        async getScales(store) {
            try {
                const res = await axios.get('/api/manage/scales');
                store.state.scales = res.data;
            } catch (error) {
                throw error;
            }
        },
        async getUnits(store) {
            try {
                const res = await axios.get('/api/manage/units');
                store.state.units = res.data;
            } catch (error) {
                throw error;
            }
        },
        async getBrands(store) {
            try {
                const res = await axios.get('/api/manage/brands');
                store.state.brands = res.data;
            } catch (error) {
                throw error;
            }
        },
        async getCategories(store) {
            try {
                const res = await axios.get('/api/manage/categories');
                store.state.categories = res.data;
            } catch (error) {
                throw error;
            }
        },
    }
});
const router = createRouter({
    routes: routes,
    history: createWebHistory(),
});

const app = createApp(App);

app.use(router);
app.use(store);
app.mount('#app');

router.beforeEach(async (to, from) => {
    LoadingModal();
    const loggedIn = await isAuthorized();
    if ((!loggedIn && !['index','product.details', 'login'].includes(to.name)) || to.name === 'logout') {
        if (await logUserOut()) return { name: 'index' };
    }

    if (loggedIn) {
        if (['login'].includes(to.name)) {
            return { name: 'dashboard' };
        }
        const user = loggedIn.data.user;
        store.commit('refreshUser', user);
        const guard = to.meta.guard;
        if (guard !== undefined) {
            return store.getters.permittedUser(guard.id_perms, guard.readOnly, true);
        }
    }
});

router.afterEach((to, from, failure) => {
    CloseModal();
    if (!failure) {
        document.title = 'PP DutyFreeStore';
    }
})