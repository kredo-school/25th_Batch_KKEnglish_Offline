<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Material;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class MaterialController extends Controller
{
    // 閲覧
    public function index(): View
    {
        $materials = Material::query()->latest('material_id')->paginate(20);
        return view('materials.index', compact('materials'));
    }

    public function show(Material $material): View
    {
        return view('materials.show', compact('material'));
    }

    // admin編集
    public function create(): View
    {
        return view('materials.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'file_path' => ['required', 'string', 'max:255'],
        ]);

        Material::create($validated);

        return redirect()->route('materials.index')->with('success', '教材を作成しました。');
    }

    public function edit(Material $material): View
    {
        return view('materials.edit', compact('material'));
    }

    public function update(Request $request, Material $material): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'file_path' => ['required', 'string', 'max:255'],
        ]);

        $material->update($validated);

        return redirect()->route('materials.show', $material)->with('success', '教材を更新しました。');
    }

    public function destroy(Material $material): RedirectResponse
    {
        $material->delete();

        return redirect()->route('materials.index')->with('success', '教材を削除しました。');
    }
}
