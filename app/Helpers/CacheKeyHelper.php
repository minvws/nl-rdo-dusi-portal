<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Shared\Models\Definition\Form;

class CacheKeyHelper
{
    public function keyForActiveSubsidies(): string
    {
        return 'form_list';
    }

    public function keyForFormId(string $id): string
    {
        return 'form_' . $id;
    }

    public function keyForForm(Form $form): string
    {
        return $this->keyForFormId($form->id);
    }
}
