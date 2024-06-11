<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Arr;

class HasOneUuidSupport extends HasOne
{
    /**
     * Get a new query for the related model, grouping the query by the given column,
     * often the foreign key of the relationship.
     *
     * This is an override of the original method to support UUID columns.
     * We only need to override the aggregatedColumn here.
     *
     * @param  string|array  $groupBy
     * @param  array<string>|null  $columns
     * @param  string|null  $aggregate
     * @return Builder
     */
    protected function newOneOfManySubQuery($groupBy, $columns = null, $aggregate = null): Builder
    {
        $subQuery = $this->query->getModel()
            ->newQuery()
            // @phpstan-ignore-next-line
            ->withoutGlobalScopes($this->removedScopes());

        foreach (Arr::wrap($groupBy) as $group) {
            $subQuery->groupBy($this->qualifyRelatedColumn($group));
        }

        if (! is_null($columns)) {
            foreach ($columns as $key => $column) {
                $qualifiedColumn = $subQuery->qualifyColumn($column);

                $aggregatedColumn = $subQuery->getQuery()->grammar->wrap($qualifiedColumn);

                if ($key === 0) {
                    $aggregatedColumn = $this->getCustomAggregatedColumn(
                        qualifiedColumn: $qualifiedColumn,
                        aggregatedColumn: $aggregatedColumn,
                        aggregate: $aggregate,
                    );
                } else {
                    $aggregatedColumn = "min({$aggregatedColumn})";
                }

                $subQuery->selectRaw(
                    $aggregatedColumn
                    . ' as '
                    . $subQuery->getQuery()->grammar->wrap($column . '_aggregate')
                );
            }
        }

        // @phpstan-ignore-next-line
        $this->addOneOfManySubQueryConstraints($subQuery, $groupBy, $columns, $aggregate);

        return $subQuery;
    }

    /**
     * Use text casts for UUID column and cast aggregated column to UUID.
     * PostgreSQL cannot MIN/MAX UUID columns directly.
     */
    protected function getCustomAggregatedColumn(
        string $qualifiedColumn,
        string $aggregatedColumn,
        ?string $aggregate = null,
    ): string {
        if ($qualifiedColumn === $this->query->getModel()->getQualifiedKeyName()) {
            return "{$aggregate}({$qualifiedColumn}::text)::uuid";
        }

        return "{$aggregate}({$aggregatedColumn})";
    }
}
