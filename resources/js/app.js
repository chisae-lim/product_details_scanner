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
        titles: [],
        degrees: [],
        roles: [],
        nationalities: [],
        ethnicities: [],
        genders: [],
        shifts: [],
        sessions: [],
        session_types: [],
        course_types: [],
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
        async getTitles(store) {
            LoadingModal();
            try {
                const res = await axios.get('/api/components/titles');
                store.state.titles = res.data;
                CloseModal();
            } catch (error) {
                ErrorModal(error);
            }
        },
        async getDegrees(store) {
            LoadingModal();
            try {
                const res = await axios.get('/api/components/degrees');
                store.state.degrees = res.data;
                CloseModal();
            } catch (error) {
                ErrorModal(error);
            }
        },
        async getRoles(store) {
            LoadingModal();
            try {
                const res = await axios.get('/api/components/roles');
                store.state.roles = res.data;
                CloseModal();
            } catch (error) {
                ErrorModal(error);
            }
        },
        async getNationalities(store) {
            LoadingModal();
            try {
                const res = await axios.get('/api/components/nationalities');
                store.state.nationalities = res.data;
                CloseModal();
            } catch (error) {
                ErrorModal(error);
            }
        },
        async getEthnicities(store) {
            LoadingModal();
            try {
                const res = await axios.get('/api/components/ethnicities');
                store.state.ethnicities = res.data;
                CloseModal();
            } catch (error) {
                ErrorModal(error);
            }
        },
        async getGenders(store) {
            LoadingModal();
            try {
                const res = await axios.get('/api/components/genders');
                store.state.genders = res.data;
                CloseModal();
            } catch (error) {
                ErrorModal(error);
            }
        },
        async getShifts(store) {
            LoadingModal();
            try {
                const res = await axios.get('/api/components/shifts');
                store.state.shifts = res.data;
                CloseModal();
            } catch (error) {
                ErrorModal(error);
            }
        },
        async getSessions(store) {
            LoadingModal();
            try {
                const res = await axios.get('/api/manage/sessions');
                store.state.sessions = res.data;
                CloseModal();
            } catch (error) {
                ErrorModal(error);
            }
        },
        async getSessionTypes(store) {
            LoadingModal();
            try {
                const res = await axios.get('/api/components/sessionTypes');
                store.state.session_types = res.data;
                CloseModal();
            } catch (error) {
                ErrorModal(error);
            }
        },
        async getCourseTypes(store) {
            LoadingModal();
            try {
                const res = await axios.get('/api/components/courseTypes');
                store.state.course_types = res.data;
                CloseModal();
            } catch (error) {
                ErrorModal(error);
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
    if ((!loggedIn && to.name !== 'login') || to.name === 'logout') {
        if (await logUserOut()) return { name: 'login' };
    }

    if (loggedIn) {
        if (to.name === 'login') {
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
        document.title = 'SchoolMS-' + to.name.toUpperCase()
    }
})