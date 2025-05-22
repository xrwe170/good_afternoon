<template>
	<view class="">
		
		<view class="set-top">
			<view class="d-flex justify-content-between align-items-center">
				<image src="/static/image/img/set-return.png" style="width: 50rpx; height: 50rpx;" @tap="goBack"></image>
				<image src="/static/image/img/ke-fu-fff.png" style="width: 50rpx; height: 50rpx;" @click="$utils.jump('/pages/kefu/index')"></image>
			</view>
			<navigator class="set-header d-flex align-items-center justify-content-between" :url="$store.state.token ? '/pages/setting/userInfo' : '/pages/common/login'">
				<view class="d-flex align-items-center">
					<image :src="user.head_portrait   | retImageUrl" class="border-radius-50per border header" mode="aspectFill"></image>
					<view>
						<view class="user-name">{{user.id ? user.account_number : $t("common.plsLogin")}}</view>
						<view class="d-flex align-items-center user-info">
							<text class="d-block" v-if="user.id">ID:{{user.id}}</text>
							<text class="d-block" v-if="user.score">{{i18n.score}}:{{Number(user.score)}}</text>
						</view>
					</view>
				</view>
				
				<view class="d-flex align-items-center" style="position: absolute; right: 22rpx;">
					<view class="u-tag u-size-default u-shape-square u-mode-dark-info" v-if="user.review_status == 0">
						{{i18n.goAudit}}
					</view>
					<view class="u-tag u-size-default u-shape-square u-mode-dark-warning" v-if="user.review_status == 1">
						{{i18n.auditing}}
					</view>
					<view class="u-tag u-size-default u-shape-square u-mode-dark-success" v-if="user.review_status == 2">
						{{i18n.hasaudit}}
					</view>
					<image src="/static/image/img/gengduo1.png" style="width: 50rpx; height: 50rpx;"></image>
				</view>
			</navigator>
			<navigator>
				<view class="set-top-find">
					<view class="d-flex justify-content-between align-items-center">
						<view>
							<text class="inviteDesc">
								{{ i18n.inviteDesc }}
							</text>
						</view>
						<view class="d-flex align-items-center">
							<text class="inviteDesc2">
								{{ i18n.inviteDesc2 }}
							</text>
						</view>
					</view>
					<view class="d-flex set-team">
						<view>
							<text class="d-block">{{i18n.inviteNumber}}</text>
							<text class="d-block">0</text>
						</view>
						<view>
							<text class="d-block">{{i18n.myRebate}}</text>
							<text class="d-block">0</text>
						</view>
					</view>
				</view>
				
			</navigator>
		</view>
		
		<view class="pb-60 px-30">
			<!-- <u-navbar :title="i18n.mine">
				<u-icon slot="right" name="server-man"  size="38" @click="$utils.jump('/pages/kefu/index')"></u-icon>
			</u-navbar> -->
			<!-- <navigator :url="$store.state.token ? '/pages/setting/userInfo' : '/pages/common/login'" class=" d-flex align-items-center justify-content-between mt-30">
				<view class="d-flex  align-items-center">
					<image :src="user.head_portrait   | retImageUrl" class="border-radius-50per border" mode="aspectFill" style="width: 120rpx;height:120rpx;"></image>
					<view class="ml-22">
						<text class="d-block font-size-36 font-weight-bold">{{user.id ? user.account_number : $t("common.plsLogin")}}</text>
						<text class="d-block font-size-22 opacity-50 mt-4" v-if="user.id">ID:{{user.id}}</text>
						<text class="d-block font-size-22 opacity-50 mt-4" v-if="user.score">{{i18n.score}}:{{Number(user.score)}}</text>
					</view>
				</view>
				<text class="iconfont icon-gengduo1 font-size-40"></text>
			</navigator> -->
			<!-- <view class="fanli text-black mt-40">
				<view class="d-flex justify-content-between align-items-center">
					<text class="font-size-30 ">{{i18n.inviteDesc}}</text>
					<text class="haoyou">{{i18n.inviteDesc2}}</text>
					<text class="iconfont text-black icon-gengduo1 font-size-32"></text>
				</view>
				<view class="data">
					<view class="w-33">
						<text class="d-block font-size-22 opacity-75">{{i18n.inviteNumber}}</text>
						<text class="d-block font-size-32 mt-24 font-weight-bold">0人</text>
					</view>
					<view class="w-33 text-center">
						<text class="d-block font-size-22 opacity-75">{{i18n.inviteTransNumber}}</text>
						<text class="d-block font-size-32 mt-24 font-weight-bold">0人</text>
					</view>
					<view class="w-33 text-right">
						<text class="d-block font-size-22 opacity-75">{{i18n.myRebate}}</text>
						<text class="d-block font-size-32 mt-24 font-weight-bold">0.00 USDT</text>
					</view>
				</view>
			</view> -->
			
			<view class="setting-nav mt-40">
				<view class="item" v-for="item in navs" :key="item.value" @click="navFunc(item)">
					<view class="d-flex align-items-center">
						<image :src="item.image" class="image"></image>
						<text class="opacity-90">{{item.name}}</text>
					</view>
					<view class="d-flex align-items-center opacity-90" v-if="item.value == 'Default fiat currency'">
						<text>1USD {{$store.state.fiat.currency_code == 'USD' ? '=' : '≈'}} {{$store.state.fiat.rate + $store.state.fiat.currency_code}}</text>
						<u-icon name="arrow-down" class="ml-12"></u-icon>
					</view>
					<view class="opacity-90" v-else-if="item.value == 'Language'">
						<text>{{lang.name}}</text>
					</view>
				</view>
			</view>
			
			<button class="logout mt-28" @click="logout">{{$t("common.logout")}}</button>
			
			<u-popup v-model="showLanguage" mode="bottom" length="60%" :title="$t('setting.selectLang')">
				<view class="popup-list">
					<view class="popup-list-item" v-for="item in langs" :key="item.value" :class="{active : item.selected}"  @click="setLang(item)">
						<text>{{item.name}}</text>
					</view>
				</view>
			</u-popup>
			
			<u-popup v-model="showDefaultCurrency" mode="bottom" length="80%" :title="$t('setting.selectCoinType')">
				<view class="popup-list">
					<view class="popup-list-item" v-for="(item,index) in $store.state.fiats" :key="item.currency_code" :class="{active : item.currency_code == $store.state.fiat.currency_code }" @click="saveFiat(index)">
						<text class="pl-18 pr-14 font-size-32">{{item.currency_code}}</text>
					</view>
				</view>
			</u-popup>
		</view>
	</view>
</template>

<script>
	import {langs} from "./data.js"
	export default {
		data() {
			return {
				user:{},
				langs:null,
				lang:null,
				fiats:null,
				//显示修改默认法币
				showDefaultCurrency:false,
				//显示修改默认语言
				showLanguage:false,
			}
		},
		methods: {
			
		},
		onShow() {
			this.setDefaultLang()
			this.getUserInfo()
		},
		methods:{
			goBack() {
				// 如果自定义了点击返回按钮的函数，则执行，否则执行返回逻辑
				if (typeof this.customBack === 'function') {
					// 在微信，支付宝等环境(H5正常)，会导致父组件定义的customBack()函数体中的this变成子组件的this
					// 通过bind()方法，绑定父组件的this，让this.customBack()的this为父组件的上下文
					this.customBack.bind(this.$u.$parent.call(this))();
				} else {
					uni.navigateBack();
				}
			},
			setDefaultLang(){
				let langsData = langs.map(el=>{
					el.selected = false
					return el
				})
				const lang = uni.getStorageSync('lang') || 'en'
				const has = langsData.findIndex(item => item.value == lang)
				this.lang = langsData[has]
				
				langsData[has].selected = true
				this.langs = langsData
			},
			//保存法币
			saveFiat(index){
				const fiat = this.$store.state.fiats[index]
				this.$store.commit('saveFiat',fiat)
				this.showDefaultCurrency = false
			},
			navFunc(item){
				const {openType,url} = item
				if(openType == 'url'){
					uni.navigateTo({
						url
					})
				}else if(openType == 'popup'){
					this[url] = true
				}else if(openType == 'link'){
					// #ifdef APP-PLUS
						plus.runtime.openURL(url);
					// #endif
					// #ifdef H5
						window.location.href = url;
					// #endif
				}
			},
			setLang(item){
				let langs = this.langs.map(el=>{
					el.selected = false
					if(el.value == item.value) el.selected = true
					return el
				})
				this.langs = langs
				this._i18n.locale = item.value 
				this.lang = item
				uni.setStorageSync('lang',item.value)
				this.$store.commit('setLang', item.value)
				setTimeout(()=>{
					this.showLanguage = false
				},200)
			},
			//获取个人信息
			getUserInfo(){
				this.$u.api.setting.getUserInfo().then(res=>{
					this.user = res.message
					this.$store.commit('refreshUser',res.message)
				})
			},
			//退出登陆
			async logout(){
				const ret = await this.$utils.showModal(this.$t("common.hint"),this.$t("setting.confirmLogout"))
				if(!ret) return
				this.$u.api.user.logout().then(res=>{
					this.$utils.showToast(res.message)
					this.$store.commit('deleteUser')
					setTimeout(()=>{
						uni.reLaunch({
							url:'/pages/index/index'
						})
					},1200)
				})
			}
		},
		computed:{
			i18n(){
				return this.$t("setting");
			},
			navs(){
				const _i18n = this._i18n
				const message = _i18n.messages[_i18n.locale].setting
				return [
					// {
					// 	name:message.bill,
					// 	value:'Bill',
					// 	image:require('static/image/icon/setting-icon-1.png'),
					// 	show:true,
					// 	openType:'url',
					// 	url:'/pages/setting/bill',
					// },
					{
						name:message.wallet,
						value:'Receiving Account',
						image:require('static/image/img/icon/setting-icon-1.png'),
						show:true,
						openType:'url',
						url:'/pages/setting/wallet',
					},
					{
						name:message.securitySettings,
						value:'Security settings',
						image:require('static/image/img/icon/setting-icon-2.png'),
						show:true,
						openType:'url',
						url:'/pages/setting/security',
					},
					// {
					// 	name:message.banks,
					// 	value:'Banks',
					// 	image:require('static/image/icon/setting-icon-13.png'),
					// 	show:true,
					// 	openType:'url',
					// 	url:'/pages/setting/bank',
					// },
					// {
					// 	name:message.banksInternational,
					// 	value:'Banks - International',
					// 	image:require('static/image/icon/setting-icon-13.png'),
					// 	show:true,
					// 	openType:'url',
					// 	url:'/pages/setting/bank_international',
					// },
					// {
					// 	name:message.coupon,
					// 	value:'Coupon',
					// 	image:require('static/image/icon/setting-icon-4.png'),
					// 	show:true,
					// 	openType:'url',
					// 	url:'/pages/setting/coupon',
					// },
					// {
					// 	name:message.systemNotification,
					// 	value:'System notification',
					// 	image:require('static/image/icon/setting-icon-5.png'),
					// 	show:false,
					// 	openType:'url',
					// 	url:'',
					// },
					{
						name:message.defaultFiatCurrency,
						value:'Default fiat currency',
						image:require('static/image/img/icon/setting-icon-3.png'),
						show:true,
						openType:'popup',
						url:'showDefaultCurrency',
					},
					{
						name:message.language,
						value:'Language',
						image:require('static/image/img/icon/setting-icon-4.png'),
						show:true,
						openType:'popup',
						url:'showLanguage',
					},
					{
						name:message.faq,
						value:'FAQ',
						image:require('static/image/img/icon/setting-icon-5.png'),
						show:true,
						openType:'url',
						url:'/pages/common/faq',
					},
					// {
					// 	name:message.operationalCompliance,
					// 	value:'Operational Compliance',
					// 	image:require('static/image/icon/setting-icon-9.png'),
					// 	show:true,
					// 	openType:'url',
					// 	url:'/pages/setting/operational_compliance',
					// },
					{
						name:message.share,
						value:'Share',
						image:require('static/image/img/icon/setting-icon-6.png'),
						show:false,
						openType:'url',
						url:'/pages/setting/share',
					},
					{
						name:message.contactUs,
						value:'Contact us',
						image:require('static/image/img/icon/setting-icon-7.png'),
						show:true,
						openType:'url',
						url:'/pages/common/contactus',
					},
					// {
					// 	name:message.aboutUs,
					// 	value:'About us',
					// 	image:require('static/image/icon/setting-icon-12.png'),
					// 	show:true,
					// 	openType:'url',
					// 	url:'/pages/common/aboutus',
					// },
					{
						name:message.download,
						value:'Download App',
						image:require('static/image/img/icon/setting-icon-8.png'),
						show:true,
						openType:'link',
						url:  'https://app.letternyse.com',
					},
				]
			}
		}

	}
</script>

<style lang="scss" scoped>
	
.set-top {
    background: #2e5cd1;
    padding: 29rpx 29rpx 0 29rpx;
	.header {
	    width: 125rpx;
	    height: 125rpx;
	    margin-right: 33rpx;
	}
	.user-name {
	    font-size: 38rpx;
	    font-family: Roboto;
	    font-weight: 500;
	    color: #fff;
	    padding-bottom: 15rpx;
	}
	.user-info {
		font-size: 24rpx;
		font-family: Roboto;
		font-weight: 400;
		color: #fff;
	}
	.user-info > uni-text:nth-child(1) {
	    margin-right: 17rpx;
	}
}
.set-header{
	padding: 35rpx 0;
}

.u-size-default {
    font-size: 22rpx;
    padding: 11rpx 22rpx;
}
.u-mode-dark-primary{background-color:#2979ff;color:#fff}
.u-mode-dark-success{background-color:#19be6b;color:#fff}
.u-mode-dark-error{background-color:#fa3534;color:#fff}
.u-mode-dark-warning{background-color:#ffcc8f;color:#fff}
.u-mode-dark-info{background-color:#909399;color:#fff}

.u-tag {
    box-sizing: border-box;
    align-items: center;
    border-radius: 6rpx;
    display: inline-block;
    line-height: 1;
}
.set-top-find{
	padding: 20rpx 15rpx;
	border-radius: 15rpx 15rpx 0 0;
	box-shadow: 0px 11px 36px -4px rgba(24, 39, 75, .1), 0px 7px 16px -6px rgba(24, 39, 75, .12);
	background: #fff;
	.inviteDesc{
		font-size: 30rpx;
		font-family: Roboto;
		font-weight: 400;
		color: #333;
	}
	.inviteDesc2{
		background: #ffb016;
		border-radius: 8rpx;
		padding: 10rpx 15rpx;
		font-size: 20rpx;
		font-family: Roboto;
		font-weight: 400;
		color: #fff;
		display: block;
	
	}
	
	.set-team{
	    padding-top: 30rpx;
		& > uni-view {
			width: 50%;
			text-align: center;
			& > uni-text:nth-child(1) {
				font-size: 24rpx;
				font-family: Roboto;
				font-weight: 500;
				color: #333;
				margin-bottom: 15rpx;
			}
			
			& > uni-text:nth-child(2) {
				font-size: 42rpx;
				font-family: Roboto;
				font-weight: 500;
				color: #2e5cd1;
			}
		}
	}
	
}
	
.fanli{
	@extend .linear-gradient-button;
	border-radius: 60rpx 20rpx 60rpx 20rpx;
	// background-image:linear-gradient(60deg,#ff174d,#724685);
	padding: 36rpx;
	padding-bottom: 0;
	overflow: hidden;
	.haoyou{
		font-size: 22rpx;
		padding: 8rpx 30rpx;
		color: #fff;
		background-image:linear-gradient(to right,#ff174d,#724685);
		border-radius: 20rpx;
	}
	.data{
		margin:0 -36rpx;
		margin-top: 36rpx;
		background-color:rgba(255,255,255,.1) ;
		padding: 32rpx 36rpx 26rpx 36rpx;
		display: flex;
		justify-content: space-between;
	}
}

.setting-nav{
	.item{
		margin-bottom: 12rpx;
		padding:  26rpx 32rpx;
		border-radius: 12rpx;
		box-shadow: 0px 0px 8.9px 1.1px rgba(0, 0, 0, 0.05);
		display: flex;
		justify-content: space-between;
		align-items: center;
		font-size: 32rpx;
		.image{
			width: 48rpx;
			height: 48rpx;
			margin-right: 17rpx;
		}

	}
}

.logout{
	background: #2e5cd1;
	box-shadow: 0px 11px 36px -4px rgba(24, 39, 75, .1), 0px 7px 16px -6px rgba(24, 39, 75, .12);
	border-radius: 18rpx;
	font-size: 29rpx;
	font-family: Roboto;
	font-weight: 500;
	color: #fff;
	height: 108rpx;
	line-height: 108rpx;
}


// 弹出层列表
.popup-list{
	.popup-list-item{
		height: 96rpx;
		line-height: 96rpx;
		padding: 0 30rpx;
		font-size: 32rpx;
		position: relative;
		display: flex;
		align-items: center;
		&:before{
			content: "";
			position: absolute;
			left: 30rpx;
			right: 30rpx;
			bottom: 0;
			height: 2rpx;
			background-color: #efefef;
		}
		&.active{
			background-color: #f2f6ff;
			&:after{
				content: "";
				width: 36rpx;
				height: 20rpx;
				background-image: url('../../static/image/icon/setting-icon-20.png');
				background-size: cover;
				position: absolute;
				right: 80rpx;
				top: 50%;
				margin-top: -10rpx;
			}
		}
	}
}

.button-base{
	background-image: linear-gradient(to right,#dd1d46,#f9577a);
	color: #fff;
}
</style>
