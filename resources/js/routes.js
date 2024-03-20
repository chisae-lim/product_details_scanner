
import Login from '../components/auth/Login.vue';
import Profile from '../components/profile/Profile.vue';
import Dashboard from '../components/dashboard/Dashboard.vue';
import Index from '../components/index/Index.vue';
import Users from '../components/manage/users/Users.vue';
import Brand from '../components/manage/brand/Brand.vue';
import Category from '../components/manage/category/Category.vue';
import Scale from '../components/manage/scale/Scale.vue';
import Unit from '../components/manage/unit/Unit.vue';
import Color from '../components/manage/color/Color.vue';
import Product from '../components/manage/product/Product.vue';

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
        name: 'index',
        component: Index,
    },
    {
        path: '/login',
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
        path: '/manage/brands',
        name: 'manage.brands',
        components: {
            default: Brand,
            ...operationComponents
        },
        meta: {
            guard: {
                id_perms: [1],
                readOnly: true,
            }
        }
    },
    {
        path: '/manage/categories',
        name: 'manage.categories',
        components: {
            default: Category,
            ...operationComponents
        },
        meta: {
            guard: {
                id_perms: [1],
                readOnly: true,
            }
        }
    },
    {
        path: '/manage/scales',
        name: 'manage.scales',
        components: {
            default: Scale,
            ...operationComponents
        },
        meta: {
            guard: {
                id_perms: [1],
                readOnly: true,
            }
        }
    },
    {
        path: '/manage/colors',
        name: 'manage.colors',
        components: {
            default: Color,
            ...operationComponents
        },
        meta: {
            guard: {
                id_perms: [1],
                readOnly: true,
            }
        }
    },
    {
        path: '/manage/units',
        name: 'manage.units',
        components: {
            default: Unit,
            ...operationComponents
        },
        meta: {
            guard: {
                id_perms: [1],
                readOnly: true,
            }
        }
    },
    {
        path: '/manage/products',
        name: 'manage.products',
        components: {
            default: Product,
            ...operationComponents
        },
        meta: {
            guard: {
                id_perms: [1, 3],
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
        redirect: { name: 'dashboard' }
    }
]