<?php

namespace App\Http\Resources\Coach;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CoachApplicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'status'           => $this->status,
            'description'      => $this->description,
            'rejection_reason' => $this->rejection_reason,
            'reviewed_at'      => $this->reviewed_at,
            'documents'        => CoachApplicationDocumentResource::collection($this->whenLoaded('documents')),
            'submitted_at'     => $this->created_at,
        ];
    }
}
