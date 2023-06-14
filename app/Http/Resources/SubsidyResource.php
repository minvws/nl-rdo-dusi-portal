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
        ];

        if (isset($this->publishedForm)) {
            $data['_links'] = [
                'form' => ['href' => route('api.form-show', ['id' => $this->publishedForm->id])]
            ];
        }

        return $data;
    }
}
