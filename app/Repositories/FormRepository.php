<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\Form;
use Illuminate\Support\Collection;

class FormRepository
{
    public function getActiveForms(): Collection
    {
        return collect([$this->getForm('123')]);
    }

    public function getForm(string $id): ?Form
    {
        $form = new Form();
        $form->id = $id;
        $form->title = 'Example Form';
        return $form;
    }
}
