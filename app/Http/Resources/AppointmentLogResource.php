<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $result = parent::toArray($request);
        $result['appointment'] = new AppointmentResource($this->resource->appointment);
        $result['owner'] = new UserResource($this->resource->owner);
        $result['created'] = $this->resource->created_at->diffForHumans();
        return $result;
    }
}
