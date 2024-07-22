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
            ids: this.cartIds
        })
    },

    computed: {
        cartIds() {
            return this.$root.cart.items.map((item) => item.product.id)
        }
    }
}
</script>
