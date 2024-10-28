<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Barcodes</title>
    <style>
        .barcode-container {
            text-align: center;
            margin: 10px;
        }
        .barcode img {
            width: 200px;
            height: 80px;
        }
        .barcode-label {
            font-size: 12px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    @foreach($barcodes as $barcode)
        <div class="barcode-container">
            <div class="barcode">
                <img src="data:image/png;base64,{{ $barcode['barcode'] }}" alt="Barcode">
            </div>
            <div class="barcode-label">
                {{ $barcode['label'] }}
            </div>
        </div>
    @endforeach
</body>
</html>
