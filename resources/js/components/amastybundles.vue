<script>
    import GetCart from 'Vendor/rapidez/core/resources/js/components/Cart/mixins/GetCart.js'
    export default {
        mixins: [GetCart],

        props: ['mainProduct', 'bundle'],

        data: () => ({
            selectedProducts: [],
            options: {}
        }),

        render() {
            return this.$scopedSlots.default({
                bundlePrice: this.bundlePrice,
                bundleDiscountAmount: this.bundleDiscountAmount,

                selectedProducts: this.selectedProducts,
                addToCart: this.addToCart,
                options: this.options,
            });
        },

        created() {
            Object.keys(this.bundle.items).forEach(() => this.selectedProducts.push(true))
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

                let response = await this.magentoCart('post', 'items', {
                    cartItem: {
                        sku: this.mainProduct.sku,
                        qty: 1,
                        quote_id: localStorage.mask,
                        product_option: this.productOptions
                    }
                })

                // Just a workaround to make sure all calculations are triggered.
                await this.magentoCart('put', 'items/' + response.data.item_id, {
                    cartItem: {
                        quote_id: localStorage.mask,
                        qty: response.data.qty
                    }
                })

                await this.refreshCart()
            },

            discountMultiplier(percentage) {
                return (1 - (percentage / 100))
            }
        },

        computed: {
            mainProductPrice: function () {
                return parseFloat(this.mainProduct.price_range.maximum_price.regular_price.value)
            },

            bundlePrice: function() {
                let price = 0
                // This options is not available through GraphQL,
                // until it's implemented by Amasty we use 1.
                let apply_condition = 1
                let conditionsAreNotMet = apply_condition
                    && this.selectedProducts.length != Object.values(this.selectedProducts).filter(Boolean).length

                if (!Object.values(this.selectedProducts).filter(Boolean).length) {
                    return this.mainProductPrice
                }

                if (!this.bundle.apply_for_parent || conditionsAreNotMet) {
                    price += this.mainProductPrice
                } else {
                    price += this.bundle.discount_type
                        ? this.mainProductPrice * this.discountMultiplier(this.bundle.discount_amount)
                        : this.mainProductPrice - this.bundle.discount_amount
                }

                Object.entries(this.selectedProducts).forEach(([itemKey, itemSelected]) => {
                    if (itemSelected) {
                        let productPrice = this.bundle.items[itemKey].product.price_range.maximum_price.regular_price.value
                        let itemDiscount = this.bundle.items[itemKey].discount_amount ?? this.bundle.discount_amount

                        if (conditionsAreNotMet) {
                            price += productPrice
                        } else {
                            price += this.bundle.discount_type
                                ? productPrice * this.discountMultiplier(itemDiscount)
                                : productPrice - itemDiscount
                        }
                    }
                })

                return price
            },

            bundleDiscountAmount: function() {
                let productPricesSummed = this.mainProductPrice

                Object.entries(this.selectedProducts).forEach(([itemKey, itemSelected]) => {
                    if (itemSelected) {
                        let productPrice = this.bundle.items[itemKey].product.price_range.maximum_price.regular_price.value

                        productPricesSummed += productPrice
                    }
                })

                return Math.abs(this.bundlePrice - productPricesSummed)
            },

            productOptions: function () {
                let options = []

                Object.entries(this.options).forEach(([key, val]) => {
                    options.push({
                        option_id: key,
                        option_value: val,
                    })
                })

                return {
                    extension_attributes: {
                        configurable_item_options: options
                    }
                }
            }
        }
    }
</script>
