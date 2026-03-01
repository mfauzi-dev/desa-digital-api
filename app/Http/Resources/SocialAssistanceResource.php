<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SocialAssistanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'thumbnail' => $this->thumbnail ? asset('storage/' . $this->thumbnail) : null,
            'name' => $this->name,
            'category' => $this->category,
            'amount' =>  [
                'unformatted' => $this->amount,
                'formatted' => number_format($this->amount, 2, ',', '.')
            ],
            'provider' => $this->provider,
            'description' => $this->description,
            'is_available' => $this->is_available
        ];
    }
}
