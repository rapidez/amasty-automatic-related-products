<script>
export default {
    render() {
        return this.$scopedSlots.default(this)
    },

    data: () => ({
        amastyIds: []
    }),

    async mounted() {
        this.amastyIds = await rapidezAPI('post', 'cart/cross-sells', {
            ids: this.productIds
        })
    },

    computed: {
        productIds() {
            return this.$root.cart.items.map((item) => item.product.id)
        }
    }
}
</script>
