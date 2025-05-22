<template>
	<view>
		<u-navbar :title="`${i18n.secondsPosition}(${symbol})`" :custom-back="()=>{$utils.jump('/pages/transaction/index','switchTab')}"></u-navbar>
		<view class="p-30">
			<u-subsection :list="nav" :current="0" @change="changeNav"></u-subsection>
			<view class="mt-30">
				<view class="p-20 box-shadow border-radius-20 mb-20 item" v-for="item in list">
					<view class="d-flex-between-center py-10 ">
						<text class="opacity-50">{{i18n.tradingPair}}</text>
						<text class="font-weight-bold">{{item.symbol_name}}</text>
					</view>
					<view class="d-flex-between-center py-10">
						<text class="opacity-50">{{i18n.direction}}</text>
						<text class="font-weight-bold"
							:class="item.type == 1 ? 'text-success' : 'text-error'">{{item.type == 1 ? i18n.buyUp : i18n.buyDown}}</text>
					</view>
					<view class="d-flex-between-center py-10">
						<text class="opacity-50">{{i18n.buyPrice}}</text>
						<text class="font-weight-bold">{{Number(item.open_price).toFixed(4)}}</text>
					</view>
					<view class="d-flex-between-center py-10">
						<text class="opacity-50">{{i18n.number}}</text>
						<text class="font-weight-bold">{{Number(item.number)}}</text>
					</view>
					<view class="d-flex-between-center py-10">
						<text class="opacity-50">{{i18n.orderTimes}}</text>
						<text class="font-weight-bold">{{Number(item.seconds)}}s</text>
					</view>
					<view class="d-flex-between-center py-10" v-if="currentNav == 1">
						<text class="opacity-50">{{i18n.sellTime}}</text>
						<text class="font-weight-bold">{{item.handled_at}}</text>
					</view>
					<view class="d-flex-between-center py-10" v-if="currentNav == 1">
						<text class="opacity-50">{{i18n.pl}}</text>
						<text class="font-weight-bold font-size-30"
							:style="{color:$utils.getColor(item.fact_profits)}">{{Number(item.fact_profits)}}</text>
					</view>
				</view>
				<default-page  :length="list.length" :total="total"></default-page>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				match_id: 2,
				page: 1,
				canGet: true,
				list: [],
				symbol: '',
				currentNav: 0,
				showNav:true,
				total:0
			};
		},
		onLoad(options) {
			const {
				match_id
			} = options
			this.match_id = match_id || 2
		},
		onShow() {
			this.showNav = false
			setTimeout(()=>{
				this.showNav = true
			},100)
			
			this.page = 1
			this.canGet = true
			this.list = []
			this.getSecondsList()
		},
		methods: {
			getSecondsList() {
				if (!this.canGet) return
				const {
					match_id,
					status,
					page
				} = this
				this.$u.api.market.getSecondsList(match_id, status, page).then(res => {
					this.total = res.message.total
					const list = res.message.data
					if (list.length) {
						this.symbol = list[0].symbol_name
						this.list = [...this.list, ...list]
						this.page++
					} else {
						this.canGet = false
					}
				})
			},
			changeNav(val) {
				this.currentNav = val
				this.page = 1
				this.canGet = true
				this.list = []
				this.getSecondsList()

			}
		},
		computed: {
			i18n() {
				return this.$t("transaction")
			},

			status() {
				// 1:在持，3：历史
				return this.currentNav == 0 ? 1 : 3
			},
			nav() {
				const i18n = this.$t("transaction")
				return [{
					name: i18n.position
				}, {
					name: i18n.history
				}]
			}
		},
		onReachBottom() {
			this.getSecondsList()
		}
	}
</script>

<style lang="scss">
.item{
	border: 2rpx solid #dddddd;
}
</style>
