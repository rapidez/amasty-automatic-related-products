<script>
import { cart } from 'Vendor/rapidez/core/resources/js/stores/useCart.js'

export default {
    render() {
        return this?.$slots?.default(this)
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
            return (cart.value?.items || []).map((item) => item.product.id)
        }
    }
}
</script>
