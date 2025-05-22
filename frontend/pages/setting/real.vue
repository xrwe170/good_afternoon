<template>
	<view>
		<u-navbar :title="i18n.authentication"></u-navbar>
		<!-- 初级认证 -->
		<view class="p-30 m-30 box-shadow border-radius-20" v-if="type == 1">
			<!-- 未认证 -->
			<view class="d-flex-between-center">
				<text class="d-block font-weight-bold font-size-32">{{i18n.authentication1}}</text>
				<u-tag size="mini" mode="dark" :type="real.review_status == '1' ? 'warning' : 'success'" v-if="real.review_status" :text="real.review_status == '1' ? i18n.auditing : i18n.hasaudit"></u-tag>
			</view>
			<view class="mt-20">
				<text class="d-block font-size-28">{{i18n.idType}}</text>
				<view class="login-input-group mt-2">
					<picker :value="form1.id_type" :range="types" class="login-input" @change="form1.id_type=$event.detail.value" :disabled="form1.disabled">
						<view class="uni-input">{{types[form1.id_type]}}</view>
					</picker>
				</view>
			</view>
			<view class="mt-20">
				<text class="d-block font-size-28">{{i18n.name}}</text>
				<view class="login-input-group mt-2">
					<input type="text" v-model="form1.name" class="login-input" :disabled="form1.disabled">
				</view>
			</view>
			<view class="mt-30">
				<text class="d-block font-size-28">{{i18n.idcard}}</text>
				<view class="login-input-group mt-2">
					<input type="text" v-model="form1.card_id" class="login-input" :disabled="form1.disabled">
				</view>
			</view>
			<button class="warning-button mt-50 py-0" @click="submit1" v-if="!form1.disabled">{{$t("common.confirm")}}</button>
		</view>
		<!-- 高级认证 -->
		<view class="p-30 m-30 box-shadow border-radius-20" v-else-if="type == 2">
			<!-- 未认证 -->
			<view class="d-flex-between-center">
				<text class="d-block font-weight-bold font-size-32">{{i18n.authentication2}}</text>
				<u-tag size="mini" mode="dark" :type="real.advanced_review_status == '1' ? 'warning' : 'success'" v-if="real.advanced_review_status" :text="real.advanced_review_status == '1' ? i18n.auditing : i18n.hasaudit"></u-tag>
			</view>
			<view class="text-center">
				<view class="d-flex justify-content-around mt-30">
					<view class="">
						<view class="upload-wrap" @click="uploadImage('front')">
							<u-icon name="plus" size="40" v-if="!form2.front_pic"></u-icon>
							<image class="w-100 h-100" :src="form2.front_pic | retImageUrl" mode="aspectFill" v-else></image>
						</view>
						<text class="d-block mt-20">{{i18n.uploadIdcardFront}}</text>
					</view>
					<view class="">
						<view class="upload-wrap" @click="uploadImage('reverse')">
							<u-icon name="plus" size="40" v-if="!form2.reverse_pic"></u-icon>
							<image class="w-100 h-100" :src="form2.reverse_pic | retImageUrl" mode="aspectFill" v-else></image>
						</view>
						<text class="d-block mt-20">{{i18n.uploadIdcardReverse}}</text>
					</view>
				</view>
			</view>
			<view class="d-flex text-center mt-40 font-weight-bold">
				{{i18n.idType}}: {{types[form1.id_type]}}
			</view>
			<button class="warning-button mt-50 py-0" @click="submit2" v-if="!form2.disabled">{{$t("common.confirm")}}</button>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				type:'1',
				real:{},
				types: [],
				form1:{
					id_type: 0,
					name:'',
					card_id:'',
					disabled:false
				},
				form2:{
					front_pic:'',
					reverse_pic:"",
					disabled:false
				}
			};
		},
		onLoad(options) {
			const {type} = options
			this.type = type || '1'
			this.types = [
				this.i18n.idCard,
				this.i18n.passport,
				this.i18n.driverLicense
			]
		},
		onShow() {
			//检查认证状态
			this.getUserRealState()
		},
		methods:{
			getUserRealState(){
				this.$u.api.setting.getUserRealState().then(res=>{
					this.real = res.message
					//real_status:初级认证，advanced_review_status：高级认证
					//判断如果高级认证state为0，则调至初级认证
					if(res.message.advanced_review_status == 0 && this.type == 2 && res.message.review_status != 2){
						this.type = 1
					}
					if(res.message.real_data){
						const data = res.message.real_data
						if(data.name && data.card_id){
							this.form1 = {
								id_type: data.id_type,
								name:data.name,
								card_id:data.card_id,
								disabled:true
							}
						}
						if(data.front_pic && data.reverse_pic){
							this.form2 = {
								front_pic:data.front_pic,
								reverse_pic:data.reverse_pic,
								disabled:true
							}
						}
					}
				})
			},
			//提交初级认证
			submit1(){
				this.$u.throttle(()=>{
					const {form1,i18n} = this
					if(!form1.name){
						this.$utils.showToast(i18n.plsIptName)
						return false
					}
					if(this.$utils.charTest(form1.name)){
						this.$utils.showToast(this.$t("common.specialChart"))
						return false
					}
					if(!form1.card_id || form1.card_id.length < 6){
						this.$utils.showToast(i18n.plsIptCorrectIdcard)
						return false
					}
					
					this.$u.api.setting.postRealState1(form1.id_type, form1.name,form1.card_id).then(res=>{
						this.$utils.showToast(res.message)
						setTimeout(()=>{
							uni.navigateBack({
								delta:1
							})
						},1200)
					})
					
				},1200)
			},
			//上传图片
			uploadImage(type){
				if(this.form2.disabled) return
				this.$utils.uploadImage().then(res=>{
					if(type == 'front'){
						this.form2.front_pic = res
					}else if(type == 'reverse'){
						this.form2.reverse_pic = res
					}
				})
			},
			submit2(){
				this.$u.throttle(()=>{
					const {form2,i18n} = this
					if(!form2.front_pic){
						this.$utils.showToast(this.$t("common.pls") + i18n.uploadIdcardFront)
						return false
					}
					if(!form2.reverse_pic){
						this.$utils.showToast(this.$t("common.pls") + i18n.uploadIdcardReverse)
						return false
					}
					this.$u.api.setting.postRealState2(form2.front_pic,form2.reverse_pic).then(res=>{
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
	.login-input{
		margin-top: 18rpx;
		border: 1px solid #999;
		padding-left: 18rpx;
	}
	.upload-wrap{
		background: #ffffff;
	}
</style>
