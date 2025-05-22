<template>
	<view>
		<view class="status_bar"></view>
		<view class="pt-30 mx-50 d-flex-between-center">
			<view class="white-circle box-shadow animated fadeInLeft" @click="jump2Search">
				<text class="iconfont icon-quanbu font-size-42 opacity-75"></text>
			</view>
			<view class="white-circle box-shadow animated fadeInRight position-relative" @click="$utils.jump('/pages/nft/mine')">
				<u-image :src="$store.state.user.head_portrait  | retImageUrl" width="66" height="66" border-radius="500"></u-image>
				<text class="fixed-number" v-if="noReadNum">{{noReadNum}}</text>
			</view>
		</view>
		<view class="mx-50 mb-30 d-grid-columns-2" style="grid-column-gap:20rpx">
			<view class="w-100 overflow-hidden" v-for="(item,index) in list">
				<nft-artwork :data="item" @clickCollect="collect"></nft-artwork>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		props:{
			noReadNum:{
				type:Number,
				default:0
			}
		},
		data() {
			return {
				list:[],
				page:1,
				canGet:true,
				limit:6,
			};
		},
		created() {
			this.page = 1
			this.canGet = true
			this.list = []
			this.getArtwork()
		},
		mounted() {

		},
		methods:{
			getArtwork(){
				const {page,canGet,limit} = this
				if(!canGet) return
				this.$u.api.nft.getArtwork({page,limit}).then(res=>{
					let list = res.message
					if(list.length){
						list.forEach(item => {
							item.show = true
						})
						this.list = [...this.list,...list]
						this.page++
					}else{
						this.canGet = false
					}
				})
			},
			//通知父组件跳转至search
			jump2Search(){
				this.$emit('jumpTo',2)
			},
			//收藏
			collect(code){
				const has = this.list.findIndex(el => el.code == code)
				if(has > -1){
					let item = this.list[has]
					item.collect = !item.collect
					this.list.splice(has,1,item)
				}
			}
		},
		computed:{
			i18n(){
				return this.$t("nft")
			}
		},
	}
</script>

<style lang="scss" scoped>
.white-circle{
	width: 80rpx;height: 80rpx;
	border-radius: 50%;
	background-color: white;
	display: flex;
	align-items: center;
	justify-content: center;
}

.place-a-bid{
	left: 0;right: 0;bottom: 0;
	background-image: linear-gradient(to right, #ffecd2 0%, #fcb69f 100%);
	border-radius: 0 0 20rpx 20rpx;
	position: absolute;
	text-align: center;
	font-size: 38rpx;
	font-weight: bold;
	padding: 30rpx 0;
	color: $uni-color-deepblue;
	&.place-a-bid-2{
		background-image: linear-gradient(120deg, #fdfbfb 0%, #ebedee 100%);
		color: #999;
		font-size: 32rpx;
	}
}

.type-name{
	position: absolute;
	right: 20rpx;
	top: 20rpx;
}

.fixed-number{
	position: absolute;
	display: flex;
	align-items: center;
	justify-content: center;
	width: 38rpx;
	height: 38rpx;
	border-radius: 50%;
	background-image: linear-gradient(to right, #f78ca0 0%, #f9748f 19%, #fd868c 60%, #fe9a8b 100%);
	color: #fff;
	right: -10rpx;
	top:  -6rpx;
	font-size: 22rpx;
}
</style>
