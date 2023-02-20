import 'Vendor/rapidez/core/resources/js/vue'

(() => {
    const components = {
        ...import.meta.glob(['./components/*.vue', '!./components/*.lazy.vue'], { eager: true, import: 'default' }),
        ...import.meta.glob(['./components/*.lazy.vue'], { eager: false, import: 'default' })
    };
    for (const path in components) {
        Vue.component(path.split('/').pop().split('.').shift(), components[path])
    }
})();
