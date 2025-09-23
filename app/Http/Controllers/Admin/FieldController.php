<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Field;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FieldController extends Controller
{
    public function index(): View
    {
        $fields = Field::query()->orderByDesc('created_at')->paginate(10);

        return view('admin.fields.index', compact('fields'));
    }

    public function create(): View
    {
        return view('admin.fields.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price_per_hour' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        Field::create($validated);

        return redirect()->route('admin.fields.index')->with('status', 'Lapangan berhasil ditambahkan.');
    }

    public function edit(Field $field): View
    {
        return view('admin.fields.edit', compact('field'));
    }

    public function update(Request $request, Field $field): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price_per_hour' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $field->update($validated);

        return redirect()->route('admin.fields.index')->with('status', 'Lapangan berhasil diperbarui.');
    }

    public function destroy(Field $field): RedirectResponse
    {
        $field->delete();

        return redirect()->route('admin.fields.index')->with('status', 'Lapangan dihapus.');
    }
}
