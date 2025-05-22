<template>
	<view>
		<Artwork ref="artwork" :noReadNum="noReadNum" v-if="activeNav == 1" @jumpTo="(val)=>selectNav(val)" class="animated fadeInRight" :class="activeNav2 != 1 ? 'fadeOutLeft' : '' "></Artwork>
		<Search v-else-if="activeNav == 2" class="animated fadeInRight" :class="activeNav2 != 2 ? 'fadeOutLeft' : '' "></Search>
		<Collection v-else-if="activeNav == 3" @jumpTo="(val)=>selectNav(val)" class="animated fadeInRight" :class="activeNav2 != 3 ? 'fadeOutLeft' : '' "></Collection>
		<Artist ref="artist" v-else-if="activeNav == 4" class="animated fadeInRight" :class="activeNav2 != 4 ? 'fadeOutLeft' : '' "></Artist>
		
		<!-- footernav -->
		<view class="fixed-nav text-center animated fadeInUp" @click="clickFixedNav" >
			<view class="px-30 d-flex align-items-center h-100">
				<view v-for="(item,index) in nav" @click.stop="selectNav(index)" class="w-25 text-center d-block" :class="'navitem-' + index" :style="{color:activeNav == index  ? '#fff' : '#333'}">
					<text class="iconfont font-size-36 animated fadeInRight transition font-weight-bold" :class="item.name"></text>
				</view>
			</view>
			<text class="circle animated fadeIn" :style="{left:circleLeft}"></text>
		</view>
	</view>
</template>

<script>
	import Artwork from './components/artwork'
	import Artist from './components/artist'
	import Search from './components/search'
	import Collection from './components/collection'
	export default {
		data() {
			return {
				view:null,
				activeNav:1,
				activeNav2:1,
				circleLeft:'48rpx',
				nav:[{
					name:'icon-shouye-shouye',
				},{
					name:'icon-shangpin',
				},{
					name:'icon-sousuo1',
				},{
					name:'icon-aixin',
				},{
					name:'icon-wode3',
				}],
				noReadNum:0,//未读消息的数量
			};
		},
		components:{
			Artist,
			Artwork,
			Search,
			Collection
		},
		onLoad() {
			
		},
		onShow() {
			setTimeout(()=>{
				this.selectNav(this.activeNav)
			},200)
			this.getNeedPayNFTOrder()
		},
		methods:{
			selectNav(index){
				if(index == 0){
					uni.switchTab({
						url:'/pages/index/index'
					})
				}
				const _this = this
				this.view = uni.createSelectorQuery().in(this);
				this.view.select('.fixed-nav').boundingClientRect();
				this.view.select('.circle').boundingClientRect();
				this.view.select('.navitem-0').boundingClientRect();
				this.view.select('.navitem-1').boundingClientRect();
				this.view.select('.navitem-2').boundingClientRect();
				this.view.select('.navitem-3').boundingClientRect();
				this.view.select('.navitem-4').boundingClientRect();
									
				this.view.exec(res => {
					if(res[1] && res[0]){
						const circleLength = res[1].width / 2
						const length = res[index + 2].width / 2
						_this.circleLeft = (res[index + 2].left - res[0].left) + (length - circleLength)  +  'px'
						
						this.activeNav2 = index
						setTimeout(()=>{
							this.activeNav = index
						},300)
					}
					
				});
			},
			getNeedPayNFTOrder(){
				this.$u.api.nft.getNeedPayNFTOrder().then(res=>{
					this.noReadNum = res.message.reduce((total,el)=>{
						if(el.is_read == '0'){
							return total + 1
						}else return total
					},0)
					
				})
			}
			
		},
		onReachBottom() {
			//触及到底部
			if(this.activeNav == 1){
				this.$refs.artwork.getArtwork()
			}else if(this.activeNav == 4){
				this.$refs.artist.getArtist()
			}
		}
	}
</script>
<style>
	page,html,body{
		min-height: 100vh;
		padding-bottom: 160rpx;
		background-color: white;
	}
</style>
<style lang="scss" scoped>

.fixed-nav{
	position: fixed;
	bottom: 10vw;
	@extend .box-shadow;
	height: 100rpx;
	border-radius: 50rpx;
	transition: all .8s ease 0s;
	z-index: 999;
	background-color: #fff;
	left: 16vw;
	right: 16vw;
	.circle{
		position: absolute;
		top: 20rpx;
		height: 60rpx;
		width: 60rpx;
		background-color: $uni-color-error;
		border-radius: 50%;
		z-index: -1;
		transition: all .3s ease 0s;
	}
}
</style>
