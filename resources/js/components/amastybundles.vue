<script>
    import GetCart from 'Vendor/rapidez/core/resources/js/components/Cart/mixins/GetCart.js'
    export default {
        mixins: [GetCart],

        props: ['bundle'],

        data: () => ({
            selectedProducts: [],
            options: {}
        }),

        render() {
            return this.$scopedSlots.default({
                bundlePrice: this.bundlePrice,
                bundleDiscountAmount: this.bundleDiscountAmount,
                bundleDiscountPercentage: this.bundleDiscountPercentage,

                selectedProducts: this.selectedProducts,
                addToCart: this.addToCart,
                options: this.options,
            });
        },

        created() {
            Object.keys(this.bundle.items).forEach(([key, val]) => {
                this.selectedProducts.push(true)
            })
        },

        methods: {
            async addToCart() {
                await this.getMask()

                Object.entries(this.selectedProducts).forEach(async ([itemIndex, itemChecked]) => {
                    if (itemChecked) {
                        let product = this.bundle.items[itemIndex]
                        await this.magentoCart('post', 'items', {
                            cartItem: {
                                sku: product.product.sku,
                                qty: 1,
                                quote_id: localStorage.mask
                            }
                        })
                    }
                })

                await this.magentoCart('post', 'items', {
                    cartItem: {
                        sku: config.product.sku,
                        qty: 1,
                        quote_id: localStorage.mask,
                        product_option: this.productOptions
                    }
                })

                await this.refreshCart()
            }
        },

        computed: {
            bundlePrice: function() {
                let price = 0;

                Object.entries(this.selectedProducts).forEach(([key, val]) => {
                    if (val && this.bundle.discount_type === 0) {
                        price += (this.bundle.items[key].product.price_range.maximum_price.regular_price.value - this.bundle.items[key].discount_amount)
                    } else if (val && this.bundle.discount_type === 1) {
                        price += this.bundle.items[key].product.price_range.maximum_price.regular_price.value
                    }
                })

                if (this.bundle.discount_type) {
                    price = price * (1 - (this.bundle.discount_amount / 100))
                }

                return price + parseFloat(config.product.price)
            },
            bundleDiscountAmount: function() {
                let amount = 0;
                Object.entries(this.selectedProducts).forEach(([key, val]) => {
                    if(val) {
                        amount += this.bundle.items[key].discount_amount
                    }
                })

                return amount
            },
            bundleDiscountPercentage: function () {
                let discount = 0
                Object.entries(this.selectedProducts).forEach(([key, val]) => {
                    discount += this.bundle.items[key].product.price_range.maximum_price.regular_price.value
                })

                return discount * (this.bundle.discount_amount / 100)
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
