@props(['product'])

<graphql v-cloak query='@include('amastyrelatedproducts::queries.bundles', ['product_id' => $product->id])'>
    <div slot-scope="{ data }" v-if="data">
        <div v-for="bundle in data.amMostviewedBundlePacks.items">
            <amastybundles :bundle="bundle">
                <div class="flex" slot-scope="{ bundlePrice, bundleDiscountAmount, bundleDiscountPercentage, selectedProducts, addToCart, options }">
                    <div class="w-1/3">
                        <div class="px-1 my-1">
                            <div class="w-full bg-white rounded hover:shadow group relative">
                                <div class="block">
                                    @if(!empty($product->images))
                                        <img
                                            src="/storage/resizes/200/catalog/product{{ $product->images[0] }}"
                                            class="object-contain rounded-t h-48 w-full mb-3"
                                            alt="{{ $product->name }}"
                                            loading="lazy"
                                        />
                                    @else
                                        <x-rapidez::no-image class="rounded-t h-48 mb-3"/>
                                    @endif
                                    <div class="px-2">
                                        <div class="hyphens">{{ $product->name }}</div>
                                        <div class="font-semibold">{{ $product->price }}</div>
                                        @if($product->super_attributes)
                                            <div>
                                                @foreach($product->super_attributes as $superAttributeId => $superAttribute)
                                                    <x-rapidez::select
                                                        v-bind:id="'super_attribute_'+{{ $superAttributeId }}"
                                                        v-model="options[{{ $superAttributeId }}]"
                                                        class="block w-64 mb-3"
                                                    >
                                                        <option disabled selected hidden :value="undefined">
                                                            @lang('Select') {{ strtolower($superAttribute->label) }}
                                                        </option>
                                                        @foreach($product[$superAttribute->code] as $superAttribute => $label)
                                                            <option value="{{ $superAttribute }}">{{ $label }}</option>
                                                        @endforeach
                                                    </x-rapidez::select>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-row overflow-x-scroll">
                        <div class="sm:min-w-1/2 md:min-w-1/3 border border-gray-200 hover:shadow" v-for="(item, index) in bundle.items" :key="index">
                            <div class="px-1 my-1">
                                <div class="w-full bg-white rounded group relative">
                                    <div class="block">
                                        <img
                                            v-if="item.product.thumbnail.url"
                                            :src="'/storage/resizes/200'+item.product.thumbnail.url.replace(config.magento_url+'/media', '')"
                                            class="object-contain rounded-t h-48 w-full mb-3"
                                            :alt="item.product.name"
                                            loading="lazy"
                                        />
                                        <x-rapidez::no-image v-else class="rounded-t h-48 mb-3"/>
                                        <input type="checkbox" v-model.lazy="selectedProducts[index]" />

                                        <div class="px-2">
                                            <div class="hyphens">
                                                <a :href="item.product.canonical_url">
                                                    @{{ item.product.name }}
                                                </a>
                                            </div>
                                            <p class="line-through">
                                                @{{ item.product.price_range.maximum_price.regular_price.value | price }}
                                            </p>
                                            <div class="font-semibold">
                                                @{{ item.product.price_range.maximum_price.regular_price.value - item.discount_amount | price}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="w-1/3">
                        <div class="px-1 my-1 h-full">
                            <div class="w-full bg-white rounded group relative h-full">
                                <div class="block h-full">
                                    <div class="px-2 flex flex-col justify-between h-full">
                                        <div class="hyphens">@{{ bundle.block_title }}</div>
                                        <span class="font-extrabold text-2xl mb-3">
                                            @{{ bundlePrice | price }}
                                        </span>
                                        <div class="justify-self-end mb-1">
                                            <span v-if="!bundle.discount_type">
                                                @lang('You\'re saving')
                                                @{{ bundleDiscountAmount | price }}
                                            </span>
                                            <span v-if="bundle.discount_type">
                                                @{{ bundleDiscountPercentage | price}}
                                            </span>
                                            @{{ selectedProducts }}
                                            <button
                                                class="inline-block font-semibold py-2 px-4 border rounded disabled:opacity-50 disabled:cursor-not-allowed hover:opacity-75 bg-primary border-primary text-white"
                                                :disabled="$root.loading"
                                                v-on:click="addToCart"
                                            >
                                                <svg v-if="$root.loading" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="spinner" class="w-5 h-5 animate-spin absolute left-5 top-4 mr-4" role="img" viewBox="0 0 512 512"><path fill="currentColor" d="M304 48c0 26.51-21.49 48-48 48s-48-21.49-48-48 21.49-48 48-48 48 21.49 48 48zm-48 368c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.49-48-48-48zm208-208c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.49-48-48-48zM96 256c0-26.51-21.49-48-48-48S0 229.49 0 256s21.49 48 48 48 48-21.49 48-48zm12.922 99.078c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48c0-26.509-21.491-48-48-48zm294.156 0c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48c0-26.509-21.49-48-48-48zM108.922 60.922c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.491-48-48-48z"/></svg>
                                                @lang('Add to cart')
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </amastybundles>
        </div>
    </div>
</graphql>
