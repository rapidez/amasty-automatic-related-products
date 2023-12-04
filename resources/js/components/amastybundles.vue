<script>
    import { mask, refreshMask } from 'Vendor/rapidez/core/resources/js/stores/useMask'

    export default {
        props: {
            mainProduct: Object,
            bundle: Object,
            addedDuration: {
                type: Number,
                default: 3000,
            }
        },

        data: () => ({
            selectedProducts: [],
            options: {},
            adding: false,
            added: false,
        }),

        render() {
            return this.$scopedSlots.default({
                bundlePrice: this.bundlePrice,
                oldBundlePrice: this.oldBundlePrice,
                bundleDiscountAmount: this.bundleDiscountAmount,
                selectedProducts: this.selectedProducts,
                addToCart: this.addToCart,
                options: this.options,
                adding: this.adding,
                added: this.added,
                itemDiscount: this.itemDiscount,
                itemDiscountedPrice: this.itemDiscountedPrice,
                itemPrice: this.itemPrice,
                mainProductPrice: this.mainProductPrice,
                mainProductDiscount: this.mainProductDiscount,
                mainProductDiscountedPrice: this.mainProductDiscountedPrice
            });
        },

        created() {
            Object.keys(this.bundle.items).forEach(() => this.selectedProducts.push(true))
        },

        mounted() {
            this.$root.$on('product-super-attribute-change', (product) => {
                if (this.mainProduct.configurable_options) {
                    let values = {}
                    this.mainProduct.configurable_options.forEach(function (option) {
                        values[option.attribute_id_v2] = product[option.attribute_code]
                    })
                    this.options = values
                }
            })
        },

        methods: {
            async addToCart() {
                this.added = false
                this.adding = true

                if (!mask.value) {
                    await refreshMask()
                }

                try {
                    Object.entries(this.selectedProducts).forEach(async ([itemIndex, itemChecked]) => {
                        if (itemChecked) {
                            let product = this.bundle.items[itemIndex]
                            await this.magentoCart('post', 'items', {
                                cartItem: {
                                    sku: product.product.sku,
                                    qty: 1,
                                    quote_id: mask.value
                                }
                            })
                        }
                    })

                    let response = await this.magentoCart('post', 'items', {
                        cartItem: {
                            sku: this.mainProduct.sku,
                            qty: 1,
                            quote_id: mask.value,
                            product_option: this.productOptions
                        }
                    })

                    // Just a workaround to make sure all calculations are triggered.
                    await this.magentoCart('put', 'items/' + response.data.item_id, {
                        cartItem: {
                            quote_id: mask.value,
                            qty: response.data.qty
                        }
                    })

                    await this.refreshCart()

                    this.added = true
                    setTimeout(() => { this.added = false }, this.addedDuration)
                } catch (error) {
                    Notify(error.response.data.message, 'error')
                }

                this.adding = false
            },

            discountMultiplier(percentage) {
                return (1 - (percentage / 100).toFixed(2))
            },

            itemDiscount(index) {
                if (!this.bundle || this.bundle.items[index].discount_amount <= 0) {
                    return false
                }

                return this.bundle.discount_type ? (this.itemPrice(index) / 100 * this.bundle.items[index].discount_amount).toFixed(2) : this.bundle.items[index].discount_amount
            },

            itemDiscountedPrice(index) {
                return this.itemDiscount(index) ? this.itemPrice(index) - this.itemDiscount(index) : false
            },

            itemPrice(index) {
                return this.bundle.items[index].product.price_range.maximum_price.regular_price.value
            }
        },

        computed: {
            mainProductPrice: function () {
                return this.mainProduct.price_range.maximum_price.regular_price.value
            },

            mainProductDiscount: function () {
                if (!this.bundle.apply_for_parent) {
                    return false
                }

                return this.bundle.discount_type ? (this.mainProductPrice / 100 * this.bundle.discount_amount).toFixed(2) : this.bundle.discount_amount
            },

            mainProductDiscountedPrice: function () {
                return this.mainProductDiscount ? this.mainProductPrice - this.mainProductDiscount : false
            },

            bundlePrice: function() {
                let price = 0
                // This options is not available through GraphQL,
                // until it's implemented by Amasty we use 1.
                let apply_condition = 1
                let conditionsAreNotMet = apply_condition && this.selectedProducts.length < 1

                if (!Object.values(this.selectedProducts).filter(Boolean).length) {
                    return this.mainProductPrice
                }

                if (!this.bundle.apply_for_parent || conditionsAreNotMet) {
                    price += this.mainProductPrice
                } else {
                    price += this.mainProductDiscountedPrice
                }

                Object.entries(this.selectedProducts).forEach(([itemKey, itemSelected]) => {
                    if (itemSelected) {
                        price += conditionsAreNotMet ? this.itemPrice(itemKey) : this.itemDiscountedPrice(itemKey)
                    }
                })

                return price
            },

            oldBundlePrice: function() {
                let price = this.mainProductPrice

                Object.entries(this.selectedProducts).forEach(([itemKey, itemSelected]) => {
                    if (itemSelected) {
                        price += this.itemPrice(itemKey)
                    }
                })

                return price
            },

            bundleDiscountAmount: function() {
                return this.oldBundlePrice > this.bundlePrice ? this.oldBundlePrice - this.bundlePrice : false
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
