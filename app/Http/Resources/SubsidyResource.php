<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubsidyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'validFrom' => $this->valid_from->format('Y-m-d'),
            'validTo' => $this->valid_to?->format('Y-m-d')
        ];

        if (!isset($this->publishedForm)) {
            return $data;
        }

        $data['publishedForm'] = [
            'id' => $this->publishedForm->id,
            'version' => $this->publishedForm->version
        ];

        $data['_links'] = [
            'form' => ['href' => route('api.form-show', ['form' => $this->publishedForm->id]), false]
        ];

        return $data;
    }
}
