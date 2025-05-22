<template>
	<view>
		<u-navbar :title="i18n.addWallet" :borderBottom="false"></u-navbar>
		<view class="mx-30">
			<view class="mt-36 input-item" >
				<text class="d-block font-size-28">{{i18n.selectCurrency}}</text>
				<input type="text" class="input"  @click="showSelectCurrency=true" :placeholder="i18n.plsSelectCurrency" v-model="currency.name">
			</view>
			<view class="mt-36 input-item" >
				<text class="d-block font-size-28">{{i18n.walletAddress}}</text>
				<input type="text" class="input"  :placeholder="i18n.plsIptAddress" v-model="address">
			</view>
			<view class="mt-36 input-item" >
				<text class="d-block font-size-28">{{i18n.walletQrcode}}</text>
				<view class="text-center">
					<view class="upload-wrap mt-30 ml-0" @click="uploadImage">
						<u-icon name="plus" size="40" color="#dddddd" v-if="!qrcode"></u-icon>
						<image class="w-100 h-100" :src="qrcode | retImageUrl" mode="aspectFill" v-else></image>
					</view>
				</view>
			</view>
			<button class="sub-btn-bg mt-50" @click="submit">{{$t("common.submit")}}</button>
		</view>
		
		<!-- 弹出币种选择 -->
		<u-action-sheet :list="currencyList" v-model="showSelectCurrency" @click="confirmCurrency"></u-action-sheet>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				currency:{},
				currencyList:[],
				showSelectCurrency:false,
				address:'',
				qrcode:''
			};
		},
		onShow() {
			this.getCurrencyList()
		},
		methods:{
			getCurrencyList(){
				this.$u.api.setting.getCurrencyList().then(res=>{
					this.currencyList = res.message.legal.map(el=>{
						el.text = el.name
						return el
					})
				})
			},
			confirmCurrency(index){
				this.currency = this.currencyList[index]
			},
			uploadImage(){
				this.$utils.uploadImage().then(res=>{
					this.qrcode = res
				})
			},
			submit(){
				this.$u.throttle(()=>{
					const {qrcode,currency,address,i18n} = this
					if(!currency.name || !currency.id){
						this.$utils.showToast(i18n.plsSelectCurrency)
						return false
					}
					
					if(!address){
						this.$utils.showToast(i18n.plsIptWalletAddress)
						return false
					}
					
					if(this.$utils.charTest(address)){
						this.$utils.showToast(this.$t("common.specialChart"))
						return false
					}
					
					if(!qrcode){
						this.$utils.showToast(i18n.plsUploadWalletQrcode)
						return false
					}
					
					this.$u.api.setting.saveWalletAddress(currency.id,currency.name,address,qrcode).then(res=>{
						this.$utils.showToast(res.message)
						setTimeout(()=>{
							uni.navigateBack({
								delta:1
							})
						},1200)
					})
					
					
				},1200)
			}
		},
		computed:{
			i18n(){
				return this.$t("setting")
			}
		}
	}
</script>

<style lang="scss" scoped>
.input{
	background-color: #ffffff;
	border: 1px solid #dddddd;
}
.sub-btn-bg{
	background: #2e5cd1;
	box-shadow: 0px 11px 36px -4px rgba(24, 39, 75, .1), 0px 7px 16px -6px rgba(24, 39, 75, .12);
	border-radius: 18rpx;
	color: #fff;
}
</style>
