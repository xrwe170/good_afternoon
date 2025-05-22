<template>
	<view class="pb-50">
		<u-navbar :title="'IEO'+i18n.subscribe">
			<u-icon name="order" color="#fff" @click="$utils.jump('/pages/ieo/order')" size="40" slot="right"></u-icon>
		</u-navbar>
		<view class="m-30">
			<view class="p-30 box-shadow border-radius-20 mb-20 bg-black text-white" v-for="item in list">
				<text class="d-block font-size-30">{{item.title}}</text>
				<view class="d-flex-between-center mt-20">
					<view class="d-flex align-items-baseline">
						<text class="opacity-50 font-size-24">{{i18n.lockPeriod}}:</text>
						<text class="font-weight-bold font-weight-bold ml-12">{{item.day + i18n.day}}</text>
					</view>
					<text class="tag tag-success" v-if="item.time_status == 2">{{i18n.ing}}</text>
					<text class="tag tag-error" v-else-if="item.time_status == 3">{{i18n.done}}</text>
				</view>
				<view class="d-grid-columns-2 mt-20">
					<view class="d-flex align-items-baseline">
						<text class="opacity-50 font-size-24">{{i18n.subscribed}}({{item.currency_name}}):</text>
						<text class="font-weight-bold font-weight-bold ml-12">{{parseFloat(item.total_sell)}}</text>
					</view>
					<view class="d-flex align-items-baseline">
						<text class="opacity-50 font-size-24">{{i18n.total}}({{item.currency_name}}):</text>
						<text class="font-weight-bold font-weight-bold ml-12">{{parseFloat(item.amount)}}</text>
					</view>
				</view>
				<view class="d-grid align-items-baseline" style="grid-template-columns:2fr 1fr">
					<u-line-progress  :show-percent="false" :percent="Number(item.percentage)" class="mt-20" v-if="item.percentage > 0"></u-line-progress>
					<u-line-progress  :show-percent="false" :percent="0" class="mt-20" v-else></u-line-progress>
					<view class="d-flex align-items-baseline justify-content-end">
						<text class="opacity-50 font-size-24">{{i18n.remaining}}:</text>
						<text class="font-weight-bold font-weight-bold ml-12">{{parseFloat(item.percentage)}}%</text>
					</view>
				</view>
				<button class="warning-button mt-20 font-size-24 py-0" v-if="item.time_status == 2" @click="$utils.jump('/pages/ieo/subscribe?project_id=' + item.id)">{{i18n.applySubscription}}</button>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				page:1,
				canGet:true,
				limit:10,
				list:[],
			};
		},
		onShow() {
			this.page = 1
			this.canGet = true
			this.list = []
			this.getIEOProject()
		},
		methods:{
			getIEOProject(){
				if(!this.canGet) return
				const {page,limit} = this
				this.$u.api.ieo.getIEOProject(page,limit).then(res=>{
					const list = res.message.list
					if(list.length){
						this.list = [...this.list,...list]
						this.page++
					}else{
						this.canGet = false
					}
				})
			},
		},
		computed:{
			i18n(){
				return this.$t("ieo")
			}
		},
		onReachBottom() {
			this.getIEOProject()
		}
	}
</script>

<style lang="scss">

</style>
