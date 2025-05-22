<template>
	<view>
		<view class="position-relative" style="height: 40vh;">
			<view class="white-circle box-shadow back animated fadeInLeft" @click="$utils.jump(1,'navigateBack')">
				<u-icon name="arrow-leftward" size="38"></u-icon>
			</view>
			
			<view class="animated fadeInDown" style="filter: blur(14rpx);">
				<image :src="data.head_portrait  | retImageUrl"  style="width: 100%;height: 30vh;" mode="aspectFill"></image>
				<view class="jianbian"></view>
			</view>
			
			<view class="d-flex flex-direction-column align-items-center position-absolute" style="top: 16vh;z-index: 10;left: 0;right: 0;">
				<view class="text-center animated fedeInUp">
					<view class="border-radius-50per overflow-hidden mx-auto box-shadow" style="width: 146rpx;height: 146rpx;border: 4rpx solid #ffffff;">
						<image :src="data.head_portrait  | retImageUrl" style="width: 100%;height: 100%;" mode="aspectFill"></image>
					</view>
					<text class="d-block mt-20 font-weight-bold text-deepblue font-size-40">{{data.nickname}}</text>
				</view>
				<view class="border mt-30 py-20 border-radius-20 d-grid-columns-3 w-90 mx-auto animated fadeInUp" style="border-color: rgba(0,0,0,.05);">
					<view class="text-center">
						<text class="d-block font-weight-bold text-deepblue font-size-30">{{data.artworks_collect_number | numberFormat}}</text>
						<text class="d-block opacity-50 font-size-22 mt-4">{{i18n.byCollection}}</text>
					</view>
					<view class="text-center">
						<text class="d-block font-weight-bold text-deepblue font-size-30">{{artworks.length | numberFormat}}</text>
						<text class="d-block opacity-50 font-size-22 mt-4">{{i18n.artworks}}</text>
					</view>
					<view class="text-center">
						<text class="d-block font-weight-bold text-deepblue font-size-30">{{collects.length | numberFormat}}</text>
						<text class="d-block opacity-50 font-size-22 mt-4">{{i18n.collection}}</text>
					</view>
				</view>
			</view>
		</view>
		<view class="mx-30">
			
			<slide-nav :list="nav" :current="0" @change="clickNav"></slide-nav>
			
			<!-- <view class="d-grid-columns-3 nav font-size-32 font-weight-bold text-secondary position-relative py-20">
				<text class="text-center nav-1" :class="{'text-deepblue':activeNav == 0}" @click="clickNav(0)">{{i18n.position}}</text>
				<text class="text-center nav-2" :class="{'text-deepblue':activeNav == 1}" @click="clickNav(1)">{{i18n.placeRecord}}</text>
				<text class="text-center nav-2" :class="{'text-deepblue':activeNav == 2}" @click="clickNav(2)">{{i18n.message}}</text>
				<text class="nav-line" :style="navLineStyle"></text>
			</view> -->
			
			<view class="mt-40 d-grid-columns-2 " style="grid-column-gap:20rpx" v-if="activeNav == 0">
				<view class="mb-40 animated fadeInLeft" v-for="item in position" @click="$utils.jump('/pages/nft/myNft?id='+item.id)">
					<u-image :src="item.image | retImageUrl" width="100%" height="334" border-radius="10"></u-image>
					<view class="d-flex mt-16">
						<text class="tag  type-pay_type" v-if="item.pay_type_name" :class="item.pay_type_name.color" >{{item.pay_type_name.name}}</text>
						<text class="tag tag-warning ml-10" v-if="item.pay_type == 3 && item.rarity_status == 0">{{i18n.noOpen}}</text>
						<text class="tag tag-success ml-10" v-if="item.pay_type == 3 && item.rarity_status == 1">{{i18n.hasOpen}}</text>
						<text class="tag tag-secondary ml-10" v-if="item.resell_nft_status == 1">{{i18n.reselling}}</text>
					</view>
					<text class="d-block font-size-32 mt-16 font-weight-bold text-deepblue">{{item.name}}</text>
					
				</view>
			</view>
			
			<view class="mt-40" style="grid-column-gap:20rpx" v-if="activeNav == 1">
				<view class="mb-24 border-radius-10 d-flex-between-center p-30" v-for="item in order" style="background-image: linear-gradient(225deg, #edf1f9 0%, #e4efe9 100%);">
					<view class="d-flex align-items-center">
						<u-image :src="item.nft_image | retImageUrl" width="90" height="90" border-radius="10" ></u-image>
						<view class="ml-20">
							<text class="d-block font-weight-bold text-deepblue font-size-32"><text class="text-error">{{item.nft_name}}</text></text>
							<text class="d-block opacity-50 font-size-24 mt-4">{{item.created}}</text>
						</view>
					</view>
					<view class="font-weight-bold font-size-32 text-deepblue">
						{{item.price + ' ' + item.currency_name}}
					</view>
				</view>
			</view>
			
			<view class="mt-40" style="grid-column-gap:20rpx" v-if="activeNav == 2">
				<view class="mb-24 box-shadow border-radius-10 d-flex-between-center p-30"  v-for="(item,index) in needPayList" @click="read(index)" :class=" item.is_read == 1 ? 'bg-success-filter' : 'bg-error-filter' " >
					<u-image :src="item.image  | retImageUrl" width="150" height="150" class="mr-20" border-radius="20"></u-image>
					<view class="" style="flex: 1;">
						<text class="d-block font-size-32 font-weight-bold text-deepblue">{{item.name}}</text>
						<view class="d-flex align-items-baseline mt-8" v-if="item.is_pay == 0 && item.is_expired == 0">
							<text class="d-block font-size-22 opacity-50">{{i18n.zfdjs}}：</text>
							<text class="d-block font-weight-bold  text-deepblue">
								{{$utils.stamp2Time(item.remainTime,'h')}}:{{$utils.stamp2Time(item.remainTime,'m')}}:{{$utils.stamp2Time(item.remainTime,'s')}}
							</text>
						</view>
						<view class="d-flex align-items-baseline mt-8" v-else>
							<text class="d-block font-size-22 opacity-50">{{i18n.payTime}}：</text>
							<text class="d-block font-weight-bold font-size-24  text-deepblue">
								{{item.pay_time | date('yyyy-mm-dd hh:MM:ss')}}
							</text>
						</view>
						
						<view class="d-flex align-items-baseline mt-8">
							<text class="d-block font-size-22 opacity-50">{{i18n.lastPrice}}：</text>
							<text class="d-block font-weight-bold text-deepblue">
								{{item.price}}{{item.currency_name}}
							</text>
						</view>
					</view>
					<view class="">
						<text class="tag tag-success font-size-24" v-if="item.is_pay == 1">{{i18n.isPay}}</text>
						<text class="tag tag-error font-size-24" v-else-if="item.is_expired == 1">{{i18n.isExpired}}</text>
						<text class="tag tag-primary d-block py-10 px-30 border-radius-20" @click.stop="pay(item)"  v-else>
							<u-icon name="arrow-rightward" size="34"></u-icon>
						</text>
					</view>
				</view>
			</view>
			
			<default-page v-if="(activeNav == 0 && position.length == 0) || (activeNav == 1 && order.length == 0) || (activeNav == 2 && needPayList.length == 0)"></default-page>
			
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				activeNav:0,
				data:{},
				artworks:[],
				collects:[],
				purchased:[],
				navLineStyle:{
					left:'10%',
					right:'76%'
				},
				position:[],
				order:[],
				page:1,
				noReadNum:0,//未读数量
				needPayList:[],
				interval:null
			};
		},
		onLoad() {
			this.user = this.$store.state.user
		},
		onShow() {
			this.getArtistDetail()
			this.getMyNFTs()
			this.getNeedPayNFTOrder()
		},
		methods:{
			getArtistDetail(){
				this.$u.api.nft.getArtistDetail(this.user.id).then(res=>{
					this.artworks = res.message.artworks
					delete res.message.artworks
					this.collects = res.message.collects
					delete res.message.collects
					this.data = res.message
				})
			},
			getMyNFTs(){
				const {i18n} = this
				this.$u.api.nft.getMyNFTs().then(res=>{
					const buyType = {
						'1':{name:i18n.normal,color:'tag-primary'},
						'2':{name:i18n.auction,color:'tag-warning'},
						'3':{name:i18n.bindBox,color:'tag-error'}}
						
					let list = res.message
					list.forEach(data=>{
						data.pay_type_name = buyType[data.pay_type.toString()]
					})
					
					this.position = res.message
				})
			},
			getMyOrder(){
				this.$u.api.nft.getMyOrder(this.page).then(res=>{
					let list = res.message.data
					if(this.page == 1) this.order = []
					if(list.length){
						list.forEach(item => {
							item.price = Number((+item.price).toFixed(4))
						})
						this.order = [...this.order,...list]
						this.page++
					}else{
						this.canGet = false
					}
				})
			},
			clickNav(index){
				if(index == 0){
					this.getMyNFTs()
				}else if(index == 1){
					this.page = 1;
					this.canGet = true
					this.getMyOrder()
				}else if(index == 2){
					this.getNeedPayNFTOrder()
				}
				this.activeNav = index
			},
			getNeedPayNFTOrder(){
				this.$u.api.nft.getNeedPayNFTOrder().then(res=>{
					this.noReadNum = res.message.reduce((total,el)=>{
						if(el.is_read == '0'){
							return total + 1
						}else return total
					},0)
					console.log(this.noReadNum);
					const nowTimestamp = Date.parse(new Date()) / 1000 
					this.needPayList = res.message.map(item => {
						if(item.is_pay == 0 && item.is_expired == 0){
							item.remainTime = Number(item.overtime) - nowTimestamp
						}
						item.price = Number(Number(item.price).toFixed(4))
						return item
					})
					if(this.interval){
						clearInterval(this.interval)
					}
					this.interval = setInterval(()=>{
						this.needPayList.forEach(item => {
							if(item.remainTime > 0){
								item.remainTime--
							}
							if(item.remainTime == 0){
								item.is_expired = 1
							}
						})
					},1000)
					
				})
			},
			read(index){
				this.$u.throttle(()=>{
					const {i18n} = this
					let item = this.needPayList[index]
					if(item.is_read == 0){
						this.$u.api.nft.readMessage(item.id).then(res=>{
							item.is_read = 1
							this.needPayList.splice(index,1,item)
							this.$utils.showToast(i18n.hasRead)
						})
					}
				},1000)
			},
			//支付
			pay(item){
				this.$u.throttle(async ()=>{
					const {i18n} = this
					const ret = await this.$utils.showModal(this.$t("common.hint"),i18n.confirmPay)
					if(!ret) return
					this.$u.api.nft.payOrder(item.id).then(res=>{
						this.$utils.showToast(this.$t("common.success"))
						this.getNeedPayNFTOrder()
					})
				},1000)
			},
			onPullDownRefresh(){
				if(this.activeNav == 0){
					
				}
			}
		},
		computed: {
			i18n() {
				return this.$t("nft")
			},
			nav(){
				const {noReadNum} = this
				const i18n = this.$t("nft")
				return [{
					name:i18n.position
				},{
					name:i18n.placeRecord
				},{
					name:i18n.message,
					num:noReadNum
				}]
			}
		},
		onUnload() {
			if(this.interval){
				clearInterval(this.interval)
			}
		}
	}
</script>
<style>
	page,html,body{
		background-color: white;
	}
</style>
<style lang="scss">
.white-circle {
		width: 80rpx;
		height: 80rpx;
		border-radius: 50%;
		background-color: white;
		display: flex;
		align-items: center;
		justify-content: center;
	}

	.back {
		position: absolute;
		left: 30rpx;
		top: 30rpx;
		z-index: 10;
	}
	
	.jianbian{
		position: absolute;
		left: 0;
		right: 0;
		top: 15vh;
		height: 260rpx;
		background-image: url(../../static/image/icon/jianbian_2.png);
		background-size: cover;
	}
	
	.nav-line{
		position: absolute;
		bottom: 0;
		height: 6rpx;
		background-color: $uni-color-deepblue;
		border-radius: 6rpx;
		transition: all .3s ease 0s;
	}
	
	.timer-wrap{
		background-image: linear-gradient(-225deg, #E3FDF5 0%, #FFE6FA 100%);
		font-size: 32rpx;
		width: 60vw;
		margin: 0 auto;
		padding: 30rpx 0;
		border-radius: 40rpx;
		font-weight: bold;
		text-align: center;
		color: $uni-color-deepblue;
		box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important;
	}
	
	.bg-filter{
		position: relative;
		&::after{
			content: '';
			display: block;
			position: absolute;
			left: 0;
			right: 0;
			top: 0;
			bottom: 0;
			z-index: -1;
		}
	}
	
	.bg-success-filter{
		position: relative;
		transition: all .3s ease 0s;
		&::after{
			content: '';
			display: block;
			position: absolute;
			left: 0;
			right: 0;
			top: 0;
			bottom: 0;
			z-index: -1;
			background-image: linear-gradient(-225deg, #DFFFCD 0%, #90F9C4 48%, #39F3BB 100%);
			filter: blur(10rpx);
			opacity: .2;
		}
		
	}
	
	.bg-error-filter{
		position: relative;
		overflow: hidden;
		transition: all .3s ease 0s;
		&::after{
			content: '';
			display: block;
			position: absolute;
			left: 0;
			right: 0;
			top: 0;
			bottom: 0;
			z-index: -1;
			background-image: linear-gradient(to right, #f78ca0 0%, #f9748f 19%, #fd868c 60%, #fe9a8b 100%);
			filter: blur(10rpx);
			opacity: .2;
		}
	}
</style>
