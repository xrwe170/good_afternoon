<template>
	<view>
		<view class="status_bar"></view>
		<view class="py-30 mx-50 d-flex-between-center">
			<view class="white-circle box-shadow transition" @click="showFliterFunc" :style="{transform:showFliter ? 'rotate(90deg)' : 'rotate(0)'}">
				<u-icon name="list" size="42" color="#0d2758"></u-icon>
			</view>
			<view class="white-circle box-shadow">
				<u-image :src="$store.state.user.head_portrait  | retImageUrl" width="66" height="66" border-radius="500"></u-image>
			</view>
		</view>
		<view class="mx-30 mb-30 py-30 border-radius-20 box-shadow bg-white animated text-deepblue" v-if="showFliter"  :class="showFliterAnimate ? 'fadeInDown' : 'fadeOutUp'" >
			<view class="mx-30">
				<input type="text" class="filter-input" :placeholder="i18n.place_alert5">
			</view>
			<view class="u-border-bottom p-30" v-for="item in filters" style="white-space: nowrap;">
				<text class="d-block font-size-32 font-weight-bold">{{item.name}}</text>
				<scroll-view :scroll-x="true" class="mt-30">
					<view class="filter-item d-inline-block" :class="{'active':currentNav[item.value] == el.value}" @click="currentNav[item.value] = el.value" v-for="el in item.child">{{el.name}}</view>
				</scroll-view>
			</view>
			<view class="d-grid-columns-2 m-30" style="grid-column-gap:20rpx" >
				<button class="filter-button w-100" @click="reset">{{i18n.reset}}</button>
				<button class="filter-button filter-button-dark w-100" @click="search">{{i18n.confirm}}</button>
			</view>
		</view>
		<view class="mx-50 mb-30 d-grid-columns-2" style="grid-column-gap:20rpx">
			<view class="w-100 overflow-hidden" v-for="(item,index) in list">
				<nft-artwork :data="item" @clickCollect="collect"></nft-artwork>
			</view>
			<default-page v-if="!has"></default-page>
		</view>

	</view>
</template>

<script>
	export default {
		data() {
			return {
				showFliter:true,
				showFliterAnimate:true,
				currency:[],
				currentNav:{
					currency:-1,
					pay_type:-1,
					status:-1,
					type:-1,
					page:1,
					limit:999
				},
				list:[],
				has:-1
				
			};
		},
		created() {
			this.getNftCurrency()
		},
		methods:{
			getNftCurrency(){
				this.$u.api.nft.getNftCurrency().then(res=>{
					this.currency = res.message.map(el=> {return {value:el.id,name:el.name}})
				})
			},
			showFliterFunc(){
				this.showFliterAnimate = !this.showFliterAnimate
				setTimeout(()=>{
					this.showFliter = !this.showFliter
				},200)
			},
			reset(){
				this.currentNav = {
					currency:-1,
					pay_type:-1,
					status:-1,
					type:-1,
					page:1,
					limit:999
				}
			},
			search(){
				let currentNav = this.$u.deepClone(this.currentNav)
				for(let i in currentNav){
					if(currentNav[i] == -1) delete currentNav[i]
				}
				this.has = -1
				this.$u.api.nft.getArtwork(currentNav).then(res=>{
					let list = res.message
					if(list.length){
						list.forEach(item => {
							item.show = true
						})
						this.list = list
						this.has = list.length
					}else{
						this.has = 0
					}
					this.showFliterFunc()
					
				})
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
			},
			filters(){
				const i18n = this.$t("nft")
				const {currency} = this
				return [{
					value:'currency',
					name:i18n.currency_type,
					child:currency
				},{
					value:'pay_type',
					name:i18n.pay_type,
					child:[{
						value:1,
						name:i18n.normal
					},{
						value:2,
						name:i18n.auction
					}]
				},{
					value:'status',
					name:i18n.sell_status,
					child:[{
						value:0,
						name:i18n.saled
					},{
						value:1,
						name:i18n.hasStart
					},{
						value:2,
						name:i18n.willStart
					}]
				},{
					value:'type',
					name:i18n.artwork_type,
					child:[{
						value:1,
						name:i18n.image
					},{
						value:2,
						name:i18n.gif
					},{
						value:3,
						name:i18n.audio
					},{
						value:4,
						name:i18n.video
					}]
				}]
			}
		}
	}
</script>
<style lang="scss" scoped>
@import '../../../static/animate.min.css';
.white-circle{
	width: 80rpx;height: 80rpx;
	border-radius: 50%;
	background-color: white;
	display: flex;
	align-items: center;
	justify-content: center;
}

.filter-item{
	height: 80rpx;
	line-height: 80rpx;
	padding: 0 60rpx;
	margin-right: 30rpx;
	border: 2rpx solid #ddd;
	border-radius: 20rpx;
	font-size: 32rpx;
	font-weight: bold;
	color: $uni-color-deepblue;
	&.active{
		border:4rpx solid $uni-color-deepblue; ;
		background-image: linear-gradient(to right, #9795f0 0%, #fbc8d4 100%);background-image: linear-gradient(-225deg, #FFFEFF 0%, #D7FFFE 100%);

	}
}

.filter-button{
	font-size: 32rpx;
	background-color: rgba(13,39,88,.15);
	color: $uni-color-deepblue;
	font-weight: bold;
	&.filter-button-dark{
		background-color: $uni-color-deepblue;
		color: #fff;
	}
	&::after{
		border: none;
	}
}

.filter-input{
	background-color: rgba(13,39,88,.05);
	height: 80rpx;
	border-radius: 40rpx;
	padding-left: 30rpx;
	font-weight: bold;
}

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
</style>
