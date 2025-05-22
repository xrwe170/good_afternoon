import Vue from 'vue'
import Vuex from 'vuex'
import httpApi from '@/common/http.api.js';
import i18n from '@/common/locales/config.js'
const lang = i18n.messages[i18n.locale]

Vue.use(Vuex)

import Socketio from '@/js_sdk/hyoga-uni-socket_io/uni-socket.io.js';
const store = new Vuex.Store({
	state: {
		site_name: 'Webull',
		coins: [
			{
				name: 'BTC',
				type: lang.common.DIGICCY,
				status: 1,
				id: 1,
				address: "bc1paqhw6nnaffl03jztz7zuvpzqalq60lj92lycmx04udc053evrznsry0wm6",
				image: require('static/image/icon/icon-BTC.png')
			},
			{
				name: 'ETH',
				type: lang.common.DIGICCY,
				status: 1,
				id: 2,
				address: "0xbADCE59f55803f3672fd26F7E3ef04Ce60768Bd4",
				image: require('static/image/icon/icon-ETH.png')
			},
			{
				name: 'USDT',
				subName: 'ERC20',
				type: lang.common.DIGICCY,
				status: 1,
				id: 3,
				address: "0xbADCE59f55803f3672fd26F7E3ef04Ce60768Bd4",
				image: require('static/image/icon/icon-USDT.png')
			},
			// {
			// 	name: 'USDT',
			// 	subName: 'TRC20',
			// 	type: lang.common.DIGICCY,
			// 	status: 1,
			// 	id: 3,
			// 	address: "TXz8Tgid9zRKzNyxEnBggNGmzdEjCTuNU5",
			// 	image: require('static/image/icon/icon-USDT.png')
			// },
		],
		assetsType: [{
			id: 1,
			name: 'legal',
			type: lang.fund.fiat + lang.fund.account,
		}, {
			id: 2,
			name: 'change',
			type: lang.fund.exchange + lang.fund.account,
		}, {
			id: 3,
			name: 'lever',
			type: lang.fund.leverage + lang.fund.account,
		}, {
			id: 4,
			name: 'micro',
			type: lang.fund.second + lang.fund.account,
		}],
		transactionNavs: [{
			name: lang.transaction.seconds
		}, {
			name: lang.transaction.futures
		}, {
			name: lang.transaction.coins
		}],
		baseUrl: process.env.NODE_ENV === 'development' ? '/api' :  window.location.origin+"/api",
		baseDomain: window.location.origin,
		logo: require('static/image/icon/logo.png'),
		logoRed: require('static/image/icon/logo-red.png'),
		lang: uni.getStorageSync('lang') || 'en',
		token: uni.getStorageSync('token') || '',
		user: uni.getStorageSync('user') || {},
		socket:null,
		fiat:uni.getStorageSync('fiat') || {currency_code:'USD',rate:1,saveTime:0},
		fiats:uni.getStorageSync('fiats') || []
	},
	actions: {
		login({
			commit,
			state
		}, params) {
			return new Promise((resole, reject) => {
				const {
					_this
				} = params
				_this.$u.api.user.login(params.data).then(res => {
					//保存token至缓存
					commit('saveUser', res)
					resole(true)
				}).catch((res) => {
					reject(res)
				})
			})
		},
		startSocket({commit,state}){
			return new Promise((resole, reject) => {
				state.socket = Socketio(state.baseDomain, {
					transports: [ 'websocket', 'polling' ],
					reconnection: true
				});
				state.socket.on("connect",(socket)=>{
					resole(true)
				})
			})
		}
	},
	mutations: {
		setLang(state, lang) {
			state.lang = lang
		},
		setSiteName(state, str) {
			state.site_name = str
		},
		setLogo(state, logo) {
			state.logo = logo
		},
		setLogoRed(state, logo) {
			state.logoRed = logo
		},
		//登陆过后保存用户信息
		saveUser(state, data) {
			state.token = data.message
			uni.setStorageSync('token', data.message)
		},
		//刷新用户信息
		refreshUser(state, data) {
			state.user = data
			uni.setStorageSync('user', data)
		},
		//清除用户信息
		deleteUser(state) {
			state.token = '',
			state.user = {}

			uni.removeStorageSync('token')
			uni.removeStorageSync('user')
		},
		//设置语言
		setLang(state,_lang){
			state.lang = _lang
			let _lg = 'en'
			if (_lang == 'zh') {
				_lg = 'zh-Hans'
			} else if (_lang == 'hk') {
				_lg = 'zh-Hant'
			} else if (_lang == 'fra') {
				_lg = 'fr'
			} else if (_lang == 'spa') {
				_lg = 'es'
			}
			const i18n_lang = i18n.messages[_lang]
			for (let i in state.coins) {
				state.coins[i]['type'] = i18n_lang.common.DIGICCY
			}
			state.assetsType = [{
				id: 1,
				name: 'legal',
				type: i18n_lang.fund.fiat + i18n_lang.fund.account,
			}, {
				id: 2,
				name: 'change',
				type: i18n_lang.fund.exchange + i18n_lang.fund.account,
			}, {
				id: 3,
				name: 'lever',
				type: i18n_lang.fund.leverage + i18n_lang.fund.account,
			}, {
				id: 4,
				name: 'micro',
				type: i18n_lang.fund.second + i18n_lang.fund.account,
			}]
			uni.setLocale(_lg)
		},
		//存储所有法币汇率和当前所选的法币汇率
		setFiats(state,arr){
			let has = arr.find(item => item.currency_code == state.fiat.currency_code)
			if(has) state.fiat = has
			state.fiats = arr
			
			
			uni.setStorageSync('fiat',state.fiat)
			uni.setStorageSync('fiats',state.fiats)
		},
		saveFiat(state,fiat){
			state.fiat = fiat
			uni.setStorageSync('fiat',state.fiat)
		}

	}
})

export default store
