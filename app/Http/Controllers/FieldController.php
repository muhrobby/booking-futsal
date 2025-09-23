<?php

namespace App\Http\Controllers;

use App\Models\Field;
use Illuminate\View\View;

class FieldController extends Controller
{
    public function index(): View
    {
        $fields = Field::query()->orderBy('name')->get();

        return view('fields.index', compact('fields'));
    }
}
