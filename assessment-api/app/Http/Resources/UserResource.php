<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\User\Models\Role;
use MinVWS\DUSi\Shared\User\Models\User;
use MinVWS\DUSi\Shared\User\Resources\OrganisationResource;

/**
 * @mixin User
 */
class UserResource extends JsonResource
{
    /**
     * @var Collection<array-key,Subsidy>
     */
    protected Collection $subsidies;

    public function __construct($resource, Collection $subsidies)
    {
        parent::__construct($resource);
        $this->subsidies = $subsidies;
    }

    /**
     * @param Request $request
     * @return array<string, mixed>
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'organisation' => OrganisationResource::make($this->organisation),
            'roles' => $this->getRoles(),
            'passwordExpired' => $this->passwordExpired(withinDays: 1),
            'subsidies' => SubsidyResource::collection($this->subsidies),
        ];
    }

    protected function getRoles(): array
    {
        $roles = [];
        $subsidies = $this->subsidies->keyBy('id');

        $rolesForAllSubsidies = $this->roles->filter(fn($role) => $role->pivot->subsidy_id === null);
        foreach ($rolesForAllSubsidies as $role) {
            foreach ($subsidies as $subsidy) {
                $roles[] = $this->getRoleData($role, $subsidy);
            }
        }

        $rolesForSubsidy = $this->roles->filter(fn($role) => $role->pivot->subsidy_id !== null);
        foreach ($rolesForSubsidy as $role) {
            $subsidy = $subsidies->get($role->pivot->subsidy_id);
            if (!$subsidy) {
                continue;
            }
            $roles[] = $this->getRoleData($role, $subsidy);
        }

        return $roles;
    }

    protected function getRoleData(Role $role, Subsidy $subsidy): array
    {
        return [
            'name' => $role->name->value,
            'subsidy' => [
                'id' => $subsidy->id,
                'title' => $subsidy->title,
            ],
            'viewAllStages' => $role->view_all_stages,
        ];
    }
}
