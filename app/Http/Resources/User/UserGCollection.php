<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use App\Http\Resources\User\UserGResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserGCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            "data" => UserGResource::collection($this->collection),
        ];
    }
}
