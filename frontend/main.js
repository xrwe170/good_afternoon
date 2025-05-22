import Vue from 'vue';
import App from './App';

Vue.config.productionTip = false;

App.mpType = 'app';

// 此处为演示Vue.prototype使用，非uView的功能部分
Vue.prototype.vuePrototype = '枣红';

// 引入全局uView
import uView from 'uview-ui';
Vue.use(uView);

// 此处为演示vuex使用，非uView的功能部分
import store from '@/store';


// 引入uView对小程序分享的mixin封装
let mpShare = require('uview-ui/libs/mixin/mpShare.js');
Vue.mixin(mpShare);

import i18n from '@/common/locales/config.js'
// 由于微信小程序的运行机制问题，需声明如下一行，H5和APP非必填
Vue.prototype._i18n = i18n;

const app = new Vue({
	i18n,
	store,
	...App
});

// http拦截器，将此部分放在new Vue()和app.$mount()之间，才能App.vue中正常使用
import httpInterceptor from '@/common/http.interceptor.js';
Vue.use(httpInterceptor, app);

// http接口API抽离，免于写url或者一些固定的参数
import httpApi from '@/common/http.api.js';
Vue.use(httpApi, app);

//常用js工具
import utils from '@/common/utils'
Vue.prototype.$utils = utils


import * as filters from '@/common/filters.js'
Object.keys(filters).forEach(key=>{
    Vue.filter(key,filters[key])//插入过滤器名和对应方法
})


Vue.prototype.$kefu = 'https://t.me/youguopay'

//绿涨红跌
Vue.prototype.$upColor = '#28ba98'
Vue.prototype.$downColor = '#F04A5A'
Vue.prototype.$black = '#4d4d4d'
Vue.prototype.$baseColor = '#333333'
app.$mount();
