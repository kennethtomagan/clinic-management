<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Milon\Barcode\DNS1D;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $result = parent::toArray($request);
        $result['image'] = $this->getMedia('product-images')->first()?->getUrl();

        $barcode = new DNS1D();
        $result['barcode_image'] = url($barcode->getBarcodePNGPath($this->barcode, 'C39'));
        return $result;

    }
}
