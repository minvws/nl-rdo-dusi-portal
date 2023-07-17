<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\Answer;
use App\Models\Application;
use App\Shared\Models\Definition\Field;
use App\Shared\Models\Definition\Form;

readonly class ApplicationRepository
{
    public function getApplication(string $applicationId): ?Application
    {
        $application = Application::query()->find($applicationId);
        assert($application === null || $application instanceof Application);
        return $application;
    }

    public function makeApplicationForForm(Form $form): Application
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

    public function getAnswer(Application $application, Field $field): ?Answer
    {
        $answer =
            Answer::query()
                ->where('application_id', '=', $application->id)
                ->where('field_id', '=', $field->id)
                ->first();

        assert($answer === null || $answer instanceof Answer);
        return $answer;
    }
}
