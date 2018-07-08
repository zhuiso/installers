import Vue from 'vue';
import VueValidate from 'vee-validate';

import './assets/style.less';
import App from './App.vue';
import zs from './zs';
import router from './router';

Vue.use(VueValidate);

Vue.config.productionTip = false;
Vue.http = zs.http;
Object.defineProperty(Vue.prototype, 'http', {
    get() {
        return zs.http;
    },
});

zs.instance = new Vue({
    el: '#app',
    router,
    template: '<App/>',
    components: { App },
});
