<template>
	<view class="content">
		<nav-bar :navIndex="2"></nav-bar>
		<view class="user-info" @click="userTap()">
			<view class="avatar">
				<image  :src="userInfos.headimg"></image>
			</view>
			<view class="user-s">
				<view class="name">你好！</view>
				<view class="desc overflow1">{{userInfos.name}}</view>
			</view>
			<view class="rightIcon">
				<image src="../../static/index/mine-icon1.png"></image>
			</view>
		</view>
		<view class="user-info" style="height: 120rpx;">
			<view class="user-views" @click="navTaps(item.url)" v-for="(item,index) in navs" :key="index">
				<view class="icons">
					<image :src="item.imageUrl"></image>
				</view>
				<view class="name">{{item.name}}</view>
			</view>
		</view>
		<view class="titles">
			<view class="lines"></view>
			<view class="views">店铺信息</view>
			<view class="lines" style="margin-left: 20rpx;"></view>
		</view>
		<view class="index-bottoms">
			<view class="shop-banner">
				<img :src="shopInfo.app_logo" alt="">
			</view>
			<view class="shop-detail">
				<view class="views">
					<view class="shop-icons">
						<img src="/static/index/phone-icon.png" alt="">
					</view>
					<view class="texts">{{shopInfo.service_phone}}</view>
				</view>
				<view class="views">
					<view class="shop-icons">
						<img src="/static/index/eimail-icon.png" alt="">
					</view>
					<view class="texts">{{shopInfo.service_email}}</view>
				</view>
				<view class="views">
					<view class="shop-icons">
						<img src="/static/index/address-icon.png" alt="">
					</view>
					<view class="texts">{{shopInfo.app_address}}</view>
				</view>
			</view>
		</view>
	
	 

	</view>
</template>

<script>
	import api from 'utils/api'
	import wybNoticeBar from '@/components/wyb-noticeBar/wyb-noticeBar.vue'
	import navBar from '@/components/nav-bar/nav-bar.vue'
	export default {
		components:{wybNoticeBar,navBar},
		data() {
			return {
				navs:[
					{"url":1,name:"洗鞋订单","imageUrl":"/static/index/mine-icon3.png","url":"/pages/shoeList/index"},
					{"url":1,name:"我的预约","imageUrl":"/static/index/mine-icon2.png","url":"/pages/subscribeList/index"},
					{"url":1,name:"商城订单","imageUrl":"/static/index/mine-icon4.png","url":"/pages/shopList/index"}
				],
				list: [{
						iconPath: "home",
						selectedIconPath: "home-fill",
						text: '首页',
						count: 2,
						isDot: true,
						customIcon: false,
					},
					{
						iconPath: "account",
						selectedIconPath: "account-fill",
						text: '我的',
						count: 23,
						isDot: false,
						customIcon: false,
					},
				],
				current: 0,
				texts:{
					
				},
				shopInfo:'',
				array:['测试11', '测试11', '测试11', '测试11'],
				title: 'Hello',
				userInfos:'',
				rotation: [
						
				]
			}
		},
		onLoad() {
			
			
		},
		onShow() {
			
			this.getShopSet();
			this.getUserInfo();
		},
		methods: {
			navTaps(url){
				uni.navigateTo({
					url:url
				})
			},
			userTap(){
				uni.navigateTo({
					url:"/pages/userInfoUpdate/index"
				})
			},
			//获取用户信息
			getUserInfo(){
				api.getUserInfo().then((res)=>{
					console.error(res)
					if(res.code==1){
						this.userInfos=res.data.detail
					}
				})
			},
			//获取店铺设置
			getShopSet(){
			   api.getShopSet().then((res)=>{
				   if(res.code==1){
					   this.shopInfo=res.data
				   }
			   })
			},
		}
	}
</script>

<style lang="scss">
	.user-info{
		border-radius: 16rpx;
		width: 630rpx;
		height: 140rpx;
		background-color: #fff;
		display: flex;
		margin: 20rpx auto;
		padding: 30rpx;
		position: relative;
		.user-views{
			flex: 1;
			text-align: center;
			.name{
				color: #222;
				font-size: 28rpx;
				margin-top: 5rpx;
			}
			.icons{
				image{
					width: 60rpx;
					height: 60rpx;
				}
			}
		}
		.rightIcon{
			position: absolute;
			right: 30rpx;
			top: 76rpx;
			image{
				width: 40rpx;
				height: 40rpx;
			}
		}
		.user-s{
			margin: 20rpx 0 0 30rpx;
			.name{
				color: #222;
				font-size: 28rpx;
				margin-top: 5rpx;
			}
			.desc{
				color: #222;
				font-size: 32rpx;
				margin-top: 5rpx;
			}
		}
		.avatar{
			image{
				width: 128rpx;
				height: 128rpx;
				border-radius: 50%;
			}
		}
	}
	.index-bottoms{
		margin-top: 15rpx;
		padding:0 30rpx;
		.shop-detail{
			padding-bottom: 180rpx;
			margin-bottom: 100rpx;
			.views{
				margin-top: 10rpx;
				display: flex;
				line-height: 30rpx;
				.texts{
					color: #222;
					font-size: 20rpx;
					margin-left: 10rpx;
				}
				.shop-icons{
					img{
						width: 24rpx;
						height: 24rpx;
					}
				}
			}
		}
		.shop-banner{
			img{
				width: 688rpx;
				height: 284rpx;
				border-radius: 16rpx;
			}
		}
	}
	.title-texts{
		color: #222;
		font-size: 20rpx;
		text-align: center;
		margin-top: 10rpx;
	}
	.titles{
		text-align: center;
		display: flex;
		justify-content: center;
		margin-top: 35rpx;
		.lines{
			width: 18rpx;
			height: 4rpx;
			background: #000000;
			border-radius: 4rpx;
			margin: 18rpx 20rpx 0 0;
		}
		.views{
			font-size: 28rpx;
			color: #222;
		}
	}
	.contents{
		
	}
	swiper{
		height: 328rpx;
	}
	.swiper{
		margin: 12rpx auto;
		text-align: center;
	}
	.swiper-img{
		width: 684rpx;
		height: 328rpx;
		text-align: center;
		border-radius: 16rpx;
	}
</style>
