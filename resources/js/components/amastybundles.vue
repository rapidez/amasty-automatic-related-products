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
                let productsToAdd = Object.entries(this.selectedProducts).filter(([itemIndex, itemChecked]) => itemChecked);
                try {
                    productsToAdd = productsToAdd.map(([itemIndex, itemChecked]) => {
                        let product = this.bundle.items[itemIndex]
                        return {
                            sku: product.product.sku,
                            quantity: 1,
                        };
                    })

                    productsToAdd.push({
                        sku: this.mainProduct.sku,
                        quantity: 1,
                        selected_options: this.selectedOptions
                    });

                    const response = await window.magentoGraphQL(
                        `mutation (
                            $cartId: String!,
                            $cartItems: [CartItemInput!]!
                        ) { addProductsToCart(cartId: $cartId, cartItems: $cartItems) { cart { ` +
                            config.queries.cart +
                            ` } user_errors { code message } } }`,
                        {
                            cartId: mask.value,
                            cartItems: productsToAdd,
                        },
                    ).then(async (response) => {
                        await this.updateCart({}, response)

                        return response;
                    })

                    if (response.data.addProductsToCart.user_errors.length) {
                        throw new Error(response.data.addProductsToCart.user_errors[0].message)
                    }

                    this.added = true
                    setTimeout(() => { this.added = false }, this.addedDuration)
                } catch (error) {
                    error?.response && (await this.checkResponseForExpiredCart(error.response))

                    Notify(error?.response?.data?.message || error.message, 'error')
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
                if (!this.bundle.items[index]) {
                    return
                }

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

            selectedOptions: function () {
                return Object.entries(this.options).map(([optionId, optionValue]) => btoa('custom-option/' + optionId + '/' + optionValue))
            }
        }
    }
</script>
