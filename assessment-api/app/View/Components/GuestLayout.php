<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Illuminate\View\Factory;

class GuestLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View|Factory
    {
        return view('layouts.guest');
    }
}
