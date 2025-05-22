<template>
	<view class="pb-130">
		<view class="status_bar"></view>
		<text class="d-block font-size-40 text-deepblue mx-30 p-30 font-weight-bold animated fadeInLeft">{{i18n.artistList}}</text>
		<view class="m-30 box-shadow border-radius-20 p-30 d-flex-between-center animated fadeInUp position-relative overflow-hidden" v-for="(item,index) in list" @click="$utils.jump('/pages/nft/artistDetail?uid='+item.id)">
			<view class="d-flex align-items-center">
				<u-image :src="item.head_portrait  | retImageUrl" width="120" height="120" border-radius="50%"></u-image>
				<text class="font-weight-bold font-size-40 text-deepblue ml-20">{{item.nickname || i18n.nonickname}}</text>
			</view>
			<view class="go-artist">
				<u-icon name="arrow-rightward" size="32"></u-icon>
			</view>
			<view class="position-absolute" style="left: 0;right: 0;top: 0;bottom: 0;opacity: .2;z-index: -1;">
				<image :src="require(`../../../static/image/icon/artist_bg_${index % 6 + 1}.jpg`)" style="width: 100%;height: 100%;" mode="aspectFill"></image>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				list:[],
				page:1,
				canGet:true,
				limit:8,
			};
		},
		created() {
			this.page = 1
			this.canGet = true
			this.list = []
			this.getArtist()
		},
		methods:{
			getArtist(){
				const {page,canGet,limit} = this
				if(!canGet) return
				this.$u.api.nft.getArtist({page,limit}).then(res=>{
					let list = res.message
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
				return this.$t("nft")
			}
		}
	}
</script>

<style lang="scss">
@import '../../../static/animate.min.css';
.go-artist{
	width: 80rpx;
	height: 60rpx;
	border-radius: 16rpx;
	background-image: linear-gradient(-225deg, #E3FDF5 0%, #FFE6FA 100%);
	display: flex;
	align-items: center;
	justify-content: center;
}
</style>
