<template>
	<view class="content">
		<view class="swiper">
				<swiper
					  indicator-dots
					  indicator-active-color="#FFFFFF"
					  circular
					  autoplay
					>
						<swiper-item  
						  v-for="item in goodsInfo.content_arr"
						  :key="item.id"
						>
							<image class="swiper-img" :src ="item"></image>
						</swiper-item>
					</swiper>
		</view>
		<view class="goods-content">
			<view class="goods-list" v-for="(item,index) in goodsInfo.image_arr"  v-if="index<5">
				<image :src="item"></image>
			</view>
		</view>
		<view class="goods-detail">
			<view class="money"> 
				<text class="moneys">¥{{goodsInfo.low_price}}</text>
				<text class="texts">(优惠价)</text>
				<text class="price">¥{{goodsInfo.low_original}}</text>
			</view>
			<view class="goods-names">
				{{goodsInfo.name}}
        {{goodsInfo.desc}}
			</view>
		</view>  
		<view class="goods-desc">
			<view class="title">商品详情</view>
			<view style="margin-top: 20rpx;"> 	
				<mp-html class="style-s" :content="contenet" /> 
			</view>
		</view>
		<view class="pay-bottom">
			<view class="bottomStyle" @click="show1 = true">
				<view class="shoes-icon">
					<image src="/static/index/shoes-icon.png"></image>
				</view>
				<view class="texts" >线下试穿</view>
			</view>
			<view class="button" @click="show = true">立即购买</view>
		</view>
		<u-popup :show="show1" @close="close1">
		    <view class="goods-bottoms">
				<view class="goods">
					<view class="goods-img">
						<image :src="goodsInfo.cover"></image>
					</view>
					<view class="goods-name overflow2">{{goodsInfo.name}}  {{goodsInfo.desc}}</view>
				</view>
				<view class="texts">尺码</view>
				<view class="goods-rule">
					<view class="rule-list" @click="ruleTap(index)" :class="ruleIndex==index?'classStyle':''" v-for="(item,index) in goodsInfo.item_list" :key="index">
						{{item.goods_spec}}
					</view>
				</view>
				<view class="rule-bottom">
					<view class="button" @click="fitting()" style="width: 520rpx;margin-left: 50rpx;">
						预约试穿
					</view>
				</view>
			</view>
		</u-popup>
		<u-popup :show="show" @close="close">
		    <view class="goods-bottoms">
				<view class="goods">
					<view class="goods-img">
						<image :src="goodsInfo.cover"></image>
					</view>
					<view class="goods-name overflow2">{{goodsInfo.name}}  {{goodsInfo.desc}}</view>
				</view>
				<view class="texts">尺码</view>
				<view class="goods-rule">
					<view class="rule-list" @click="ruleTaps(index)" :class="ruleIndexs==index?'classStyle':''" v-for="(item,index) in goodsInfo.item_list" :key="index">
						{{item.goods_spec}}
					</view>
				</view>
				<view class="rule-bottom">
					<view class="rule-left">
						<view class="moneys">¥{{goodsInfo.item_list[ruleIndexs].original_price}}</view>
						<view class="price">¥<span>{{goodsInfo.item_list[ruleIndexs].sell_price}}</span></view>
					</view>
					<view class="button" @click="submit">
						立即购买
					</view>
				</view>
			</view>
		</u-popup>





	</view>
</template>

<script>
	import mpHtml from 'mp-html/dist/uni-app/components/mp-html/mp-html'
	import api from 'utils/api'
	import wybNoticeBar from '@/components/wyb-noticeBar/wyb-noticeBar.vue'
	import navBar from '@/components/nav-bar/nav-bar.vue'
	export default {
		components:{wybNoticeBar,navBar,mpHtml},
		data() {
			return {
				contenet:'',
				 html:'<div>Hello World!</div>',
				 show: false,
				 show1:false,
				 ruleIndex:0,
				 ruleIndexs:0,
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
				array:['测试11', '测试11', '测试11', '测试11'],
				title: 'Hello',
				rotation: [
						
				],
				goodsInfo:'',//商品详情
			}
		},
		onLoad(options) {
			this.getGoodsDetails(options.id)
		},
		methods: {
			//立即购买
			submit(){
				let index=this.ruleIndexs
				let _goods=this.goodsInfo.item_list[index]
				
				uni.setStorageSync("goods",_goods); 
				uni.setStorageSync("goodsInfo",this.goodsInfo);
				uni.setStorageSync("status",1);
				uni.navigateTo({
					url:"/pages/orderPay/index"
				})
			},
			//预约试穿
			fitting(){
				let index=this.ruleIndex
				let _goods=this.goodsInfo.item_list[index]
				uni.setStorageSync("goods",_goods);
				uni.setStorageSync("goodsInfo",this.goodsInfo);
				uni.setStorageSync("status",0);
				uni.navigateTo({
					url:"/pages/orderPay/index"
				})
			},
			close(){
				this.show=false
			},
			close1(){
				this.show1=false
			},
			ruleTap(index){
				this.ruleIndex=index;
			},
			ruleTaps(index){
				this.ruleIndexs=index;
			},
			getGoodsDetails(id){
				let that=this;
				api.getGoodsDetail({"goods_id":id}).then((res)=>{
					if(res.code==1){
						this.goodsInfo=res.data.goods_info;
						that.contenet=res.data.goods_info.detail;
					}
				})
			},
		}
	}
</script>

<style lang="scss">
	.style-s{
		font-size: 24rpx;
	}
	.u-popup__content{
		background: none !important;
	}
	.rule-bottom{
		display: flex;
		justify-content: space-between;
		padding: 0 40rpx;
		height: 150rpx;
		margin-top: 100rpx;
		.button{
			text-align: center;
			color: #fff;
			font-size: 32rpx;
			line-height: 80rpx;
			width: 400rpx;
			height: 80rpx;
			background: #222222;
			margin-left: 20rpx;
			margin-top: 20rpx;
		}
		.rule-left{
			.moneys{
				color: #999;
				font-size: 24rpx;
				text-decoration: line-through;
			}
			.price{
				color: #222;
				font-size: 28rpx;
				margin-top: 10rpx;
				span{
					font-weight: 600;
					font-size: 40rpx;
				}
			}
		}
	}
	.goods-bottoms{
		width: 710rpx;
		// height: 620rpx;
		background: #FFFFFF;
		border-radius: 16rpx 16rpx 0px 0px;
		padding: 20rpx;
		.goods-rule{
			display: flex;
			flex-wrap: wrap;
			margin-left: -50rpx;
			.classStyle{
				border: 2rpx solid #222222;
			}
			.rule-list{
				box-sizing: border-box;
				// width: 88rpx;
				padding: 0 10rpx;
				height: 48rpx;
				background: #F4F4F4;
				border-radius: 8rpx;
				color: #222;
				font-size: 24rpx;
				margin-left: 60rpx;
				text-align: center;
				line-height: 48rpx;
				margin-bottom: 10rpx;
			}
		}
		.texts{
			font-size: 28rpx;
			color: #222;
			margin: 20rpx 0;
		}
		.goods{
			display: flex;
			.goods-name{
				margin: 20rpx 0 0 20rpx;
				padding: 0 20rpx;
				width: 500rpx;
        height: 40px;
			}
			.goods-img{
				image{
					width: 120rpx;
					height:120rpx;
				}
			}
		}
	}
	.pay-bottom{
		width: 100%;
		height: 130rpx;
		background: #FFFFFF;
		position: fixed;
		bottom: 0;
		display: flex;
		padding-top: 25rpx;
		.bottomStyle{
			width: 150rpx;
			text-align: center;
			margin-left: 40rpx;
			.texts{
				font-size: 24rpx;
				color: #222;
				font-weight: 600;
			}
			.shoes-icon{
				image{
					width: 48rpx;
					height: 48rpx;
				}
			}
		}
		.button{
			text-align: center;
			color: #fff;
			font-size: 32rpx;
			line-height: 80rpx;
			width: 500rpx;
			height: 80rpx;
			background: #222222;
			margin-left: 20rpx; 	
		}
	}
	.goods-desc{
		background-color: #fff; 
		padding: 30rpx;
		width: 610rpx; 
		margin: 0 auto;
		margin-bottom: 200rpx;
    border-radius: 16rpx;
    .title{
			color: #222;
			font-size: 28rpx;
		}
	}
	.goods-detail{
		background: #FFFFFF;
		box-shadow: 0px 4rpx 12rpx 0px rgba(231,231,231,0.41);
		border-radius: 16rpx;
		margin: 30rpx auto;
		width: 630rpx;
		padding: 25rpx;
		.goods-names{
			width: 650rpx;
			color: #222;
			font-size: 28rpx;
			margin-top: 10rpx;
		}
		.money{
			.moneys{
				color: #222;
				font-size: 40rpx;
			}
			.texts{
				color: #222;
				font-size: 20rpx;
				margin-left: 10rpx;
			}
			.price{
				font-size: 24rpx;
				color: #999;
				margin-left: 10rpx;
				text-decoration:line-through
			}
		}
	}
	.goods-content{
		padding: 0 16rpx 0 4rpx;
		display: flex;
		justify-content: left;
		margin-left: 30rpx;
		.goods-list{
			// flex: 1;
			margin-left: 12rpx;
			text-align: center;
			image{
				width: 104rpx;
				height: 104rpx;
			}
		}
	}
	swiper{
		height: 750rpx;
	}
	.swiper{
		margin: 12rpx auto;
		text-align: center;
	}
	.swiper-img{
		width: 100%;
		height: 750rpx;
		text-align: center;
		// border-radius: 16rpx;
	}
</style>
