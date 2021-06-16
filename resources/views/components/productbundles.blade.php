@props(['product'])

<graphql v-cloak query='@include('amastyrelatedproducts::queries.bundles', ['product_id' => $product->id])'>
    <div slot-scope="{ data }" v-if="data">
        <div v-for="bundle in data.amMostviewedBundlePacks.items">
            <amastybundles :bundle="bundle">
                <div class="mb-3" slot-scope="{ bundlePrice, bundleDiscountAmount, selectedProducts, addToCart, options }">
                    <div class="mb-3 font-bold text-lg">@{{ bundle.block_title }}</div>
                    <form class="flex flex-col sm:flex-row" v-on:submit.prevent="addToCart">
                        <x-amastyrelatedproducts::productbundle-item
                            :name="$product->name"
                            :price="$product->formatted_price"
                            :firstProduct="true"
                        >
                            <x-slot name="image">
                                @if(!empty($product->images))
                                    <img
                                        src="/storage/resizes/200/catalog/product{{ $product->images[0] }}"
                                        class="object-contain h-40 w-full"
                                        alt="{{ $product->name }}"
                                        loading="lazy"
                                    />
                                @else
                                    <x-rapidez::no-image class="h-40"/>
                                @endif
                            </x-slot>


                            @if($product->super_attributes)
                                <div class="mt-3">
                                    @foreach($product->super_attributes as $superAttributeId => $superAttribute)
                                        <x-rapidez::select
                                            v-bind:id="'super_attribute_'+{{ $superAttributeId }}"
                                            v-model="options[{{ $superAttributeId }}]"
                                            class="block mx-auto mb-3"
                                            :label="false"
                                            required
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
                        </x-amastyrelatedproducts::productbundle-item>

                        <x-amastyrelatedproducts::productbundle-item
                            :name="'@{{ item.product.name }}'"
                            :price="'@{{ item.product.price_range.maximum_price.regular_price.value | price }}'"
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
                            <x-rapidez::button type="submit" class="flex items-center mx-auto mt-3">
                                <svg v-if="$root.loading" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="spinner" class="w-5 h-5 animate-spin mr-1" role="img" viewBox="0 0 512 512"><path fill="currentColor" d="M304 48c0 26.51-21.49 48-48 48s-48-21.49-48-48 21.49-48 48-48 48 21.49 48 48zm-48 368c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.49-48-48-48zm208-208c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.49-48-48-48zM96 256c0-26.51-21.49-48-48-48S0 229.49 0 256s21.49 48 48 48 48-21.49 48-48zm12.922 99.078c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48c0-26.509-21.491-48-48-48zm294.156 0c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48c0-26.509-21.49-48-48-48zM108.922 60.922c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.491-48-48-48z"/></svg>
                                @lang('Add to cart')
                            </x-rapidez::button>
                        </div>
                    </form>
                </div>
            </amastybundles>
        </div>
    </div>
</graphql>
