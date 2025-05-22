<template>
	<view class="mx-30">
		<view class="status_bar"></view>
		<view class="my-30 text-center" style="height: 88rpx;line-height: 88rpx;">
			<view class="white-circle box-shadow position-absolute" @click="$utils.jump(1,'navigateBack')">
				<u-icon name="arrow-leftward" size="38"></u-icon>
			</view>
			<text class="font-size-36 text-deepblue font-weight-bold">{{i18n.resell}}</text>
		</view>
		<view class="mt-50 d-flex-between-center">
			<view class="animated fadeInLeft">
				<image :src="data.image | retImageUrl" class="box-shadow"
					style="width: 200rpx;height: 200rpx;border-radius: 20rpx;"></image>
			</view>
			<view class="ml-24 animated fadeInRight" style="flex: 1;">
				<text class="d-block text-deepblue font-weight-bold ">{{data.name}}</text>
				<text class="d-block text-deepblue font-weight-bold mt-20">{{i18n.createdBy}}:{{data.author_name || i18n.nonickname}}</text>
				<text class="d-block text-error font-weight-bold mt-20 font-size-32">{{i18n.buyPrice}}:{{Number(data.price) + data.currency_name}}</text>
			</view>
		</view>
		<!-- 修改信息 -->
		<view class="mt-100 text-deepblue">
			<view class="position-relative">
				<text class="form-label">{{i18n.price}}</text>
				<input type="digit" class="nft-input mt-30" v-model="form.price">
				<text class="position-absolute" style="right: 30rpx;bottom: 30rpx;">{{data.currency_name}}</text>
			</view>
			<view class="position-relative mt-70">
				<text class="d-inline-block opacity-75 font-size-30 bg-white position-absolute px-20"
					style="left: 20rpx;top: -20rpx;">{{i18n.pay_type_2}}</text>
				<view class="nft-input mt-30" @click="showPayTypeSelect=true">{{payTypeNum2Name(form.pay_type)}}</view>
				<!-- <u-icon name="arrow-down" size="32" class="position-absolute" color="#0d2758"
					style="right: 30rpx;bottom: 22rpx;"></u-icon> -->
				<!-- <u-action-sheet :list="payTypes" v-model="showPayTypeSelect" @click="selectPayType"></u-action-sheet> -->
			</view>
			<view class="position-relative mt-70" v-if="form.pay_type == 2">
				<text class="form-label">{{i18n.margin}}</text>
				<input type="digit" class="nft-input mt-30" v-model="form.per_increase">
				<text class="position-absolute" style="right: 30rpx;bottom: 30rpx;">USDT</text>
			</view>
			<view class="position-relative mt-70">
				<text class="form-label">{{i18n.cjkssj}}</text>
				<view class="nft-input mt-30" @click="showStartTimeSelect=true">{{form.start_time}}</view>
				<u-icon name="arrow-down" size="32" class="position-absolute" color="#0d2758"
					style="right: 30rpx;bottom: 22rpx;"></u-icon>
				<u-picker mode="time" v-model="showStartTimeSelect" :params="timeParams"
					@confirm="confirmStartTimeSelect"></u-picker>
			</view>
			<view class="position-relative mt-70">
				<text class="form-label">{{i18n.cjjzsj}}</text>
				<view class="nft-input mt-30" @click="showEndTimeSelect=true">{{form.end_time}}</view>
				<u-icon name="arrow-down" size="32" class="position-absolute" color="#0d2758"
					style="right: 30rpx;bottom: 22rpx;"></u-icon>
				<u-picker mode="time" v-model="showEndTimeSelect" :params="timeParams" @confirm="confirmEndTimeSelect">
				</u-picker>
			</view>
		</view>
		
		<!-- 转卖 -->
		<view class="fixed-nav animated fadeInUp" @click="resell" >
			{{i18n.confirmResell}}
		</view>
	</view>
</template>

<script>
	//转卖
	export default {
		data() {
			return {
				data: {},
				form: {
					price: '',
					start_time: '',
					end_time: '',
					pay_type:1,
					per_increase:'',
				},
				showPayTypeSelect: false,
				showEndTimeSelect: false,
				showStartTimeSelect: false,
				timeParams: {
					year: true,
					month: true,
					day: true,
					hour: true,
					minute: true,
				}
			};
		},
		onLoad(options) {
			const {
				id
			} = options
			if (!id) {
				this.$utils.showToast(this.$t("common.paramsWrong"));
				setTimeout(() => {
					uni.navigateBack({
						delta: 1
					})
				}, 1200)
				return
			}
			this.id = id
		},
		onShow() {
			this.getArtworkDetail()
		},
		methods: {
			getArtworkDetail() {
				this.$u.api.nft.getArtworkDetail(this.id).then(res => {
					let data = res.message
					data.price = Number((+data.price).toFixed(4))
					const nowTime = Date.parse(new Date());
					this.form = {
						price: Number((+data.price).toFixed(4)),
						start_time: this.$u.timeFormat(nowTime, 'yyyy-mm-dd hh:MM:ss'),
						end_time: this.$u.timeFormat(nowTime + 24 * 60 * 60 * 1000, 'yyyy-mm-dd hh:MM:ss'),
						pay_type:data.pay_type,
						per_increase:data.per_increase
					}
					this.data = data
				})
			},
			payTypeNum2Name(payTypeNum) {
				payTypeNum = Number(payTypeNum)
				console.log(payTypeNum);
				const {
					i18n
				} = this
				return this.payTypes[payTypeNum - 1].text
			},
			confirmStartTimeSelect(e) {
				this.form.start_time = `${e.year}-${e.month}-${e.day} ${e.hour}:${e.minute}:00`
			},
			confirmEndTimeSelect(e) {
				this.form.end_time = `${e.year}-${e.month}-${e.day} ${e.hour}:${e.minute}:00`
			},
			//转卖
			async resell(){
				const {i18n} = this
				const {code,pay_type} = this.data
				const {price,start_time,end_time,per_increase} = this.form
				
				if(!this.$u.test.amount(price)){
					return this.$utils.showToast(i18n.wrongPrice)
				}
				if(pay_type == 2 && !this.$u.test.amount(per_increase)){
					return this.$utils.showToast(i18n.wrongPerIncrease)
				}
				if(Date.parse(new Date(start_time)) > Date.parse(new Date(end_time))){
					return this.$utils.showToast(i18n.wrongTime)
				}
				
				const ret = await this.$utils.showModal(this.$t("common.hint"),i18n.confirmResell2)
				if(!ret) return
				
				this.$u.api.nft.resell({
					code,price,start_time,end_time,per_increase
				}).then(res=>{
					this.$utils.showToast(this.$t("common.success"))
					setTimeout(()=>{
						uni.redirectTo({
							url:'/pages/nft/mine'
						})
					},1200)
				})
				
			}
		},
		computed: {
			i18n() {
				return this.$t("nft")
			},
			payTypes() {
				const i18n = this.$t("nft")
				return [{
					text: i18n.normal
				}, {
					text: i18n.auction
				}, {
					text: i18n.bindBox
				}]
			}
		},

	}
</script>
<style>
	page,html,body{
		background-color: white;
	}
</style>
<style lang="scss" scoped>
	.nft-input {
		border: 2rpx solid rgba(13, 39, 88, .3);
		border-radius: 10rpx;
		height: 100rpx;
		padding: 10rpx 30rpx 0 30rpx;
		font-size: 32rpx !important;
		line-height: 100rpx;
		box-sizing: border-box;
	}
	
	.fixed-nav{
		position: fixed;
		bottom: 10vw;
		@extend .box-shadow;
		height: 100rpx;
		border-radius: 50rpx;
		transition: all .8s ease 0s;
		z-index: 999;
		background-color: #fff;
		left: 20vw;
		right: 20vw;
		display: flex;
		justify-content: center;
		align-items: center;
		font-weight: bold;
		font-size:36rpx;
		background-color: #000000;
		color: #fff;
		&.fixed-nav-3{
			background: none;
			background-color: #E4E4E1;
			background-image: radial-gradient(at top center, rgba(255,255,255,0.03) 0%, rgba(0,0,0,0.03) 100%), linear-gradient(to top, rgba(255,255,255,0.1) 0%, rgba(143,152,157,0.60) 100%);
			background-blend-mode: normal, multiply;
			border: none;
			font-size: 28rpx;
		}
	}
	
	.form-label{
		display: inline-block;
		position: absolute;
		left: 20rpx;
		top: -20rpx;
		background-color: #fff;
		padding: 0 20rpx;
		color: rgba(13,39,88,.75);
	}
</style>
