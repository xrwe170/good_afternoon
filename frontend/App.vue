<script>
	import pageAnimation from './components/page-animation'
	export default {
		mixins: [pageAnimation],
		// 此处globalData为了演示其作用，不是uView框架的一部分
		globalData: {

		},
		onLaunch() { 
			// 获取站点基本设置
			this.$u.api.common.getSiteConfig().then((res) => {
				if (res.type == 'ok') {
					this.$store.commit('setLogo', this.$store.state.baseDomain + res.message.site_logo);
					this.$store.commit('setSiteName', res.message.site_name);
				}
			})
			//设置语言
			this.$utils.setTabbar(this)
			
			//自定义缓存清理方法，应放在onLaunch最上方
			this.clearStorage()

			//检测是否登陆，如果未登录，跳转到登录页面
			//this.checkIsLogin()
			
			//开启socket
			this.$store.dispatch("startSocket")
			
			//获取法币的汇率
			this.getFiatExchageRate()
			
			
		},
		methods: {
			clearStorage() {
				//当前版本号
				const currentH5Version = 1.0
				//缓存中的版本号
				const h5Version = uni.getStorageSync('h5Version') || 0
				//如果当前版本号大于缓存中的版本号，则清除缓存
				if (currentH5Version > h5Version) {
					uni.clearStorageSync()
					//清除缓存后，保存当前版本号
					uni.setStorageSync('h5Version', currentH5Version)
				}
			},
			
			checkIsLogin() {
				const token = uni.getStorageSync('token') || ""

				if (!token) {
					this.$utils.showToast(this.$t("common.plsLogin"))
					setTimeout(() => {
						uni.redirectTo({
							url: "/pages/common/login"
						})
					}, 1200)
				}
			},
			
			getFiatExchageRate(){
				const _this = this
				const timestamp = (new Date()).valueOf()
				const cha = 60 * 60 * 1000 * 24 //12小时获取一次
				const saveTime = uni.getStorageSync('saveFiatsTime')
				if(!saveTime || timestamp - saveTime > cha){
					uni.request({
						url:'https://www.mycurrency.net/US.json',
						success({data}) {
							//美元，人民币，英镑，港币，日元，欧元，阿联酋迪拉姆，泰铢，菲律宾比索，新加坡币,越南盾
							const fiat = ['USD','CNY','GBP','HKD','JPY',"EUR","AED","THB","PHP","SGD","VND"]
							const rates = data.rates
							let list = []
							fiat.forEach(item => {
								const has = rates.find(el => el.currency_code == item)
								if(has) list.push(has)
							})
							uni.setStorageSync('saveFiatsTime',timestamp)
							_this.$store.commit('setFiats',list)
						}

					})
				}
			}
		}
	}
</script>
<style>
	page,html,body{
		background-color: #fff;
	}
	.notice-popup,.notice-popup .u-mode-center-box, .notice-popup .u-mode-center-box>uni-scroll-view>.uni-scroll-view,
	.notice-popup .u-mode-center-box>uni-scroll-view>.uni-scroll-view>.uni-scroll-view {
		overflow: unset!important;
	}
</style>
<style lang="scss">
	@import 'components/page-animation/index.css';
	@import "uview-ui/index.scss";
	@import "common/demo.scss";
	@import "static/iconfont/iconfont.css";
	@import 'static/animate.min.css';
	
	.icon {
	       width: 1em; height: 1em;
	       vertical-align: -0.15em;
	       fill: currentColor;
	       overflow: hidden;
	    }
		
	image{
		will-change: transform
	}
	
	@font-face {
		font-family: 'Din';
		src: url('static/DIN-Regular.ttf');
	}
	
	@font-face {
		font-family: 'puhui';
		src: url('static/puhui.ttf');
	}
	

	page,
	body,
	html {
		font-family: Din,puhui;
	}
</style>
