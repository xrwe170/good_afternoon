<template>
	<view class="pb-50">
		<u-navbar :title="i18n.financialRecords" :borderBottom="false"></u-navbar>
		<view class="m-30">
			<view class="mt-30">
				<block v-if="list.length">
					<view class="p-30 box-shadow border-radius-20 mb-30" v-for="item in list">
						<view class="d-flex-between-center py-10">
							<text class="opacity-50">{{i18n.number}}</text>
							<text class="font-weight-bold font-size-32" :style="{color:$utils.getColor(item.value)}">{{Number(item.value) + " " +item.currency_name}}</text>
						</view>
						<view class="d-flex-between-center py-10">
							<text class="opacity-50">{{i18n.time}}</text>
							<text class="font-weight-bold">{{item.created_time}}</text>
						</view>
						<view class="py-10 font-size-30">
							{{item.info}}
						</view>
					</view>
					
				</block>
				<default-page v-else></default-page>
			</view>
		</view>
		
	</view>
</template>

<script>
	export default {
		data() {
			return {
				currency:0,
				type_id:0,
				type_name:'',
				balance:{},
				page:1,
				list:[],
				canGet:true,
				assetsType:{}
			};
		},
		onShow() {
			// 获取特定币种特定方式的帐变记录
			this.getLegalLog()
		},
		methods:{
			
			getLegalLog(){
				if(!this.canGet) return
				const {currency,type_id:type,page} = this
				this.$u.api.wallet.getLegalLog(currency,type,page).then(res=>{
					const list = res.message.list
					if(list.length){
						this.list = [...this.list,...list]
						this.page++
					}else{
						this.canGet = false
					}
				})
			}
		},
		computed:{
			i18n(){
				return this.$t("fund")
			}
		},
		onReachBottom() {
			this.getLegalLog()
		}
	}
</script>

<style lang="scss">
.box-shadow {
    box-shadow: 0 0 10px 0 rgba(0, 0, 0, .1);
}
</style>
