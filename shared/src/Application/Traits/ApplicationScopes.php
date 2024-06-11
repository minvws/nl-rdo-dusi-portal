<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Traits;

use DateTime;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;

trait ApplicationScopes
{
    public function scopeForIdentity(Builder $query, Identity $identity): Builder
    {
        return $query->where('identity_id', $identity->id);
    }

    public function scopeTitle(Builder $query, string $title): Builder
    {
        return $query->where('application_title', 'LIKE', '%' . $title . '%');
    }
    public function scopeReference(Builder $query, string $title): Builder
    {
        return $query->where('reference', 'LIKE', '%' . $title . '%');
    }

    public function scopeCreatedAtFrom(Builder $query, DateTime $timestamp): Builder
    {
        return $query->where('created_at', '>=', $timestamp);
    }

    public function scopeCreatedAtTo(Builder $query, DateTime $timestamp): Builder
    {
        return $query->where('created_at', '<=', $timestamp);
    }

    public function scopeUpdatedAtFrom(Builder $query, DateTime $timestamp): Builder
    {
        return $query->where('updated_at', '>=', $timestamp);
    }

    public function scopeUpdatedAtTo(Builder $query, DateTime $timestamp): Builder
    {
        return $query->where('updated_at', '<=', $timestamp);
    }

    public function scopeFinalReviewDeadlineFrom(Builder $query, DateTime $timestamp): Builder
    {
        return $query->where('final_review_deadline', '>=', $timestamp);
    }

    public function scopeFinalReviewDeadlineTo(Builder $query, DateTime $timestamp): Builder
    {
        return $query->where('final_review_deadline', '<=', $timestamp);
    }

    /**
     * @psalm-param Builder $query
     * @param Builder<Application> $query
     * @param array<ApplicationStatus|string> $status
     * @psalm-return Builder
     * @return Builder<Application>
     */
    public function scopeStatus(Builder $query, array $status): Builder
    {
        return $query->whereIn('status', $status);
    }

    /**
     * @psalm-param Builder $query
     * @param Builder<Application> $query
     * @param array<string> $codes
     * @psalm-return Builder
     * @return Builder<Application>
     */
    public function scopeSubsidyCode(Builder $query, array $codes): Builder
    {
        return $query->whereHas('subsidyVersion.subsidy', function (Builder $q) use ($codes) {
            $q->whereIn('code', $codes);
        });
    }

    public function scopeOrderByStatus(Builder $query): Builder
    {
        return $query->orderByRaw('CASE status '
            . "WHEN 'draft' THEN 1 "
            . "WHEN 'pending' THEN 2 "
            . "WHEN 'approved' THEN 3 "
            . "WHEN 'allocated' THEN 4 "
            . "WHEN 'rejected' THEN 5 "
            . "WHEN 'reclaimed' THEN 6 "
            . 'END');
    }

    /**
     * Note that we search explicitly for a stage title as multiple subsidy stages belonging to different subsidies
     * could have the same stage title
     *
     * @psalm-param Builder $query
     * @param Builder<Application> $query
     * @param array<string> $titles
     * @psalm-return Builder
     * @return Builder<Application>
     */
    public function scopePhase(Builder $query, array $titles): Builder
    {
        return $query->whereHas('currentApplicationStage.subsidyStage', function (Builder $q) use ($titles) {
            $q->whereIn('title', $titles);
        });
    }

    /**
     * Scope applications to a specific subsidy
     *
     * @param Builder<Application> $query
     * @param string $subsidyId
     * @return Builder<Application>
     */
    public function scopeOfSubsidy(Builder $query, string $subsidyId): Builder
    {
        return $query->whereHas('subsidyVersion', function (Builder $q) use ($subsidyId) {
            $q->where('subsidy_id', $subsidyId);
        });
    }

    /**
     * Scope applications to have a valid subsidy
     *
     * @param Builder<Application> $query
     * @return Builder<Application>
     */
    public function scopeValidSubsidy(Builder $query): Builder
    {
        return $query->whereHas('subsidyVersion', function (Builder $query) {
            $query->whereHas('subsidy', function (Builder $query) {
                /** @var Builder<Subsidy> $query */
                $query->valid();
            });
        });
    }

    /**
     * Scope applications to have the last application stage not expired
     *
     * @param Builder<Application> $query
     * @return Builder<Application>
     */
    public function scopeLastApplicationStageNotExpired(Builder $query): Builder
    {
        return $query->whereHas('lastApplicationStage', function (Builder $query) {
            $query
                ->whereNull('expires_at')
                ->orWhere('expires_at', '>=', CarbonImmutable::today());
        });
    }
}
