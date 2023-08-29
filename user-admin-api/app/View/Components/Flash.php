<?php

declare(strict_types=1);

namespace MinVWS\DUSi\User\Admin\API\View\Components;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Flash extends Component
{
    /**
     * Get the view / contents that represent the component.
     *
     * @return View|Factory
     */
    public function render(): Factory|View
    {
        return view('components.flash');
    }

    public function addNotificationTypeSpan(string $type): bool
    {
        return in_array($type, ['error', 'confirmation', 'warning', 'explanation']);
    }

    public function getNotificationTypeSpan(string $type): string
    {
        return match ($type) {
            'error' => __('Error') . ": ",
            'confirmation' => __('Confirmation') . ": ",
            'warning' => __('Warning') . ": ",
            'explanation' => __('Explanation') . ": ",
            default => '',
        };
    }
}
