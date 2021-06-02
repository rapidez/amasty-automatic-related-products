@props(['product'])

<graphql v-cloak query='@include('amastymostviewed::queries.bundles', ['product_id' => $product->id])'>
	<div slot-scope="{ data }" v-if="data">
		<div v-for="(bundle, index) in data.amMostviewedBundlePacks.items">
			<bundles :products="bundle.items" :main_product="{{json_encode($product)}}">
				<div class="flex" slot-scope="{ price, discount_amount, changeSelected,productBoxes, addToCart, options, main_product }">
					<div class="w-1/3" v-if="index == 0 && data.amMostviewedBundlePacks.main_product">
						<div class="px-1 my-1">
							<div class="w-full bg-white rounded hover:shadow group relative">
								<div class="block">
									<img v-if="main_product.gallery[0].value" :src="config.magento_url + main_product.gallery[0].value" class="object-contain rounded-t h-48 w-full mb-3" :alt="data.amMostviewedBundlePacks.main_product.name" loading="lazy" />
									<x-rapidez::no-image v-else class="rounded-t h-48 mb-3"/>
									<div class="px-2">
										<div class="hyphens">
											@{{ data.amMostviewedBundlePacks.main_product.name }}
										</div>

										<div class="font-semibold">
											@{{ main_product.price | price}}
										</div>
										<div>
											@if($product->super_attributes)
									       		@foreach($product->super_attributes as $id=>$code)
											        <x-rapidez::select
                                                        v-bind:id="'super_attribute_'+{{$id}}"
                                                        v-bind:name="superAttributeId"
                                                        v-model="options[{{$id}}]"
                                                        class="block w-64 mb-3"
                                                    >
                                                        <option disabled selected hidden :value="undefined">
                                                            @lang('Select') {{ $code->label}}
                                                        </option>
                                                        @foreach($product[$code->code] as $code=>$label)
                                                            <option value="{{ $code }}">{{ $label }}</option>
                                                        @endforeach
                                                    </x-rapidez::select>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-row overflow-x-scroll">
                        <div class="w-1/3 border border-gray-200 hover:shadow" v-for="(product, index) in bundle.items" :key="index">
                            <div class="px-1 my-1">
                                <div class="w-full bg-white rounded group relative">
                                    <div class="block">
                                        <img v-if="product.product.thumbnail.url" :src="product.product.thumbnail.url" class="object-contain rounded-t h-48 w-full mb-3" :alt="product.product.name" loading="lazy" />
                                        <x-rapidez::no-image v-else class="rounded-t h-48 mb-3"/>
                                        <input type="checkbox" v-model.lazy="productBoxes[index]" :value="product" />

                                        <div class="px-2">
                                            <div class="hyphens">
                                                @{{ product.product.name }}
                                            </div>
                                            <p class="line-through">
                                                @{{ product.product.price_range.maximum_price.regular_price.value | price }}
                                            </p>
                                            <div class="font-semibold">
                                                @{{ product.product.price_range.maximum_price.regular_price.value - product.discount_amount  | price}}
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
                                            @{{ price | price }}
                                        </span>
                                        <div class="justify-self-end mb-1">
                                            <span>
                                                @lang('You\'re saving')
                                                @{{ discount_amount | price }}
                                            </span>
                                            <button
                                                class="inline-block font-semibold py-2 px-4 border rounded disabled:opacity-50 disabled:cursor-not-allowed hover:opacity-75 bg-primary border-primary text-white"
                                                :disabled="$root.loading"
                                                @click="addToCart()"
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
            </bundles>
        </div>
    </div>
</graphql>
