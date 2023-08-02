<?php

namespace App\Http\Resources\Ecommerce\Sale;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->resource->id,
            "method_payment" => $this->resource->method_payment,
            "currency_payment" => $this->resource->currency_payment,
            "total" => $this->resource->total,
            "n_transaccion" => $this->resource->n_transaccion,
            "sale_details" => $this->resource->sale_details->map(function($sd){
                return [
                    "id" => $sd->id,
                    "course" => [
                        "id" => $sd->course->id,
                        "title" => $sd->course->title,
                        "imagen" => env("APP_URL")."storage/".$sd->course->imagen
                    ],
                    "type_discount" => $sd->type_discount,
                    "discount" => $sd->discount,
                    "type_campaing" => $sd->type_campaing,
                    "code_cupon" => $sd->code_cupon,
                    "code_discount" => $sd->code_discount,
                    "precio_unitario" => $sd->precio_unitario,
                    "total" => $sd->total,
                    "created_at" => Carbon::parse($sd->created_at)->format("Y-m-d h:i:s")
                ];
            })
        ];
    }
}
