<?php

namespace App\Http\Resources\Ecommerce\Cart;

use Illuminate\Http\Request;
use App\Http\Resources\Ecommerce\Cart\CartResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CartCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "data" => CartResource::collection($this->collection),
        ];
    }
}
