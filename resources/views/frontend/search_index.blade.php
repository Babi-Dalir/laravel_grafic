<h3>نتایج جستجو برای "{{ $q }}"</h3>

@if($products->count())
    @foreach($products as $product)
        <p>{{ $product->name }}</p>
    @endforeach

    {{ $products->links() }}
@else
    <p>محصولی یافت نشد</p>
@endif
