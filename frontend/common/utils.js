import i18n from "./locales/config.js"
let lang = i18n.messages[i18n.locale]

import store from '@/store';

import Decimal from '@/static/decimal.min.js'

const langStr = uni.getStorageSync('lang') || 'zh';

// 根据价格返回对应颜色
const getColor = (price) => {
	//判断是否为字符串
	if(typeof price == "string"){
		if(price.indexOf("%") > -1){
			price = price.slice(0,price.length - 1)
		}
		price = +price
	}
	if(price > 0){
		return "#28ba98"
	}else if(price == 0){
		return "#aaaaaa"
	}else{
		return "#F04A5A"
	}
}

//弹出确认弹框，以promise形式返回
const showModal = (title = lang.common.hint,content)=>{
	lang = i18n.messages[i18n.locale]
	return new Promise((resolve,reject)=>{
		uni.showModal({
			title,
			content,
			// #ifdef APP-PLUS
			//在app端，默认的确定在左，取消在右，这里把位置调换过来
			cancelText:lang.common.confirm,
			confirmText:lang.common.cancel,
			success(res) {
				if(res.confirm){
					resolve(false)
				}else{
					resolve(true)
				}
			}
			// #endif
			// #ifndef APP-PLUS
			cancelText:lang.common.cancel,
			confirmText:lang.common.confirm,
			success(res) {
				if(res.confirm){
					resolve(true)
				}else{
					resolve(false)
				}
			}
			// #endif
			
		})
	})
}

//弹出toast信息
const showToast = (title,icon = 'none') => {
	uni.showToast({
		title,
		icon
	})
}

//进行路由前，如果未登陆，则跳转至登录页
const navigateTo = (url,needToken = true)=>{
	let timer = 0
	if(needToken){
		const token = uni.getStorageSync('token') || ''
		if(!token){
			showToast(lang.common.plsLogin)
			url = "/pages/common/login"
			timer = 1200
		}
	}
	
	setTimeout(()=>{
		uni.navigateTo({
			url
		})
	},timer)
}

//当前语言转换成后端需要的语言
const langTrans = () => {
	let lang = uni.getStorageSync('lang') || 'zh';
	switch(lang){
		case 'zh':
			return 'zh-cn';
		case 'en':
			return 'en';
		case 'hk':
			return 'zh-tw';
	}
}

const uploadImage = () => {
	// #ifdef H5
	const sourceType =['album']
	// #endif
	// #ifndef H5
	const sourceType =['camera','album']
	// #endif
	return new Promise((resolve,reject)=>{
		const _this = this
		uni.chooseImage({
			count: 1, //默认9
			sizeType: ['original', 'compressed'], //可以指定是原图还是压缩图，默认二者都有
			sourceType, //从相册选择
			success: function(res) {
				const filePath = res.tempFilePaths[0]
				const file = res.tempFiles[0]
				uni.showLoading({
					title: lang.common.loading
				})
				uni.uploadFile({
					url: store.state.baseUrl + '/upload',
					filePath,
					name: 'file',
					header: {
						'accept-language': langTrans(),
						'Authorization': store.state.token,
					},
					success(res) {
						let result = res.data.replace(/\n/g, "").replace(/\n/g, "").replace(/\n/g, "").replace(/\s/g, "")
						res = JSON.parse(decodeURIComponent(result))
						showToast(lang.common.success)
						resolve(res.message)
					},
					fail(res){
						reject(res)
					},
					complete() {
						uni.hideLoading()
					}
				})
			}
		});
	})
}

const setTabbar = (that) => {
	const nav = that.$t("nav")
	for(let i = 0; i < nav.length; i++){
		uni.setTabBarItem({
			index:i,
			text:nav[i],
			fail(res) {
				console.log(res);
			}
		})
	}
}

const jump = (url,type = 'navigateTo') => {
	switch(type){
		case 'navigateTo':
			uni.navigateTo({
				url
			})
			return;
		case 'redirectTo':
			uni.redirectTo({
				url
			})
			return;
		case 'reLaunch':
			uni.reLaunch({
				url
			})
			return;
		case 'switchTab':
			uni.switchTab({
				url
			})
			return;
		case 'navigateBack':	
			uni.navigateBack({
				delta:url
			})
	}
}
 // 判断字符串是否为数字和字母组合
const checkIsNumAndWords = str =>{
   
    var reg =  /^[0-9a-zA-Z]*$/;  
    if (!reg.test(str)) {
        return false;
    } else {
        return true;
    }
}


const backHome = () => {
	uni.switchTab({
		url:'/pages/quotation/index.vue'
	})
}

//去除所有特殊字符
const charFilter = str => {
    var pattern = new RegExp("[`~!#$^&*()=|{}':;',\\[\\]<>/?~！#￥……&*（）——|{}【】‘；：”“'。，、？]", 'g');
    return str.replace(pattern, '');
}

//判断是否有特殊字符
const charTest = str => {
	var pattern = new RegExp("[`~!#$^&*()=|{}':;',\\[\\]<>/?~！#￥……&*（）——|{}【】‘；：”“'。，、？]", 'g');
	if(pattern.test(str)) return true
	return false
}

//返回买卖的英文
const retBuyAndSell = (str) => {
	str = str == '买' ? 'b' : 's'
	const obj = {
		'zh':{
			b:'买',
			s:'卖',
		},
		'en':{
			b:'Buy',
			s:'Sell',
		},
		'hk':{
			b:'買',
			s:'賣',
		}
	}
	return obj[langStr][str]
}

//根据当前语言返回名称
const retNameByLang = (item) => {
	switch(langStr){
		case 'zh':
			return item.option_name;
			break;
		case 'en':
			return item.en_name;
			break;
		case 'hk':
			return item.tw_name;
			break;
	}
}

//去除html标签
const removehtmltag = (str) => {
    return str.replace(/<[^>]+>/g, '')
}

// 获取url参数
const getQueryString = (name) => {
    const reg = new RegExp('(^|&)' + name + '=([^&]*)(&|$)', 'i');
    const search = window.location.search.split('?')[1] || '';
    const r = search.match(reg) || [];
    return r[2];
}

const stamp2Time = (ts,type = 'd') => {
	if(!ts) return 0
	if(typeof(ts) == 'string') ts = Number(ts)
	if(ts.toString().length > 10) ts = ts / 1000
	const d = parseInt(ts  / 60 / 60 / 24, 10);
	const h = parseInt(ts  / 60 / 60 % 24, 10);
	const m = parseInt(ts  / 60 % 60, 10);
	const s =parseInt(ts  % 60, 10);
	
	switch(type){
		case 'd':
			return checkTime(d);
		case 'h':
			return checkTime(h);
		case 'm':
			return checkTime(m);
		case 's':
			return checkTime(s);
	}
}

function checkTime(i){
	if (i < 10) {
	        i = "0" + i;
	    }
	return i;
}

const nftBuyStatus = (status,start_time,end_time) => {
	const nowTimestamp = Date.parse(new Date()) 
	if(typeof(start_time) != 'number') start_time = Date.parse(start_time)
	if(typeof(end_time) != 'number') end_time = Date.parse(end_time)
	
	//未开始
	if(nowTimestamp < start_time){
		return 2
	}
	
	if(nowTimestamp >= start_time && nowTimestamp <= end_time){
		//如果status是0已结束，则返回已结束
		if(status == 0) return 0
		return 1
	}
	
	if(nowTimestamp > end_time){
		return 0
	}
	
}

//解决js加减法的精度问题
const math = (a,method,b) => {
	switch(method){
		case '+':
			return new Decimal(a).add(new Decimal(b)).toNumber() ;
		case '-':
			return new Decimal(a).sub(new Decimal(b)).toNumber();
		case '*':
			return new Decimal(a).mul(new Decimal(b)).toNumber();
		case '/':
			return new Decimal(a).div(new Decimal(b)).toNumber();
	}
}

//根据币种返回对应颜色
const getCurrencyColor = (currency_name) => {
	const currencys = {
		'BTC':'#F2994A',
		'USDT':'#26A17B',
		'LTC':'#9EA3CA',
		'ETH':'#8DA0E0'
	}
	if(currencys[currency_name]) return currencys[currency_name]
	return '#8DA0E0'
}

//获取价格精度
const getPrecisionLength = (number) => {
	let length
	if(typeof(number) == 'number') number = String(number)
	length = number.indexOf('.') > -1 ? number.split('.')[1].length : 0
	return length
}


export default{
	getColor,
	showModal,
	showToast,
	navigateTo,
	uploadImage,
	setTabbar,
	jump,
	checkIsNumAndWords,
	backHome,
	charFilter,
	retBuyAndSell,
	retNameByLang,
	charTest,
	removehtmltag,
	getQueryString,
	stamp2Time,
	nftBuyStatus,
	math,
	getCurrencyColor,
	getPrecisionLength
}