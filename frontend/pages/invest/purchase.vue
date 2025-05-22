<template>
	<view class="text-white">
		<u-navbar :title="i18n.purchase"></u-navbar>
		<view class="mx-30 py-24">
			<!-- 年化收益率 -->
			<view class="d-flex-between-center py-6">
				<text class="font-size-24 opacity-50">{{i18n.apr}}</text>
				<text class="font-size-32 font-weight-bold" :style="{color:$downColor}">{{data.rate}}%</text>
			</view>
			<!-- 名称 -->
			<view class="d-flex-between-center py-6">
				<text class="font-size-24 opacity-50">{{i18n.name}}</text>
				<text>{{data.name}}</text>
			</view>
			<!-- 单份价格 -->
			<view class="d-flex-between-center py-6">
				<text class="font-size-24 opacity-50">{{i18n.perPrice}}</text>
				<text>{{data.exercise_price + data.currency_name}}</text>
			</view>
			<!-- 持仓期限 -->
			<view class="d-flex-between-center py-6">
				<text class="font-size-24 opacity-50">{{i18n.holdingDays}}</text>
				<text>{{data.holdingDays}}{{i18n.day}}</text>
			</view>
			<!-- 到期日 -->
			<view class="d-flex-between-center py-6">
				<text class="font-size-24 opacity-50">{{i18n.maturityDate}}</text>
				<text>{{data.end_time}}</text>
			</view>
		</view>
		<u-gap :height="6" bg-color="#3c3c3c"></u-gap>
		<view class="m-30 purchase">
			<view class="d-flex align-items-baseline justify-content-between">
				<text class="font-weight-bold font-weight-bold">{{i18n.buyShares}}</text>
				<text class="font-size-22 opacity-50 ">{{i18n.remain + data.remaining_number + i18n.share}}</text>
			</view>
			<view class="position-relative">
				<input type="digit" v-model="number" @input="calcIncome" class="input mt-20" :disabled="!inputDisabled">
				<text class="position-absolute opacity-50" style="right: 24rpx;top: 18rpx;">{{data.type == 'call' ? data.currency_name : 'USDT'}}</text>
			</view>
			<view class="font-size-24 mt-20">
				
				<text class="d-block my-6">1{{i18n.share}} = {{
					data.type == 'call' ? 0.1 + data.currency_name : (close / 10).toFixed(4) + 'USDT'
				}}</text>
				<text class="d-block my-6">{{i18n.estimatedIncome}}:{{expectedProfits}}</text>
				<text class="d-block mt-16">{{i18n.instruction}}</text>
				<view class="mt-8 p-20 bg-black border-radius-10" style="line-height: 38rpx;color: #CCC;">
					<text class="d-block">{{i18n.shuoming_1}}</text>
					<text class="d-block">{{i18n.shuoming_2}}</text>
					<text class="d-block" style="text-indent: 40rpx;">{{i18n.shuoming_3}}</text>
					<text class="d-block" style="text-indent: 40rpx;">{{i18n.shuoming_4}}</text>
					<text class="d-block">{{i18n.shuoming_5}}</text>
					<text class="d-block">{{i18n.shuoming_6}}</text>
					<text class="d-block">{{i18n.shuoming_7}}</text>
				</view>
			</view>
		</view>
		
		<button class="warning-button footer" @click="submit">{{i18n.purchase}} ( {{number}} {{data.type == 'call' ? data.currency_name : 'USDT'}} )</button>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				data:{},
				close:0, // 收盘价
				expectedProfits:0,
				number:null,
			};
		},
		onLoad(options) {
			const {id} = options
			if(!id){
				this.$utils.showToast(this.$t("common.paramsWrong"));
				setTimeout(()=>{
					uni.navigateBack({
						delta:1
					})
				},1200)
				return
			}
			this.id = id
		},
		onShow() {
			this.getDetail()
		},
		methods:{
			getDetail(){
				this.$u.api.invest.getDetail(this.id).then(res=>{
					let data = res.message
					data.holdingDays = this.datedifference(data.end_time,data.start_time)
					this.data = data
					this.getNowPrice()
				})
			},
			datedifference(sDate1, sDate2) {    //sDate1和sDate2是2006-12-18格式
				var dateSpan,
					tempDate,
					iDays;
				sDate1 = Date.parse(sDate1);
				sDate2 = Date.parse(sDate2);
				dateSpan = sDate2 - sDate1;
				dateSpan = Math.abs(dateSpan);
				iDays = Math.floor(dateSpan / (24 * 3600 * 1000));
				return iDays
			},
			getNowPrice(){
				this.$store.state.socket.on('daymarket', res => {
					if(res.currency_name == this.data.currency_name){
						this.close = res.close
					}
				
				});
			},
			//计算收益
			calcIncome(e){
				const value = e.detail.value //当前输入的份额值
				const {close,data} = this //code:收盘价
				const nowTimeStamp = Date.parse(new Date())
				const nowDate = this.$u.timeFormat(nowTimeStamp, 'yyyy-mm-dd')
				const holdingDays = this.datedifference(data.end_time,nowDate) //从当前日期到到期日的天数
				const rate = Number(data.rate) / 100 //年化收益率
				const {exercise_price} = data //挂钩参考价
				
				//如果购买的是BTC或者ETH
				if(data.type == 'call'){
					if( close <= exercise_price ){
						this.expectedProfits = value * ( 1 + rate / 365 * holdingDays ) 
					}else{
						this.expectedProfits = value * Number(exercise_price) *  ( 1 + rate / 365 * holdingDays ) 
					}
				}else if(data.type == 'put'){
					//如果购买的是USDT
					if( close <= exercise_price ) {
						this.expectedProfits = value / Number(exercise_price) * ( 1 + rate / 365 * holdingDays )
					}else{
						this.expectedProfits = value * ( 1 + rate / 365 * holdingDays ) 
					}
				}
				if(close <= exercise_price){
					this.expectedProfits = this.expectedProfits.toFixed(4) + data.currency_name
				}else{
					this.expectedProfits = this.expectedProfits.toFixed(4) + 'USDT'
				}
				
				
			},
			submit(){
				this.$u.throttle(async ()=>{
					const {number,id,i18n} = this
					if(!number){
						return this.$utils.showToast(i18n.error_1)
					}
					if(!this.$u.test.digits(number)){
						return this.$utils.showToast(i18n.error_2)
					}
					
					const ret = await this.$utils.showModal(this.$t("common.hint"),i18n.confirmPurchase)
					if(!ret) return
					this.$u.api.invest.purchase(id,number).then(res=>{
						this.$utils.showToast(i18n.purchaseSuccess)
						setTimeout(()=>{
							uni.navigateTo({
								url:'/pages/invest/mine'
							})
						},1200)
					})
				})
			}
		},
		computed:{
			i18n(){
				return this.$t("invest")
			},
			inputDisabled(){
				if(this.data.id && this.close) return true
				return false
			}
		}
	}
</script>

<style lang="scss" scoped>
	.purchase{
		.input{
			height: 70rpx;
			border-radius: 12rpx;
			background: none !important;
			border: 2rpx solid $uni-color-black;
			padding: 0 24rpx;
		}
	}
	.footer{
		position: fixed;
		left: 30rpx;
		right: 30rpx;
		bottom: 50rpx;
	}
</style>
