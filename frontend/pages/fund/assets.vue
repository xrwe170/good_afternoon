<template>
	<view>
		<view class="status_bar"></view>
		<view>
			<view>
				<view class="min-top">
					<view class="d-flex align-items-center justify-content-between">
						<view class="min-header d-flex align-items-center">
							<view class="d-flex align-items-center min-convert">
								<text class="font-size-28 mr-10">{{i18n.convert}}</text>
								<image v-if="iseye" src="/static/image/img/eye-fill.png" style="width: 34rpx; height: 34rpx; margin-left: 15rpx;" @click="iseye=false"></image>
								<image v-if="!iseye" src="/static/image/img/eye_slash-fill.png" style="width: 34rpx; height: 34rpx; margin-left: 15rpx;" @click="iseye=true"></image>
							</view>
						</view>
						<view class="min-record" @click="$utils.jump('/pages/fund/assets_all_record')">
							{{i18n.record}}
						</view>
					</view>
					<view class="d-flex min-money" v-if="iseye">
						{{convert}}
						<text>USDT</text>
					</view>
					<view class="d-flex min-money" v-if="!iseye">
						******
						<text>USDT</text>
					</view>
					<view class="min-fiat" v-if="iseye"> ≈ {{(convert * $store.state.fiat.rate).toFixed(4)}} {{$store.state.fiat.currency_code}} </view>
					<view class="min-fiat" v-if="!iseye"> ≈ ******* {{$store.state.fiat.currency_code}} </view>
					<view class="mr33">
						<view class="d-flex align-items-center justify-content-between">
							<view>{{i18n.currentProfit}}(USDT)</view>
							<view>{{i18n.totalkRevenue}}(USDT)</view>
							<view>{{i18n.profitRatio}}(USDT)</view>
						</view>
						<view class="d-flex align-items-center justify-content-between">
							<view><text v-if="iseye">0.00</text><text v-if="!iseye">******</text></view>
							<view><text v-if="iseye">0.00</text><text v-if="!iseye">******</text></view>
							<view><text v-if="iseye">0.00</text><text v-if="!iseye">******</text></view>
						</view>
					</view>
				</view>
			</view>
			
			<view class="m-30">
				<view class="d-flex align-items-center justify-content-between min-menu">
					<view class="text-center"  v-for="item in subNav" @click="$utils.jump(item.url)">
						<image :src="item.icon" style="width: 69rpx;height: 69rpx;margin: 42rpx 0;"></image>
						<text class="d-block" style="overflow: hidden; word-break: break-all;  text-overflow: ellipsis; display: -webkit-box; -webkit-box-orient: vertical;">{{item.name}}</text>
					</view>
				</view>
			</view>
		</view>
		<view class="min-ul">
			<u-subsection :list="nav" v-if="showNav" :current="current" @change="tabsChange" :afterWidth="60" class="min-tabs" style="flex:1" ></u-subsection>
			<view class="min-li">
				<view class="min-li-item" v-for="item in showList"
					@click="$utils.jump(`/pages/fund/assets_record?currency=${item.currency}&type_id=${currentAssetsType.id}&type_name=${currentAssetsType.name}`)">
					<view class="d-flex-between-center">
						<text class="item-name">{{item.currency_name }}</text>
					<image src="/static/image/img/gengduo.png" style="width: 50rpx; height: 50rpx;"></image>
					</view>
					<view class="d-grid-columns-3 mt-30" style="grid-column-gap:20rpx">
						<view class="mt-16">
							<text class="d-block availableQuota">{{i18n.availableQuota}}</text>
							<text class="d-block balance">{{item.balance.toFixed(4)}}</text>
						</view>
						<view class="mt-16">
							<text class="d-block availableQuota">{{i18n.locked}}</text>
							<text
								class="d-block balance">{{item.lock_balance.toFixed(4)}}</text>
						</view>
						<view class="mt-16">
							<text class="d-block availableQuota">{{i18n.converted}}(USDT)</text>
							<text class="d-block balance">
								{{item.usdt_balance}}
							</text>
						</view>
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				// 0:change,1:lever,2:legal,3:micro
				current: 0,
				convert: 0,
				showList: [],
				originalList: {},
				currentAssetsType: {},
				showNav:true,
				iseye: true,
			};
		},
		onLoad() {
			const _this = this
			uni.setNavigationBarTitle({
				title:_this.$t("nav")[4]
			})
		},
		onShow() {
			
			const i18n = this.$t("fund")
			this.nav = [{
					name: i18n.exchange
				}, {
					name: i18n.leverage
				}, {
					name: i18n.fiat
				}, {
					name: i18n.second
				}]
			
			this.showNav = false
			setTimeout(()=>{
				this.showNav = true
			},100)
				
			this.$utils.setTabbar(this)
			uni.showTabBar()
			this.getWalletList()
		},
		methods: {
			// tabs通知swiper切换
			tabsChange(index) {
				this.current = index
				this.setShowList()
			},
			getWalletList() {
				this.$u.api.wallet.getWalletList().then(res => {
					this.originalList = res.message
					const convert = Number(res.message.change_wallet.usdt_totle) + Number(res.message.lever_wallet
						.usdt_totle) + Number(res.message.legal_wallet.usdt_totle) + Number(res.message
						.micro_wallet.usdt_totle)

					this.originalList.change_wallet.balance.forEach(item => {
						item.balance = Number(item.change_balance)
						item.lock_balance = Number(item.lock_change_balance)
						item.usdt_balance = parseFloat((Number(item.change_balance) * Number(item
							.usdt_price)).toFixed(4))
					});

					this.originalList.lever_wallet.balance.forEach(item => {
						item.balance = Number(item.lever_balance)
						item.lock_balance = Number(item.lock_lever_balance)
						item.usdt_balance = parseFloat((Number(item.lever_balance) * Number(item
							.usdt_price)).toFixed(4))
					});

					this.originalList.legal_wallet.balance.forEach(item => {
						item.balance = Number(item.legal_balance)
						item.lock_balance = Number(item.lock_legal_balance)
						item.usdt_balance = parseFloat((Number(item.legal_balance) * Number(item
							.usdt_price)).toFixed(4))
					});

					this.originalList.micro_wallet.balance.forEach(item => {
						item.balance = Number(item.micro_balance)
						item.lock_balance = Number(item.lock_micro_balance)
						item.usdt_balance = parseFloat((Number(item.micro_balance) * Number(item
							.usdt_price)).toFixed(4))
					});

					this.convert = convert.toFixed(4)
					this.setShowList()
				})
			},
			setShowList() {
				switch (this.current) {
					case 0:
						this.showList = this.originalList.change_wallet.balance;
						this.currentAssetsType = this.$store.state.assetsType[1];
						break;
					case 1:
						this.showList = this.originalList.lever_wallet.balance;
						this.currentAssetsType = this.$store.state.assetsType[2];
						break;
					case 2:
						this.showList = this.originalList.legal_wallet.balance;
						this.currentAssetsType = this.$store.state.assetsType[0];
						break;
					case 3:
						this.showList = this.originalList.micro_wallet.balance;
						this.currentAssetsType = this.$store.state.assetsType[3];
						break;
				}
			}
		},
		computed: {
			i18n() {
				return this.$t("fund")
			},
			subNav(){
				const i18n = this.$t("fund")
				return [{
						name:i18n.receive,
						icon: require('static/image/img/icon/home-nav-1.png'),
						url: '/pages/fund/select?url=receive'
					},{
						name:i18n.withdraw,
						icon: require('static/image/img/icon/home-nav-2.png'),
						url: '/pages/fund/withdraw'
					},
					{
						name:i18n.transfer,
						icon: require('static/image/img/icon/home-nav-3.png'),
						url: '/pages/fund/transfer'
					},
				]
			}
		}
	}
</script>

<style lang="scss" scoped>
	.min-top{
		background: #2e5cd1;
		.min-header {
		    height: 225rpx;
			font-size: 38rpx;
			font-family: Roboto;
			font-weight: 500;
			color: #fff;
			// padding: 0px 0 34rpx 0;
			margin-left: 34rpx;
		}
		.min-record {
		    width: 174rpx;
		    height: 58rpx;
		    line-height: 58rpx;
		    text-align: center;
		    background: #fff;
		    border-radius: 50rpx 0px 0 50rpx;
		    font-size: 24rpx;
		    font-family: Roboto;
		    font-weight: 500;
		    color: #2e5cd1;
		}
		.min-money {
		    font-size: 58rpx;
		    font-family: Roboto;
		    font-weight: 400;
		    color: #fff;
		    align-items: baseline;
		    margin-left: 34rpx;
		    margin-bottom: 15rpx;
			&> uni-text {
			    font-size: 24rpx;
			    font-family: Roboto;
			    font-weight: 400;
			    color: #fff;
			    margin-left: 15rpx;
			}
		}
		.min-fiat {
		    font-size: 24rpx;
		    font-family: Roboto;
		    font-weight: 400;
		    color: #fff;
		    margin-left: 34rpx;
		    padding-bottom: 34rpx;
		}
		.mr33 {
		    margin: 0 34rpx;
		    font-size: 24rpx;
		    font-family: Roboto;
		    font-weight: 500;
		    color: #fff;
		    line-height: 34rpx;
			& > uni-view:nth-child(2) {
			    font-size: 42rpx;
			    font-weight: 400;
			    padding: 34rpx 0;
			}
		}
	}
	.min-menu {
	    padding: 34rpx 0 50rpx 0;
		& > uni-view {
		    width: 204rpx;
		    background: #fff;
		    box-shadow: 0px 11px 36px -4px rgba(24, 39, 75, .1), 0px 7px 16px -6px rgba(24, 39, 75, .12);
		    border-radius: 32rpx;
			& > uni-text {
			    font-size: 24rpx;
			    font-family: Roboto;
			    font-weight: 700;
			    color: #333;
			    margin-bottom: 20rpx;
			}
		}
	}
	.min-ul{
		.min-tabs{
			margin: 0 34rpx;
		}
		.min-li {
		    margin: 0 34rpx;
		    padding-bottom: 34rpx;
			.min-li-item{
				margin-top: 34rpx;
				background: #fff;
				box-shadow: 0px 11px 36px -4px rgba(24, 39, 75, .1), 0px 7px 16px -6px rgba(24, 39, 75, .12);
				border-radius: 32rpx;
				padding: 34rpx;
				.item-name {
				    font-size: 38rpx;
				    font-family: Roboto;
				    font-weight: 500;
				    color: #333;
				}
				.availableQuota {
					font-size: 24rpx;
					font-family: Roboto;
					font-weight: 500;
					color: #9e9e9e;
				}
				.balance {
					font-size: 34rpx;
					font-family: Roboto;
					font-weight: 400;
					color: #2e5cd1;
					margin-top: 15rpx;
				}
			}
		}
	}
	
	.nav {
		background: url(../../static/image/icon/nav-shadow.png), #ff0000;
		background-size: auto 100%;
		background-position: 606rpx;
	}

	.dealer {
		position: absolute;
		top: 26rpx;
		right: 0;
		color: #FBE6CC;
		border-radius: 31rpx 0 0 31rpx;
		background-image: linear-gradient(to right, #574625, #6B552D);
		padding: 14rpx 44rpx;
		display: flex;
		align-items: center;
	}
</style>
