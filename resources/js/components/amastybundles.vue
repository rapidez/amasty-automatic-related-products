<script>
	import GetCart from 'Vendor/rapidez/core/resources/js/components/Cart/mixins/GetCart.js'
	export default {
		props: ['products', 'main_product'],
		name: 'bundles',
		mixins: [GetCart],
		data: () => ({
			productBoxes: [],
			options: {}
		}),
		render() {
			return this.$scopedSlots.default({
				discount_amount: this.discount_amount,
				changeSelected: this.changeSelected,
				price: this.price,
				addToCart: this.addToCart,
				productBoxes: this.productBoxes,
				options: this.options,
				main_product: this.main_product
			});
		},
		created() {
			Object.keys(this.products).forEach(([key, val]) => {
				this.productBoxes.push(true)
			})

			let self = this
			this.$root.$on('refresh-cart', function() {
				self.refreshCart()
			})
		},
		methods: {
			async addToCart() {
				this.add().then((response) => {
					this.$root.$emit('refresh-cart')
				})	
			},
			async add() {
				Object.entries(this.productBoxes).forEach(async([key, val]) => {
					if(val) {
						let product = this.products[key]
						this.magentoCart('post', 'items', {
							cartItem: {
								sku: product.product.sku,
								qty: 1,
								quote_id: localStorage.mask
							}
						})
					}
				})
				
				this.magentoCart('post', 'items', {
					cartItem: {
						sku: this.main_product.sku,
						qty: 1,
						quote_id: localStorage.mask,
						product_option: this.productOptions
					}
				}).then((repsonse) => this.refreshCart())
			}
		},
		computed: {
			price: function() {
				let price = 0;
				Object.entries(this.productBoxes).forEach(([key, val]) => {
					if(val) {
						price += (this.products[key].product.price_range.maximum_price.regular_price.value - this.products[key].discount_amount)
					}
				})

				return price + parseFloat(this.main_product.price)
			},
			discount_amount: function() {
				let amount = 0;
				Object.entries(this.productBoxes).forEach(([key, val]) => {
					if(val) {
						amount += this.products[key].discount_amount
					}
				})

				return amount
			},
			productOptions: function () {
				let options = []
				Object.entries(this.options).forEach(([key, val]) => {
					options.push({
						option_id: key,
						option_value: val,
					});
				});
				return {
					extension_attributes: {
						configurable_item_options: options
					}
				}
			}
		}	
	}
</script>