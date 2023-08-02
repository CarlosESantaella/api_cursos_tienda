<?php

namespace App\Http\Resources\Ecommerce\Sale;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Ecommerce\Sale\SaleResource;

class SaleCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "data" => SaleResource::collection($this->collection)
        ];
    }
}
