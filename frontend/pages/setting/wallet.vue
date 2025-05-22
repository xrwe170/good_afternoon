<template>
	<view>
		<u-navbar :title="$t('setting.wallet')" :borderBottom="false">
			<!-- <view slot="right" class="nav-right text-333" @click="showFilter=true">
				<text>全部</text>
				<text class="iconfont icon-shaixuan"></text>
			</view> -->
		</u-navbar>
		
		<view class="mx-30">
			
			
			<view class="list">
				<!-- <view class="d-flex opacity-50">
					<text class="font-size-32">{{$t("common.edit")}}</text>
					<text class="font-size-32 ml-30">{{$t("common.delete") | removeSpace}}</text>
				</view> -->
				<block v-if="list.length">
					<view class="item" v-for="item in list" :key="item.id">
						<view class="d-flex align-items-center">
							<image :src="item.qrcode | retImageUrl" class="image"></image>
							<view style="flex: 1;width: 500rpx;">
								<text class="wa-name d-block">{{item.name}}</text>
								<text class="wa-address d-block mt-8" style="word-wrap:break-word;">{{item.address}}</text>
							</view>
						</view>
						<!-- <view class="d-flex-between-center pt-20 mt-20 u-border-top">
							<view class="d-flex w-50">
								<text class="font-size-28 opacity-50 mr-28">{{$t("setting.totayIncome")}}</text>
								<text class="font-size-28">{{item.todayIncome}} {{item.coinType || 0}}</text>
							</view>
							<view class="d-flex w-50 text-right">
								<text class="font-size-28 opacity-50 mr-28">{{$t("setting.totalIncome")}}</text>
								<text class="font-size-28">{{item.totalIncome}} {{item.coinType || 0}}</text>
							</view>
						</view> -->
					</view>
				</block>
				<default-page v-else></default-page>
			</view>
			
			<view class="flex-bot">
				<button class="sub-btn" @click="$utils.navigateTo('/pages/setting/addWallet')">
					<text class="iconfont icon-jia1 mr-20"></text>
					<text>{{$t("setting.addNewAccount")}}</text>
				</button>
			</view>
		</view>
				
		<!-- <u-popup v-model="showFilter"  mode="right" length="86%" border-radius="0" :title="$t('common.filter')">
			<view class="mx-30 popup-filter">
				<view class="">
					<text class="d-block font-size-28 mb-20">币种选择</text>
					<view class="d-flex flex-wrap">
						<text class="item" :class="currentCurrency == 0 ? 'active' : ''" @click="selectCurrencyFunc({id:0,name:'全部'})">全部</text>
						<text class="item" v-for="item in currencyList" :class="currentCurrency == item.id ? 'active' : ''" @click="selectCurrencyFunc(item)">{{item.name}}</text>
					</view>
				</view>
			</view>
			<view class="popup-filter-btns">
				<button class="secondary-button btn">{{$t("common.reset")}}</button>
				<button class="error-button btn">{{$t("common.confirm")}}</button>
			</view>
		</u-popup> -->
	</view>
</template>

<script>
	export default {
		data() {
			return {
				showFilter:false,
				legal:[],
				list:[
					{
						id:1,
						coinType:"CNY",
						receiveType:"微信",
						username:"迷糊先生",
						account:"qlwelqje222",
						todayIncome:0,
						totalIncome:100.0
					}
				],
				showAdd:false,
				canGet:true,
				page:1,
				currencyList:[],
				currentCurrency:0,
				originalList:[],
			};
		},
		onShow() {
			this.list = []
			this.originalList = []
			this.canGet = true
			//获取币种列表
			this.getWalletAddressList()
			this.getCurrencyList()
		},
		methods:{
			getCurrencyList(){
				this.$u.api.setting.getCurrencyList().then(res=>{
					this.currencyList = res.message.legal
				})
			},
			getWalletAddressList(){
				if(!this.canGet) return
				this.$u.api.setting.getWalletAddressList(this.page,99999).then(res=>{
					this.originalList = [...this.originalList,...res.message.data]
					if(this.originalList.length == res.message.total){
						this.canGet = false
					}else{
						this.page++
					}
					if(this.currentCurrency == 0){
						this.list = this.originalList
					}
				})
			},
			selectCurrencyFunc(item){
				this.$u.throttle(()=>{
					this.currentCurrency = item.id
					this.currentCurrencyName = item.name
					setTimeout(()=>{
						this.showFilter = false
					},300)
				},600)
			}
		},
		watch:{
			currentCurrency(val){
				if(val == 0){
					this.list = this.originalList
				}else{
					this.list = this.originalList.filter(item => item.currency == val)
				}
				
			}
		},
		onReachBottom() {
			this.getWalletAddressList()
		}
	}
</script>

<style lang="scss" scoped>
	
.nav-right{
	display: flex;
	align-items: center;
	border-radius: 24rpx;
	background-color: #f2f2f9;
	height: 48rpx;
	line-height: 48rpx;
	font-size: 22rpx;
	padding: 0 20rpx;
	.iconfont{
		margin-left: 8rpx;
		font-weight: bold;
		font-size: 22rpx;
	}
}
.flex-bot{
	width: 100%;
	background-color: #fff;
	padding: 46rpx 0;
	position: fixed;
	left: 0;
	bottom: 0;
	.sub-btn{
		margin: 0 18px;
		background: #2e5cd1;
		box-shadow: 0px 11px 36px -4px rgba(24, 39, 75, .1), 0px 7px 16px -6px rgba(24, 39, 75, .12);
		border-radius: 18rpx;
		font-size: 30rpx;
		font-family: Roboto;
		font-weight: 500;
		color: #fff;
		padding: 34rpx;
	}
}
.add-new-account-btn{
	font-size: 28rpx;
	display: flex;
	align-items: center;
	justify-content: center;
	height: 70rpx;
	line-height: 70rpx;
	background-color: #f2f2f9;
	margin: 0 56rpx;
	border-radius: 35rpx;
	margin-top: 30rpx;
	&::after{
		border: none;
	}
}

.list{
	margin-top: 40rpx;
	.item{
		margin-bottom: 12rpx;
		padding: 30rpx;
		border-radius:16rpx ;
		box-shadow: 0px 0px 8.9px 1.1px rgba(0, 0, 0, 0.05);
		.image{
			width: 120rpx;
			height: 120rpx;
			margin-right: 20rpx;
			border-radius: 10rpx;
			border: 2rpx solid $uni-color-secondary;
		}
	}
}

.popup-filter{
	.item{
		display: flex;
		align-items: center;
		font-size: 28rpx !important;
		background-color: #f2f2fa;
		height: 46rpx;
		line-height: 48rpx;
		border: 1px solid #f2f2fa;
		border-radius: 8rpx;
		margin-right: 18rpx;
		margin-bottom: 12rpx;
		padding: 0 12rpx;
		
		&.active{
			border-color: #c00028;
			color: #c00028;
			background-color: #fff;
		}
		
		.image{
			width: 28rpx;
			height: 28rpx;
			margin-right: 12rpx;	
		}
	}
}

.popup-filter-btns{
	margin: 0 22rpx;
	display: flex;
	justify-content: space-between;
	margin-top: 50rpx;
	.btn{
		width: 47%;
		font-size: 24rpx;
	}
}
</style>
