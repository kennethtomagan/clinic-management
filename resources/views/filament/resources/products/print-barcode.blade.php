<div class="p-4">
    <h3 class="text-lg font-semibold">{{ $product->name }}</h3>
    <img src="data:image/png;base64,{{ $barcode }}" alt="Barcode for {{ $product->name }}" class="mt-4" />
    <button onclick="window.print();" >
        Print Barcode
    </button>
</div>