import 'Vendor/rapidez/core/resources/js/vue'
import amastybundles from './components/amastybundles.vue'
import CrossSells from './components/CrossSells.vue'

document.addEventListener('vue:loaded', function (event) {
	const vue = event.detail.vue
	vue.component('amastybundles', amastybundles)
	vue.component('amasty-cross-sells', CrossSells)
})
