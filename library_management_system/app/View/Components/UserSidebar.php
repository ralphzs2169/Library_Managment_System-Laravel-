<?php

namespace App\View\Components;

use App\Models\Category;
use Illuminate\View\Component;

class UserSidebar extends Component
{
    public $categories;

    public function __construct()
    {
        $this->categories = Category::with('genres')->orderBy('name')->get();
    }

    public function render()
    {
        return view('components.user-sidebar');
    }
}
