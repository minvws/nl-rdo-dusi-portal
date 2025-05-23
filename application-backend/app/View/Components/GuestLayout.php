<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Application\Backend\View\Components;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class GuestLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     *
     * @return Application|Factory|View|\Illuminate\View\View
     */
    public function render()
    {
        return view('layouts.guest');
    }
}
