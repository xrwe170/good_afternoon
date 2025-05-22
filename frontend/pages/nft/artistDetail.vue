<template>
	<view class="pb-130">
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
			<view class="d-grid-columns-2 nav font-size-32 font-weight-bold text-secondary position-relative py-20">
				<text class="text-center nav-1" :class="{'text-deepblue':activeNav == 0}" @click="clickNav(0)">{{i18n.artworks}}</text>
				<text class="text-center nav-2" :class="{'text-deepblue':activeNav == 1}" @click="clickNav(1)">{{i18n.collection}}</text>
				<text class="nav-line" :style="navLineStyle"></text>
			</view>
			<view class="mt-40 d-grid-columns-2 " style="grid-column-gap:20rpx" v-if="activeNav == 0">
				<view class="mb-40 animated fadeInLeft" v-for="item in artworks" @click="$utils.jump('/pages/nft/artworkDetail?id='+item.id)">
					<u-image :src="item.image | retImageUrl" width="100%" height="334" border-radius="10"></u-image>
					<text class="d-block font-size-32 mt-16 font-weight-bold text-deepblue">{{item.name}}</text>
				</view>
				<default-page v-if="!artworks.length"></default-page>
			</view>
			<view class="mt-40 d-grid-columns-2 " style="grid-column-gap:20rpx" v-else>
				<view class="mb-40 animated fadeInLeft" v-for="item in collects" @click="$utils.jump('/pages/nft/artworkDetail?id='+item.id)">
					<u-image :src="item.image | retImageUrl" width="100%" height="334" border-radius="10"></u-image>
					<text class="d-block font-size-32 mt-16 font-weight-bold text-deepblue">{{item.name}}</text>
				</view>
				<default-page v-if="!artworks.length"></default-page>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				uid:0,
				activeNav:-1,
				navLineStyle:{left:0,right:0},
				data: {
					pic:'http://desk-fd.zol-img.com.cn/t_s1920x1080/g6/M00/02/0A/ChMkKWGAmuqIRyQ8ADQDcB93xVgAAVLRwOOfkkANAOI798.jpg',
					title:'The Treasure Keeper',
					avatar:'http://img.duoziwang.com/2021/06/q101801413228587.jpg',
					name:'Helloyolaa',
					price:1.28,
					currency_name:'ETH'
				},
				artworks:[],
				collects:[],
			};
		},
		onLoad(options) {
			const {uid} = options
			if(!uid){
				this.$utils.showToast(this.$t("common.paramsWrong"));
				setTimeout(()=>{
					uni.navigateBack({
						delta:1
					})
				},1200)
				return
			}
			this.uid = uid
		},
		mounted() {
			setTimeout(()=>{
				this.clickNav(0)
			},200)
			this.getArtistDetail()
		},
		methods:{
			getArtistDetail(){
				this.$u.api.nft.getArtistDetail(this.uid).then(res=>{
					this.artworks = res.message.artworks
					delete res.message.artworks
					
					this.collects = res.message.collects
					delete res.message.collects
					
					this.data = res.message
				})
			},
			clickNav(index){
				if(index == this.activeNav) return
				if(index == 0){
					this.navLineStyle = {left:0,right:0}
					setTimeout(()=>{
						this.navLineStyle = {left:0,right:'50%'}
						this.activeNav = 0
					},200)
				}else{
					this.navLineStyle = {left:0,right:0}
					setTimeout(()=>{
						this.activeNav = 1
						this.navLineStyle = {left:'50%',right:0}
					},200)
				}
			}
		},
		computed:{
			i18n(){
				return this.$t("nft")
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

</style>
