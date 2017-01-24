import Vue from 'vue'
import Hanoi from './Hanoi.vue';

import {store} from './store';

new Vue({
    store,
    components: {
        Hanoi
    }
}).$mount('#app')