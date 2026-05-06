@props(['name', 'firstProduct' => false])

<div class="contents" {{ $attributes->only('v-for') }}>
    @if(!$firstProduct)
        <div class="text-center text-8xl text-primary mx-2 sm:pt-12">
            +
        </div>
    @endif

    <div {{ $attributes->except('v-for')->merge(['class' => 'w-full sm:w-1/6']) }}>
        <div class="relative">
            @if(!$firstProduct)
                <div class="absolute top-0 left-0">
                    <input type="checkbox" v-model.lazy="selectedProducts[index]" />
                </div>
            @endif
            {{ $image }}
        </div>
        <div class="text-center my-2">{{ $name }}</div>
        @if($firstProduct)
            <div class="font-bold text-center">@{{ window.price(mainProductPrice) }}</div>
        @else
            <div class="font-bold text-center">@{{ window.price(itemPrice(index)) }}</div>
        @endif
        {{ $slot }}
    </div>
</div>
