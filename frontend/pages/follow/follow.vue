<template>
	<view class="text-white">
		<u-navbar :title="i18n.editCopy">
			<u-icon name="trash" style="margin-right: 30rpx;color: #606266;" size="40" slot="right" @click="cancelCopytrade" v-if="showCancel"></u-icon>
		</u-navbar>
		<view class="px-30">
			<view class="py-30 u-border-bottom">
				<text>{{i18n.copyType}}</text>
				<view class="d-flex mt-20">
					<u-button class="mx-0 mr-16 px-40"  :type="activeCopyType == item.id ? 'warning' : ''" v-for="item in copyType" size="medium" @click="activeCopyType = item.id">{{item.name}}</u-button>
				</view>
				<view class="mt-20 opacity-75 font-size-24">
					{{i18n.copyAlert1}}
				</view>
				<view class="mt-20 opacity-75 font-size-24" v-if="activeCopyType != 2">
					{{i18n.copyAlert2}}
				</view>
			</view>
			<view class="d-flex-between-center py-30 u-border-bottom">
				<text class="" v-if="activeCopyType == 1">{{i18n.orderfollow_multiple}}</text>
				<text class="" v-else-if="activeCopyType == 2">{{i18n.orderfollow_hand}}</text>
				<u-number-box v-model="number" :min="1" :disabled-input="true"></u-number-box>
			</view>
			
			<view class="mt-30">
				<button class="warning-button py-0 font-size-28 mb-20" @click="submit">{{$t("common.confirm")}}</button>
				<text class="iconfont icon-checkbox-full font-size-24" v-if="!hasRead" style="opacity: .1;"
					@click="hasRead = !hasRead"></text>
				<text class="iconfont icon-checkbox-ok-full text-warning font-size-24" v-else
					@click="hasRead = !hasRead"></text>
				<text class="ml-8 ">
					{{i18n.beforeCopy}}
					<text class="text-warning" @click="jump">《{{i18n.copyAgreement}}》</text>
				</text>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				activeCopyType:1,
				number:1,
				hasRead:false,
				showCancel:false,
				follow_user_id:0
			};
		},
		onLoad(options) {
			const {uid} = options
			if(!uid){
				this.$utils.showToast(this.$t("common.paramsWrong"))
				setTimeout(()=>{
					uni.navigateBack({
						delta:1
					})
				},1200)
				return
			}
			this.uid = uid
		},
		onShow() {
			//this.getCopytradeOrder()
		},
		methods:{
			getCopytradeOrder(){
				this.$u.api.copytrade.getCopytradeOrder(this.uid).then(res=>{
					
				})
			},
			submit(){
				this.$u.throttle(()=>{
					const {activeCopyType:type,number:numerical,uid,hasRead,i18n} = this
					if(!hasRead){
						this.$utils.showToast(`${i18n.beforeCopy}《${i18n.copyAgreement}》`)
						return
					}
					this.$u.api.follow.follow(uid,numerical,type).then(res=>{
						this.$utils.showToast(res.message)
						setTimeout(()=>{
							uni.navigateBack({
								delta:1
							})
						},1200)
					})
				},1000)
			},
			async cancelCopytrade(){
				const res = await this.$utils.showModal(this.$t("common.hint"),this.$t("copytrade.confirmCancelFollow"))
				if(!res) return
				this.$u.api.copytrade.cancelFollow(this.uid).then(res=>{
					this.$utils.showToast(res.message)
					this.showCancel = false	
				})
			},
			jump(){
				const lang = this.$store.state.lang
				const url = '/pages/follow/agreement_' + lang
				uni.navigateTo({
					url
				})
			}
		},
		computed:{
			i18n(){
				return this.$t("follow")
			},
			copyType(){
				const i18n = this.$t("follow")
				return [{
					id:1,
					name:i18n.copyType1
				},{
					id:2,
					name:i18n.copyType2
				}]
			}
		}
	}
</script>

<style lang="scss" scoped>
.px-40{
	padding-left: 40rpx !important;
	padding-right: 40rpx !important;
}
</style>
