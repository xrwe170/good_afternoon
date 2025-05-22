<template>
	<view class="text-white">
		<u-navbar :title="$t('setting.editPayPassword')"></u-navbar>
		<view class="mx-30">
			<view class="mt-36 input-item" >
				<text class="d-block font-weight-bold font-size-28">{{i18n.email}}</text>
				<input type="text" class="input" disabled :value="$store.state.user.email">
			</view>
			<view class="mt-36 input-item" >
				<text class="d-block font-weight-bold font-size-28">{{i18n.emailVerificationCode}}</text>
				<input type="digit" class="input" v-model="code" maxlength="6" :placeholder="i18n.plsiptEmailCode">
				<view class="send-code-button" :class="hasSend ? 'send' : ''" @click="getCode">
					<text class="d-block h-100 w-100" v-if="!hasSend">{{i18n.getVerificationCode}}</text>
					<text class="d-block h-100 w-100" v-else>{{seconds}}s</text>
				</view>
			</view>
			<view class="mt-36 input-item" v-for="(item,index) in passwordItem" :key="item.name">
				<text class="d-block font-weight-bold font-size-28">{{item.title}}</text>
				<input type="password" class="input" maxlength="6" v-if="!item.showText" v-model="item.value" :placeholder="item.placeholder">
				<input type="text" class="input" v-else maxlength="6" v-model="item.value" :placeholder="item.placeholder">
				<text class="iconfont font-size-40  eye" :class="item.showText ? 'icon-yanjing_yincang' : 'icon-yanjing_xianshi'" @click="changeShowText(index)" ></text>
			</view>
			<button class="warning-button mt-40" @click="submit">{{$t("common.confirm")}}</button>
		</view>
		
	</view>
</template>

<script>
	export default {
		data() {
			return {
				passwordItem:null,
				hasSend:false,
				seconds:120,
				secondsInterval:null,
				code:''
			};
		},
		onShow() {
			const i18n = this.$t("setting")
			const passwordItem = [
				{
					name:'new_password',
					title:i18n.newPayPassword,
					placeholder:i18n.newPayPasswordPlaceholder,
					value:'',
					showText:false
				},
				{
					name:'new_password_confirm',
					title:i18n.confirmNewPayPassword,
					placeholder:i18n.confirmNewPayPasswordPlaceholder,
					value:'',
					showText:false
				}
			]
			this.passwordItem = passwordItem
		},
		methods:{
			changeShowText(index){
				let passwordItem = this.$u.deepClone(this.passwordItem)
				const showText = passwordItem[index].showText
				passwordItem[index].showText = !showText
				this.passwordItem = passwordItem
			},
			//发送验证码
			getCode(){
				// 设置节流,防止频繁点击
				this.$u.throttle(()=>{
					const {hasSend,i18n} = this
					
					if(hasSend) return

					//发送接口
					this.$u.api.setting.sendEmailCode(this.$store.state.user.email).then(res => {
						this.$utils.showToast(res.message)
						//倒计时
						this.hasSend = true
						this.secondsInterval = setInterval(() => {
							this.seconds = this.seconds - 1
							if (this.seconds == 0) {
								clearInterval(this.secondsInterval)
								this.hasSend = false
								this.seconds = 120
							}
						}, 1000)
					})
				},1000)
			},
			async submit(){
				const {code,passwordItem} = this
				const password = passwordItem[0].value
				const repassword = passwordItem[1].value
				const user_string = this.$store.state.user.account_number
				const i18n = this.$t("common")
				//判断验证码
				if(code.length < 6){
					this.$utils.showToast(i18n.plsInputCode)
					return false
				}
				//判断密码
				if(password.length < 6){
					this.$utils.showToast(i18n.pwdMoreThen6)
					return false
				}
				if(this.$utils.charTest(password)){
					this.$utils.showToast(i18n.specialChart)
					return false
				}
				//判断确认密码
				if(repassword.length < 6){
					this.$utils.showToast(i18n.rePwdMoreThen6)
					return false
				}
				if(this.$utils.charTest(repassword)){
					this.$utils.showToast(i18n.specialChart)
					return false
				}
				if(password != repassword){
					this.$utils.showToast(i18n.pwdInconsistent)
					return false
				}
				//先验证验证码
				try{
					const res = await this.$u.api.setting.verifyMailCode(user_string,code)
					this.$u.api.setting.editPayPassword(password,repassword,code).then(res=>{
						this.$utils.showToast(res.message)
						setTimeout(()=>{
							uni.navigateBack({
								delta:1
							})
						},1200)
					})
					
				}catch(e){
					//TODO handle the exception
				}
			}
		},
		computed:{
			i18n(){
				return this.$t("setting")
			},
		}
	}
</script>

<style lang="scss" scoped>

.button-base{
	height: 76rpx;
	line-height: 76rpx;
}

.input-item{
	.send-code-button{
		position: absolute;
		@extend .font-size-22;
		right: 30rpx;
		top: 78rpx;
		height: 50rpx;
		line-height: 50rpx;
		padding: 0 20rpx;
		background-color: $uni-color-error;
		border-radius: 10rpx;
		color: #fff;
		&.send{
			background-color: $uni-color-secondary;
		}
	}
}
.input{
	background-color: $uni-color-black;
}
</style>
