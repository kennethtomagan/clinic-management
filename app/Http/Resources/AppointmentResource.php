<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'date' => $this->resource->date->format('Y/m/d'),
            'description' => $this->resource->description,
            'appointment_id' => $this->resource->appointment_id,
            'clinic' => $this->resource->clinic,
            'status' => $this->resource->status,
            'slot' => new TimeSlotResource($this->resource->slot),
            'doctor' => new DoctorResource($this->resource->doctor),

        ];
        // return parent::toArray($request);
    }
}
