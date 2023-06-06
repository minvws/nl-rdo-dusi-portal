<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Models\Form;

class FormKeyHelper
{
    public function keyForList(): string
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
