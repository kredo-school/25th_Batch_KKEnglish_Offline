<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MaterialStoreRequest;
use App\Http\Requests\Admin\MaterialUpdateRequest;
use App\Models\Material;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index(Request $request): View
    {
        $keyword = $request->input('keyword');
        $status = $request->input('status', 'all');

        $materials = Material::query()
            ->when($keyword, fn ($q) => $q->where('name', 'like', "%{$keyword}%"))
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->orderByDesc('updated_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.materials.index', compact('materials', 'keyword', 'status'));
    }

    public function create(): View
    {
        return view('admin.materials.create');
    }

    public function store(MaterialStoreRequest $request): RedirectResponse
    {
        Material::create([
            ...$request->validated(),
            'status' => $request->input('status', 'active'),
        ]);

        return redirect()->route('admin.materials.index')
            ->with('success', '教材を登録しました。');
    }

    public function edit(Material $material): View
    {
        return view('admin.materials.edit', compact('material'));
    }

    public function update(MaterialUpdateRequest $request, Material $material): RedirectResponse
    {
        $material->update($request->validated());

        return redirect()->route('admin.materials.index')
            ->with('success', '教材を更新しました。');
    }

    public function suspend(Material $material): RedirectResponse
    {
        $material->update(['status' => 'inactive']);

        return redirect()->route('admin.materials.index')
            ->with('success', '教材を一時停止しました。');
    }
}
