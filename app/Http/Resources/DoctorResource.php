<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class DoctorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $result = parent::toArray($request);
        $result['profile_image'] = $this->resource->getAvarUrl();
        $result['clinic'] = $this->resource->doctorDetail->clinic;
        $result['clinic_id'] = $this->resource->doctorDetail->clinic_id;
        $result['education'] = $this->resource->doctorDetail->education;
        $result['specialization'] = $this->resource->doctorDetail->specialization;
        $result['subspecialty'] = explode(',', $this->resource->doctorDetail->subspecialty);
        $result['years_of_experience'] = $this->resource->doctorDetail->years_of_experience;
        $result['status'] = $this->resource->doctorDetail->status;
        $result['profile_description'] = $this->resource->doctorDetail->profile_description;
        return $result;
    }
}
