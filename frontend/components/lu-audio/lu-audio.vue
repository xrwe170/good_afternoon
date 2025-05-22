<template>
	<view class="text-center">
		<view class="position-relative mx-auto" style="width: 350rpx;height: 525rpx;">
			<u-image :src="pic" width="100%" height="100%" border-radius="10" class="d-block mx-auto"></u-image>
			<view class="position-absolute d-flex align-items-center justify-content-center" @click="control()" style="left: 0;right: 0;top: 0;bottom: 0;background-color: rgba(0,0,0,.1);">
				<u-icon name="pause-circle" size="80" color="#aaa" v-if="hasPlay"></u-icon>
				<u-icon name="play-circle" size="80" color="#aaa" v-else></u-icon>
			</view>
			<text class="position-absolute font-size-22 opacity-75" style="right: 10rpx;bottom: 10rpx;">{{currentTime + ' / ' + duration}}</text>
		</view>
		<text class="d-block mt-30 font-weight-bold font-size-40" v-if="title">{{title}}</text>
		<text class="d-block opacity-50" v-if="author">by {{author}}</text>
		<view class="d-flex justify-content-center mt-20 align-items-center">
			<view class="border-radius-10 overflow-hidden progress" style="background-color: #dddddd;width: 200rpx;height: 12rpx;" @click="clickProgress">
				<text class="d-block h-100" style="background-color: #2979ff;" :style="{width:percent + '%'}"></text>
			</view>
			<text class="font-size-26 opacity-75 ml-10">{{percent}}%</text>
		</view>
	</view>
</template>

<script>
	export default {
		name:"lu-audio",
		props:{
			src:{
				type:String,
				required:true,
			},
			pic:{
				type:String,
				default:require('../../static/image/icon/nft-product-bg.png')
			},
			title:{
				type:String,
				default:''
			},
			author:{
				type:String,
				default:''
			}
		},
		data(){
			return {
				audio:null,
				duration:'',
				interval:null,
				currentTime:'00:00',
				percent:0,
				hasPlay:false
			}
		},
		created() {
			this.createAudio()
		},
		methods:{
			createAudio(){
				if(this.src){
					this.audio = uni.createInnerAudioContext();
					this.audio.src = this.src
					this.audio.onError(()=>{
						this.$utils.showToast(this.i18n.audioError)
					})
					this.audio.onCanplay((res)=>{
						this.duration = this.getTime(this.audio.duration)
					})
					
				}else{
					console.log('src无效');
				}
			},
			//控制audio的开关
			control(){
				if(!this.audio.paused){
					this.audio.pause()
					this.hasPlay = false
					if(this.interval) clearInterval(this.interval)
				}else{
					this.audio.play()
					this.hasPlay = true
					this.interval = setInterval(()=>{
						this.currentTime = this.getTime(this.audio.currentTime)
						if(this.audio.currentTime >= this.audio.duration){
							this.audio.seek(0)
							this.hasPlay = false
							clearInterval(this.interval)
						}
						this.percent = parseInt((this.audio.currentTime / this.audio.duration)*100)
						
					},1000)
				}
			},
			getTime(result){
				let h = Math.floor(result / 3600) < 10 ? '0'+Math.floor(result / 3600) : Math.floor(result / 3600);
				let m = Math.floor((result / 60 % 60)) < 10 ? '0' + Math.floor((result / 60 % 60)) : Math.floor((result / 60 % 60));
				let s = Math.floor((result % 60)) < 10 ? '0' + Math.floor((result % 60)) : Math.floor((result % 60));
				if(h == '00'){
					return  m + ":" + s;
				}else{
					return  h + ":" + m + ":" + s;
				}
				
			},
			clickProgress(e){
				this.$u.throttle(()=>{
					
					const _this = this
					const x = e.detail.x
					let view = uni.createSelectorQuery().in(this);
					view.select('.progress').boundingClientRect();
					view.exec(res => {
						const left = res[0].left
						this.percent = parseInt(x - left)
						_this.audio.seek((x - left) * this.audio.duration / 100)
					})
					if(this.audio.paused) this.control()
				},500)
			}
			
		},
		destroyed() {
			this.audio.destroy()
			if(this.interval) clearInterval(this.interval)
		},
		computed:{
			i18n(){
				return this.$t("common")
			}
		}
	}
</script>

<style lang="scss">

</style>
