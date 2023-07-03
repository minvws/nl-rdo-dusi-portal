<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\Answer;
use App\Models\Application;
use App\Models\Definition\Field;
use App\Models\Definition\Form;

readonly class ApplicationRepository
{
    public function makeApplication(Form $form): Application
    {
        $application = new Application();
        $application->form_id = $form->id;
        return $application;
    }

    public function saveApplication(Application $application): void
    {
        $application->save();
    }

    public function makeAnswer(Application $application, Field $field): Answer
    {
        $answer = new Answer();
        $answer->application()->associate($application);
        $answer->field_id = $field->id;
        return $answer;
    }

    public function saveAnswer(Answer $answer): void
    {
        $answer->save();
    }
}
