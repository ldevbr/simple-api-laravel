<?php

namespace App\Api\v1\Resources;

use Illuminate\Http\Request;
use App\Api\v1\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'user' => new UserResource($this->user),
            'created_at' => $this->created_at->format('Y-m-d')
        ];
    }
}
