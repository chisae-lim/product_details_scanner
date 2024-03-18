
import Login from '../components/auth/Login.vue';
import Profile from '../components/profile/Profile.vue';
import Dashboard from '../components/dashboard/Dashboard.vue';
import Users from '../components/manage/users/Users.vue';

import NotFound from '../components/NotFound.vue';


import Navbar from '../components/includes/Navbar.vue';
import MainSidebar from '../components/includes/MainSidebar.vue';
import ControlSidebar from '../components/includes/ControlSidebar.vue';
import Footer from '../components/includes/Footer.vue';

const operationComponents = {
    navbar: Navbar,
    main_sidebar: MainSidebar,
    control_sidebar: ControlSidebar,
    footer: Footer,
}
export default [
    {
        path: '/logout',
        name: 'logout',
    },
    {
        path: '/',
        name: 'login',
        component: Login,
    },
    {
        path: '/profile',
        name: 'profile',
        components: {
            default: Profile,
            ...operationComponents
        },
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        components: {
            default: Dashboard,
            ...operationComponents
        },
    },
    {
        path: '/manage/users',
        name: 'manage.users',
        components: {
            default: Users,
            ...operationComponents
        },
        meta: {
            guard: {
                id_perms: [1, 2],
                readOnly: true,
            }
        }
    },
    {
        path: '/404',
        name: 'notfound',
        component: NotFound,
    },
    {
        path: '/:path(.*)*',
        redirect: { name: 'notfound' }
    }
]