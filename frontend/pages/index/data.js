import i18n from '@/common/locales/config.js'
const lang = i18n.messages[i18n.locale].home

const highQualityProject = [{
		name: 'DeFi挖矿',
		item: [{
				icon: require('static/image/icon/home-icon-7.png'),
				text1: 'USDC挖矿',
				text2: '5000 USDC起存',
				text3: '20.56%',
				text4: '参考年化'
			},
			{
				icon: require('static/image/icon/home-icon-7.png'),
				text1: 'USDC挖矿',
				text2: '5000 USDC起存',
				text3: '20.56%',
				text4: '参考年化'
			},
			{
				icon: require('static/image/icon/home-icon-7.png'),
				text1: 'USDC挖矿',
				text2: '5000 USDC起存',
				text3: '20.56%',
				text4: '参考年化'
			}
		]
	},
	{
		name: '云矿机',
		item: [{
			icon: require('static/image/icon/home-icon-7.png'),
			text1: 'USDC挖矿',
			text2: '5000 USDC起存',
			text3: '20.56%',
			text4: '参考年化'
		}]
	}
]

const homeNav = [{
		icon: require('static/image/icon/home-nav-7.png'),
		text: lang.recharge,
		url:'/pages/fund/select?url=receive',
		openType:'navigateTo'
	},
	{
		icon: require('static/image/icon/home-nav-1.png'),
		url:'/pages/fund/select?url=withdraw',
		text:  lang.withdraw,
		openType:'navigateTo'
	},
	{
		icon: require('static/image/icon/home-nav-2.png'),
		text: lang.futures,
		url:'/pages/contract/contract',
		openType:'switchTab'
	},
	{
		icon: require('static/image/icon/home-nav-3.png'),
		text: lang.seconds,
		url:'/pages/seconds/seconds',
		openType:'switchTab'
	},
	{
		icon: require('static/image/icon/ieo.svg'),
		text: lang.ieo,
		url:'/pages/ieo/ieo',
		openType:'navigateTo'
	},
	{
		icon: require('static/image/icon/home-nav-4.png'),
		text: lang.lockming,
		url:'/pages/lockming/lockming',
		openType:'navigateTo'
	},
	{
		icon: require('static/image/icon/home-nav-5.png'),
		text: lang.margin,
		openType:'navigateTo'
	},
	{
		icon: require('static/image/icon/home-nav-6.png'),
		text: lang.transfer,
		url:'/pages/fund/transfer',
		openType:'navigateTo'
	},
]
let gupiao = [

]
let obj = {
	code: 'US',
	name: '道琼斯',
	num1: 1233.4,
	num2: -8.7,
	num3: '-0.02%'
}
for (let i = 0; i < 5; i++) {
	let oobj = JSON.parse(JSON.stringify(obj))
	let ii = i % 2 == 0 ? -1 : 1
	oobj.num2 = oobj.num2 * ii
	gupiao.push(oobj)
}

let coinCurrencyMarket = [
	{
		from:{
			code:'BTC',
			name:'比特币'
		},
		to:{
			code:'USDT',
			name:'泰达币'
		},
		price:56715.15,
		price2:365149.15,
		change:'1.1%'
	},
	{
		from:{
			code:'BTC',
			name:'比特币'
		},
		to:{
			code:'USDT',
			name:'泰达币'
		},
		price:56715.15,
		price2:365149.15,
		change:'-2.2%'
	},
	{
		from:{
			code:'BTC',
			name:'比特币'
		},
		to:{
			code:'USDT',
			name:'泰达币'
		},
		price:56715.15,
		price2:365149.15,
		change:-3.3
	},
	{
		from:{
			code:'BTC',
			name:'比特币'
		},
		to:{
			code:'USDT',
			name:'泰达币'
		},
		price:56715.15,
		price2:365149.15,
		change:4.4
	}
]


export {
	highQualityProject,
	homeNav,
	gupiao,
	coinCurrencyMarket
}
