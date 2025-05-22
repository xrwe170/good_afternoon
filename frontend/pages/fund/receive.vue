<template>
	<view>
		<u-navbar :title="i18n.receive" :borderBottom="false">
			<navigator url="/pages/fund/receive_withdraw_record?type=1" slot="right">
				<u-icon name="order" size="38"></u-icon>
			</navigator>
		</u-navbar>
		<view class="mx-36 py-20">
			<navigator open-type="redirect" url="/pages/fund/select?url=receive"
				class="d-flex align-items-center justify-content-between curr-cz">
				<view>
					<view class="d-flex align-items-center">
						<image :src="'/static/image/icon/icon-' + selectCoin.name + '.png'"
							style="width: 50rpx; height: 50rpx;"></image>
						<text class="curr-name">{{selectCoin.name}}</text>
					</view>
				</view>
				<view>
					<view class="d-flex align-items-center">
						<text class="curr-type">{{selectCoin.type}}</text>
						<image src="/static/image/img/gengduo.png" style="width: 50rpx; height: 50rpx;"></image>
					</view>
				</view>
			</navigator>

			<!-- <view class="receive-alert mt-20">
				为充分保障您的隐私，建议您经常更换收款地址一个收款地址建议使用不超过5次
			</view> -->

			<!-- <view class="d-flex justify-content-between mt-22" v-if="rechargeChannel">
				<view class="d-flex slide-wrap" style="width: 400rpx;">
					<view class="item" v-for="(item,index) in rechargeChannel" :key="item.name"
						:class="{active:activeAddressList == index}" @click="activeAddressList=index">
						{{item.name}}
					</view>
				</view>
				<view class="slide-wrap px-32" style="width:158rpx;" @click="showAddNewAddressFunc">
					+地址
				</view>
			</view> -->

			<!-- <template>
				<view class="mt-22 list-item" v-for="item in address">
					<view class="d-flex align-items-center justify-content-between">
						<view class="d-flex">
							<text class="name">{{item.name | substring(0,10)}}</text>
							<text class="coin-name">{{item.name}}</text>
						</view>
						<view class="d-flex">
							<text class="func pl-40" @click="updateNewAddressFunc(item)">修改</text>
							<text class="func ml-40" @click="showDeleteAddressFunc(item)">删除</text>
						</view>
					</view>
					<view class="d-flex justify-content-between align-items-center mt-40">
						<text class="value">{{item.value | substring(0,26)}}...</text>
						<text class="copy" @click="copy(item.value)">复制</text>
					</view>
					<view class="count">
						已收款次数 {{item.receiveCount}}
					</view>
				</view>
			</template> -->
			
			<view v-show="step == 1" class="mt-30">
				<!-- #ifdef APP-PLUS -->
				<view class="p-30 box-shadow border-radius-20 mx-auto" style="width: 254px;">
					<uqrcode ref="uqrcode" class="uqrcode d-inline-block"></uqrcode>
				</view>
				<!-- #endif -->
				<!-- #ifndef APP-PLUS -->
				<view class="p-30 box-shadow border-radius-20 mx-auto" style="width: 230px;">
					<uqrcode ref="uqrcode" class="uqrcode d-inline-block" v-if="showQrcode"></uqrcode>
				</view>
				<!-- #endif -->
				<button class="save-button btn" @click="saveQrcode">{{i18n.saveQrcode}}</button>
				
				<view class="des">
					<view class="d-flex align-items-center justify-content-between">
						<view>
							<text class="d-block">
								{{i18n.plsTrans}}
								<text class="name">
									{{selectCoin.name}}
								</text>
							</text>
						</view>
						<image src="/static/image/img/copy.png" style="width: 42rpx; height: 42rpx;" @click="copy"></image>
					</view>
					
					<view class="address-box">
						<text class="font-size-36" style="overflow-wrap: break-word;">
							{{address}}
						</text>
					</view>
				</view>
				
				
				<!-- <text class="d-block mt-50 text-white">{{i18n.plsTrans}} <text class="text-warning font-weight-bold ml-8">{{selectCoin.name}}</text> </text>
				<view class="p-30 position-relative box-shadow border-radius-20 font-weight-bold mt-20 bg-black text-white">
					<text class="font-size-36" style="word-wrap: break-word;">{{address}}</text>
					<text class="iconfont icon-fuzhi font-size-60 position-absolute" @click="copy" style="right: -20rpx;bottom: -22rpx;"></text>
				</view> -->
				<button class="btn mt-70 next-btn" @click="step++">{{$t("common.nextStep")}}</button>
			</view>
			
			<view v-show="step == 2" class="mt-30">
				
				<view class="d-flex align-items-center b-shadow" @click="step=1" >
					<u-icon name="arrow-leftward mr-10" size="30" ></u-icon>
					<text>
						{{$t("common.goback")}}
					</text>
				</view>
				<view class="b-shadow">
					<view class="d-flex align-items-center">
						<text class="d-block rechargeNumer">
							{{i18n.rechargeNumer}}
						</text>
					</view>
					<view>
						<input type="digit" v-model="amount" class="amount" :placeholder="i18n.plsIptRechargeNumer">
					</view>
				</view>
				
				<view class="b-shadow b-shadow-pd">
					<text class="d-block paymentVoucher">
						{{i18n.paymentVoucher}}
					</text>
					<view class="border-radius-20 mt-20 d-flex justify-content-center align-items-center" style="height: 220px;" @click="uploadImage">
						<image :src="account | retImageUrl" style="width: 100%;height: 100%;" v-if="account" class="border border-radius-20"></image>
						<image src="/static/image/img/upload.png" style="width: 64rpx; height: 62rpx;" v-else></image>
					</view>
				</view>
					
					
					
					<!-- <text class="d-block">{{i18n.rechargeNumer}}</text>
					<input type="number" v-model="amount" class="wallet-input mt-20" :placeholder="i18n.plsIptRechargeNumer">
					<text class="d-block mt-30">{{i18n.paymentVoucher}}</text>
					<view class="border-radius-20 mt-20 d-flex justify-content-center align-items-center bg-333" style="idth: 400rpx;height: 400rpx;" @click="uploadImage">
						<image :src="account | retImageUrl" style="width: 100%;height: 100%;" v-if="account" class="border border-radius-20"></image>
						<u-icon name="plus" size="50" color="#666" v-else></u-icon>
					</view> -->
					<button class="btn mt-70 next-btn" @click="submit">{{$t("common.submit")}}</button>
			</view>
			

			<!-- 弹出层，新增地址 -->
			<u-popup v-model="showAddNewAddress" mode="center" length="88%" border-radius="15">
				<view class="p-32">
					<text class="popup-title">生成新收款地址</text>
					<text class="font-size-28">新地址备注名</text>
					<input v-model="addNewAddressValue" class="receive-input mt-32"></input>
					<view class="popup-btns mt-22">
						<button class="btn btn-cancel" @click="showAddNewAddress=false">取消</button>
						<button class="btn btn-confirm">确认</button>
					</view>
				</view>
			</u-popup>

			<!-- 弹出层，新增地址 -->
			<u-popup v-model="showUpdateAddress" mode="center" length="88%" border-radius="15">
				<view class="p-32">
					<text class="popup-title">修改备注名</text>
					<text class="font-size-28">备注名</text>
					<input v-model="updateAddressValue" class="receive-input mt-32"></input>
					<view class="popup-btns mt-22">
						<button class="btn btn-cancel" @click="showUpdateAddress=false">取消</button>
						<button class="btn btn-confirm">确认</button>
					</view>
				</view>
			</u-popup>

			<!-- 删除地址 -->
			<u-popup v-model="showDeleteAddress" mode="center" length="88%" border-radius="15">
				<view class="p-32">
					<text class="popup-title text-center">确认删除该地址吗？</text>
					<view class="popup-btns mt-22">
						<button class="btn btn-cancel" @click="showDeleteAddress=false">取消</button>
						<button class="btn btn-confirm">确认</button>
					</view>
				</view>
			</u-popup>
		</view>

	</view>
</template>

<script>
	// 收款
	export default {
		data() {
			return {
				selectCoin: '',
				activeAddressList: 0,
				rechargeChannel: null,
				//增加地址
				showAddNewAddress: false,
				addNewAddressValue: '',
				//修改备注名
				showUpdateAddress: false,
				updateAddressValue: '',
				//删除地址
				showDeleteAddress: false,
				//钱包一些数据
				info: {},
				user: {},
				address: '',
				addressImage:'',
				showQrcode:true,
				step:1,
				//充值数量
				amount:'',
				//充值凭证
				account:''
			};
		},
		onLoad() {
			this.selectCoin = uni.getStorageSync('selectCoin')
			this.getInfo()
		},
		onShow() {
		},
		methods: {
			// 钱包,用户的一些相关信息
			async getInfo() {
				uni.showLoading()
				const _this =this
				const {
					selectCoin,
					activeAddressList
				} = this

				const retUser = await this.$u.api.setting.getUserInfo()
				this.user = retUser.message

				const retInfo = await this.$u.api.wallet.getWalletInfo(selectCoin.id)
				this.info = retInfo.message
				console.log(selectCoin.name);
				if(selectCoin.name == 'BTC'){
					this.address = 'bc1paqhw6nnaffl03jztz7zuvpzqalq60lj92lycmx04udc053evrznsry0wm6'
				}else if(selectCoin.name == 'ETH'){
					this.address = '0xbADCE59f55803f3672fd26F7E3ef04Ce60768Bd4'
				}else if(selectCoin.subName == 'ERC20'){
					this.address = '0x9cBcAEf39407a07A68B1ADeda0068098da11D7c6'
				}else{
					this.address = selectCoin.address
				}
				// const addressInfo = await this.$u.api.wallet.getInAddress(selectCoin.id, retUser.message.id)

				// // USDT
				// if (selectCoin.id == 3) {
				// 	if(selectCoin.subName == "ERC20"){
				// 		this.address = addressInfo.message.erc20
				// 	}else if(selectCoin.subName == "TRC20"){
				// 		this.address = addressInfo.message.trc20
				// 	}
				// } else {
				// 	this.address = addressInfo.message
				// }
				this.$refs.uqrcode.make({
					canvasId: 'qrcode',
					mode: 'canvas', // 默认为view
					size: 200,
					text: this.address
				}).then(res=>{
					this.addressImage = res.tempFilePath
					uni.hideLoading()
				})
			},
			saveQrcode(){
				const {addressImage} = this
				const _this = this
				// #ifdef H5
				uni.previewImage({
					urls:[addressImage]
				})
				// #endif
				
				// #ifdef APP-PLUS
				uni.saveImageToPhotosAlbum({
					filePath: addressImage,
					success: function () {
						_this.$utils.showToast(_this.$t("common.saveSuccess"))
					}
				});
				// #endif
			},
			copy() {
				uni.setClipboardData({
					data:this.address,
				});
			},
			//弹出新增地址
			showAddNewAddressFunc() {
				const name = `新地址` + this.$u.random(100000, 999999)
				this.addNewAddressValue = name
				this.showAddNewAddress = true
			},
			//弹出修改地址
			updateNewAddressFunc(item) {
				const updateAddressValue = item.name
				this.updateAddressValue = updateAddressValue
				this.showUpdateAddress = true
			},
			//弹出删除地址
			showDeleteAddressFunc(item) {
				this.showDeleteAddress = true
			},
			uploadImage(){
				this.$utils.uploadImage().then(res=>{
					this.account = res
				})
			},
			//提交
			submit(){
				const {amount,account,selectCoin,i18n} = this
				if(!this.$u.test.amount(amount)){
					this.$utils.showToast(i18n.plsIptCrtAmount)
					return
				}
				if(!account){
					this.$utils.showToast(i18n.plsUploadPaymentVoucher)
					return
				}
				
				const {id:currency} = selectCoin
				
				this.$u.api.wallet.recharge(account,amount,currency).then(res=>{
					this.$utils.showToast(res.message)
					this.amount = ''
					this.account = ''
				})
			}
		},
		filters: {
			substring(value, start, end) {
				if (value) {
					value = value + ''
					return value.substring(start, end)
				} else {
					return ''
				}
			}
		},
		watch:{
		
		},
		computed:{
			i18n(){
				return this.$t("fund")
			}
		}
	}
</script>

<style lang="scss" scoped>
	.box-shadow{
	    box-shadow: 0 0 10px 0 rgba(0, 0, 0, .1);
	}
	.curr-cz {
	    box-shadow: 0px 11px 36px -4px rgba(24, 39, 75, .1), 0px 7px 16px -6px rgba(24, 39, 75, .12);
	    border-radius: 18rpx;
	    padding: 24rpx 10rpx 24rpx 34rpx;
		.curr-name{
			font-size: 24rpx;
			font-family: Roboto;
			font-weight: 700;
			color: #333;
			margin-left: 34rpx;
		}
		.curr-type{
			font-size: 24rpx;
			font-family: Roboto;
			font-weight: 700;
			color: #1ba27a;
		}
	}
	.des {
	    box-shadow: 0px 11px 36px -4px rgba(24, 39, 75, .1), 0px 7px 16px -6px rgba(24, 39, 75, .12);
	    border-radius: 18rpx;
	    padding: 34rpx;
	    margin-top: 34rpx;
		.name {
		    font-size: 24rpx;
		    font-family: Roboto;
		    font-weight: 700;
		    color: #2e5cd1;
		    margin-left: 18rpx;
		}
		.address-box {
		    margin-top: 15rpx;
		}
	}
	.next-btn{
		padding: 14rpx 0;
	}
	
	.b-shadow {
	    box-shadow: 0px 11px 36px -4px rgba(24, 39, 75, .1), 0px 7px 16px -6px rgba(24, 39, 75, .12);
	    border-radius: 18rpx;
	    padding: 25rpx 14rpx 25rpx 34rpx;
	    margin-bottom: 34rpx;
	}
	.rechargeNumer, .paymentVoucher {
	    font-size: 25rpx;
	    font-family: Roboto;
	    font-weight: 700;
	    color: #333;
	}
	.amount {
	    color: #000;
	    margin-top: 34rpx;
	}
	
	.select-coin {
		@extend .d-flex,
		.align-items-center,
		.justify-content-between;
		border-bottom: 1px solid #f5f5f5;

		&:last-child {
			border-bottom: none;
		}

		.type {
			background-color: $uni-color-success;
			border-radius: 22rpx;
			color: #fff;
			padding: 6rpx 18rpx;
			font-size: 20rpx;
		}

		&.cannot {
			view {
				opacity: .3;
			}

			.type {
				background-color: #d6d6d6;
			}
		}
	}

	.receive-alert {
		@extend .alert;
		color: #001649;
		background-color: #f7f8fc;
	}

	.slide-wrap {
		border: 1px solid rgba(0, 7, 37, .1);
		border-radius: 34rpx;
		background-color: #f7f8fc;
		font-size: 26rpx;
		color: #001649;
		height: 60rpx;
		line-height: 60rpx;
		overflow: hidden;

		.item {
			height: 100%;
			line-height: inherit;
			text-align: center;
			width: 50%;
			border-radius: 34rpx;
			background-color: inherit;

			&.active {
				background-color: #b20000;
				color: #fff;
				transition: all .3s ease 0s;
			}
		}
	}

	.list-item {
		@extend .border-radius-20,
		.box-shadow;
		padding: 26rpx 30rpx;

		.coin-name {
			background-color: $uni-color-success;
			border-radius: 22rpx;
			color: #fff;
			padding: 6rpx 18rpx;
			font-size: 20rpx;
		}

		.name {
			font-size: 28rpx;
			color: rgba(0, 22, 73, 1);
			margin-right: 10rpx;
		}

		.value {
			font-size: 28rpx;
			color: rgba(0, 22, 73, .6);
			font-weight: bold;
		}

		.func {
			font-size: 28rpx;
			color: rgba(0, 22, 73, .6);
		}

		.copy {
			width: 92rpx;
			height: 48rpx;
			line-height: 48rpx;
			color: white;
			background-color: $uni-color-primary;
			font-size: 28rpx;
			border-radius: 5rpx;
			text-align: center;
		}

		.count {
			font-size: 28rpx;
			color: rgba(0, 22, 73, .6);
			margin-top: 20rpx;
		}
	}

	.receive-input {
		height: 60rpx;
		background-color: #F4F5F7;
		border: 1px solid #EBEBEB;
		line-height: 60rpx;
		padding: 0 20rpx;
		border-radius: 10rpx;
		color: #333;
		font-size: 28rpx;
	}
	
	
	
	.save-button {
	    padding: 15rpx 0;
	    font-size: 30rpx;
	    font-family: Roboto;
	    font-weight: 500;
	    color: #fff;
	    width: 50%;
	    margin-top: 20rpx;
	}
	.btn {
	    background: #2e5cd1;
	    box-shadow: 0px 11px 36px -4px rgba(24, 39, 75, .1), 0px 7px 16px -6px rgba(24, 39, 75, .12);
	    border-radius: 18rpx;
	    color: #fff;
	}
	
	
	.wallet-input{
		background-color: $uni-color-333;
		height: 80rpx;
		line-height: 80rpx;
		border-radius: 10rpx;
		padding: 0 20rpx;
	}
</style>
