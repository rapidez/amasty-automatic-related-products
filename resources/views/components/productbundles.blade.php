@props(['productId'])

<graphql v-cloak query="@include('amastyrelatedproducts::queries.bundles')" :variables='{ uid: "{{ base64_encode($productId) }}" }'>
    <div slot-scope="{ data }" v-if="data">
        <div v-for="bundle in data.amMostviewedBundlePacks.items">
            <amastybundles :main-product="data.amMostviewedBundlePacks.main_product" :bundle="bundle">
                <div class="mb-3" slot-scope="{ bundlePrice, bundleDiscountAmount, selectedProducts, mainProductPrice, addToCart, options, adding, added, itemPrice }">
                    <div class="mb-3 font-bold text-lg">@{{ bundle.block_title }}</div>
                    <form class="flex flex-col sm:flex-row" v-on:submit.prevent="addToCart">
                        <x-amastyrelatedproducts::productbundle-item
                            :name="'@{{ data.amMostviewedBundlePacks.main_product.name }}'"
                            :firstProduct="true"
                        >
                            <x-slot name="image">
                                <img
                                    v-if="data.amMostviewedBundlePacks.main_product.thumbnail.url"
                                    :src="'/storage/resizes/200'+data.amMostviewedBundlePacks.main_product.thumbnail.url.replace(config.magento_url+'/media', '')"
                                    class="object-contain h-40 w-full"
                                    :alt="data.amMostviewedBundlePacks.main_product.name"
                                    loading="lazy"
                                />
                                <x-rapidez::no-image v-else class="h-40"/>
                            </x-slot>

                            <div class="mt-3" v-if="data.amMostviewedBundlePacks.main_product.configurable_options">
                                <x-rapidez::input.select
                                    v-for="superAttribute in data.amMostviewedBundlePacks.main_product.configurable_options"
                                    v-bind:id="'super_attribute_'+superAttribute.attribute_id_v2"
                                    v-model="options[superAttribute.attribute_id_v2]"
                                    class="block mx-auto mb-3"
                                    :label="false"
                                    required
                                >
                                    <option disabled selected hidden :value="undefined">
                                        @lang('Select') @{{ superAttribute.label }}
                                    </option>
                                    <option
                                        v-for="value in superAttribute.values"
                                        :value="value.value_index"
                                        v-text="value.label"
                                    />
                                </x-rapidez::input.select>
                            </div>
                        </x-amastyrelatedproducts::productbundle-item>

                        <x-amastyrelatedproducts::productbundle-item
                            :name="'@{{ item.product.name }}'"
                            v-for="(item, index) in bundle.items"
                            :firstProduct="false"
                        >
                            <x-slot name="image">
                                <img
                                    v-if="item.product.thumbnail.url"
                                    :src="'/storage/resizes/200'+item.product.thumbnail.url.replace(config.magento_url+'/media', '')"
                                    class="object-contain h-40 w-full"
                                    :alt="item.product.name"
                                    loading="lazy"
                                />
                                <x-rapidez::no-image v-else class="h-40"/>
                            </x-slot>
                        </x-amastyrelatedproducts::productbundle-item>

                        <div class="flex-1 text-center sm:text-left text-8xl text-primary mx-2 sm:pt-12">
                            =
                        </div>

                        <div class="sm:w-64 text-center">
                            <div class="font-extrabold text-2xl mb-3 sm:mt-16">
                                @{{ bundlePrice | price }}
                            </div>
                            <div class="text-gray-700" v-if="bundleDiscountAmount">
                                @lang('You\'re saving')
                                @{{ bundleDiscountAmount | price }}
                            </div>
                            <x-rapidez::button type="submit" class="flex items-center mx-auto mt-3" v-bind:disabled="adding">
                                <x-heroicon-o-shopping-cart class="h-5 w-5 mr-2" v-if="!adding && !added" />
                                <x-heroicon-o-arrow-path class="h-5 w-5 mr-2 animate-spin" v-if="adding" />
                                <x-heroicon-o-check class="h-5 w-5 mr-2" v-if="added" />
                                <span v-if="!adding && !added">@lang('Add bundle')</span>
                                <span v-if="adding">@lang('Adding')...</span>
                                <span v-if="added">@lang('Added')</span>
                            </x-rapidez::button>
                        </div>
                    </form>
                </div>
            </amastybundles>
        </div>
    </div>
</graphql>
