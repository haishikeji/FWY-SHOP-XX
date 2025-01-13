<template>
	<view class="content">
		<view class="title">洗鞋小程序使用协议</view>
			<rich-text :nodes="contenet"></rich-text>
	</view>
</template>

<script>
	import api from 'utils/api'
	import util from 'utils/util'
	import uParse from '@/components/gaoyia-parse/parse.vue'
	export default {
		 components: {
		    uParse
		  },
		data() {
			return {
				contenet:'',
			}
		},
		onLoad() {
			api.getSysConfig({"search_name":"agreement"}).then((res)=>{
				if(res.code==1){
					this.contenet=res.data.agreement.replace(/\<img/gi, '<img style=max-width:100%;height:auto')

				}
			})
		},
		methods: {
											
		}
	}
</script>

<style lang="scss">
	.title{
		padding: 20rpx 0;
		font-size: 32rpx;
		color: #222;
		font-weight: bold;
		text-align: center;
	}
	.content{
		padding:30rpx;
		background-color: #fff;
		width: 640rpx;
		margin: 0 auto;
		border-radius: 16rpx;
	}
</style>
