<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'address' => $this->address,
            'author' => $this->author,
            'contributions' => $this->contributions,
            'date' => $this->created_at->format('F j, Y'),
            'percentage_funded' => $this->percentage_funded,
            'raised_amount' => $this->raised_amount,
            'state' => $this->state,
            'target_amount' => $this->target_amount,
            'title' => $this->title,
        ];
    }
}
