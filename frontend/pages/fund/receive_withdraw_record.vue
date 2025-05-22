<template>
	<view>
		<u-navbar :title="$t('common.log')" :borderBottom="false"></u-navbar>
		<view class="mx-36">
			<view class=" my-30 p-20 border-radius-20 rec-list" v-for="item in data" :key="item.id">
				<view class="d-flex justify-content-between align-items-center">
					<view class="d-flex align-items-center">
						<text class="font-size-32">{{item.name}}</text>
					</view>
					<text class="font-size-32" :style="{'color':$utils.getColor(item.type)}">{{item.amount}}</text>
				</view>
				<view class="text-right font-size-22 my-4">
					{{item.name}}
				</view>
				<view class="d-flex justify-content-between mt-10">
					<text :style="{'color':$utils.getColor(item.type)}">{{item.type == -1 ? $t("home.withdraw") : $t("home.recharge")}}</text>
					<text>{{item.created_at}}</text>
				</view>
				<view class="d-flex justify-content-between mt-10">
					<text>{{$t("transaction.status")}}</text>
					<text v-if="item.status == 1">{{$t("common.underAudit")}}</text>
					<text v-if="item.status == 2" style="color: rgb(40, 186, 152);">{{$t("common.success")}}</text>
					<text v-if="item.status == 3" style="color: red;">{{$t("common.auditFailure")}}</text>
				</view>
			</view>
			<default-page v-if="!data.length"></default-page>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				data: [],
				page: 1,
				limit: 99999999,
				canGet: true,
				withdrawList: []
			};
		},
		onShow() {
			this.data = []
			this.getList()
		},
		methods: {
			async getList() {
				let arr1   = [];
				let arr2   = [];
				let type   = this.$route.query.type;
				if(type == 1){
					const ret1 = await this.$u.api.wallet.getRechargeLog(this.page, this.limit)
					arr1 = ret1.message.data.map(item => {
						item.type = 1
						return item
					})
				
					
				}else if(type == 2){
				
					const ret2 = await this.$u.api.wallet.getWithdrawList(this.page, this.limit)
					arr2 = ret2.message.data.map(item => {
						item.type = -1
						item.created_at = item.create_time
						item.amount = item.number
						return item
					})
					
				}
				
				
				let arr = [...arr1,...arr2]
				this.data = arr.sort((a,b)=>a.created_at - b.created_at)
			},
			// getRechargeLog() {
			// 	const coins = this.$store.state.coins
			// 	if (!this.canGet) return
			// 	this.$u.api.wallet.getRechargeLog(this.page, this.limit).then(res => {
			// 		const data = res.message
			// 		if (data.total == this.data.length) {
			// 			this.canGet = false
			// 		} else {
			// 			let list = data.data.map(item => {
			// 				const obj = coins.find(el => item.currency_id == el.id)
			// 				item.amount = Number(item.amount).toFixed(2)
			// 				item.currency_image = obj.image,
			// 					item.type = 1
			// 				return item
			// 			})

			// 			this.data = this.data.concat(list)
			// 		}
			// 	})
			// },
			// getWithdrawList() {
			// 	const coins = this.$store.state.coins
			// 	if (!this.canGet) return
			// 	this.$u.api.wallet.getWithdrawList(this.page, this.limit).then(res => {
			// 		const data = res.message
			// 		if (data.total == this.data.length) {
			// 			this.canGet = false
			// 		} else {
			// 			let list = data.data.map(item => {
			// 				const obj = coins.find(el => item.currency_id == el.id)
			// 				item.amount = Number(item.amount).toFixed(2)
			// 				item.currency_image = obj.image,
			// 					item.type = 0
			// 				return item
			// 			})

			// 			this.withdrawList = this.withdrawList.concat(list)
			// 		}
			// 	})
			// },
		},
		onReachBottom() {
			//this.getRechargeLog()
		},
		filters: {

		}
	}
</script>

<style lang="scss" scoped>
	.rec-list{
		box-shadow: 0px 11px 36px -4px rgba(24, 39, 75, .1), 0px 7px 16px -6px rgba(24, 39, 75, .12);
		border-radius: 18rpx;
	}
	.type {
		background-color: rgba(33, 193, 146, .2);
		border-radius: 18rpx;
		color: #028a62;
		font-size: 20rpx;
		height: 36rpx;
		line-height: 36rpx;
		padding: 0 24rpx;
	}
</style>
