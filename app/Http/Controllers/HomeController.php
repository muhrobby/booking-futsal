<?php

namespace App\Http\Controllers;

use App\Models\Field;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $fields = Field::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->take(3)
            ->get();

        return view('home', [
            'fields' => $fields,
        ]);
    }
}
