<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\DTO;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use MinVWS\DUSi\Shared\Application\Models\Application;

class ApplicationsFilter
{
    private Builder $query;
    private array $validatedData;

    public function __construct(array $validatedData)
    {
        $this->query = Application::query();
        $this->validatedData = $validatedData;
    }

    public function filterApplications(): Collection|array
    {
        $this->query->when(
            isset($this->validatedData['application_title']),
            fn () => $this->query->title($this->validatedData['application_title'])->get() // @phpstan-ignore-line
        );
        $this->query->when(
            isset($this->validatedData['date_from']),
            fn () => $this->query->createdAtFrom($this->validatedData['date_from'])->get() // @phpstan-ignore-line
        );
        $this->query->when(
            isset($this->validatedData['date_to']),
            fn () => $this->query->createdAtTo($this->validatedData['date_to'])->get() // @phpstan-ignore-line
        );
        $this->query->when(
            isset($this->validatedData['date_last_modified_from']),
            fn () => $this->query->updatedAtFrom( // @phpstan-ignore-line
                $this->validatedData['date_last_modified_from']
            )->get()
        );
        $this->query->when(
            isset($this->validatedData['date_last_modified_to']),
            fn () => $this->query->updatedAtTo( // @phpstan-ignore-line
                $this->validatedData['date_last_modified_to']
            )->get()
        );
        $this->query->when(
            isset($this->validatedData['date_final_review_deadline_from']),
            fn () => $this->query->finalReviewDeadlineFrom( // @phpstan-ignore-line
                $this->validatedData['date_final_review_deadline_from']
            )->get()
        );
        $this->query->when(
            isset($this->validatedData['date_final_review_deadline_to']),
            fn () => $this->query->finalReviewDeadlineTo( // @phpstan-ignore-line
                $this->validatedData['date_final_review_deadline_to']
            )->get()
        );
        $this->query->when(
            isset($this->validatedData['status']),
            fn () => $this->query->status($this->validatedData['status'])->get() // @phpstan-ignore-line
        );
        $this->query->when(
            isset($this->validatedData['subsidy']),
            fn () => $this->query->subsidyTitle($this->validatedData['subsidy'])->get() // @phpstan-ignore-line
        );
        return $this->query->get();
    }
}
