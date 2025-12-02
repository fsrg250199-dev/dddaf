<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Route;

class NavLink extends Component
{
    public $href;
    public $icon;

    /**
     * Create a new component instance.
     */
    public function __construct($href, $icon)
    {
        $this->href = $href;
        $this->icon = $icon;
    }

    /**
     * Determine if the link is active.
     */
    public function isActive()
    {
        return request()->url() === $this->href || request()->routeIs($this->href);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.nav-link');
    }
}
