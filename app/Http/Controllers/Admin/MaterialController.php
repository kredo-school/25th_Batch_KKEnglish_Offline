<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MaterialStoreRequest;
use App\Http\Requests\Admin\MaterialUpdateRequest;
use App\Models\Material;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        $data = $request->validated();

        if ($request->hasFile('cover_image_file')) {
            $data['cover_image'] = $request->file('cover_image_file')->store('materials', 'public');
        }

        unset($data['cover_image_file']);

        \App\Models\Material::create($data);

        return redirect()->route('admin.materials.index')->with('success', '教材を登録しました。');
    }

    public function show(Material $material): View
    {
        return view('admin.materials.show', compact('material'));
    }

    public function edit(Material $material): View
    {
        return view('admin.materials.edit', compact('material'));
    }

    public function update(MaterialUpdateRequest $request, Material $material): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('cover_image_file')) {
            if (!empty($material->cover_image) && Storage::disk('public')->exists($material->cover_image)) {
                Storage::disk('public')->delete($material->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image_file')->store('materials', 'public');
        }

        unset($data['cover_image_file']);

        $material->update($data);

        return redirect()->route('admin.materials.index')->with('success', '教材を更新しました。');
    }

    public function suspend(Material $material): RedirectResponse
    {
        $material->update(['status' => 'inactive']);

        return redirect()->route('admin.materials.index')
            ->with('success', '教材を一時停止しました。');
    }

    public function destroy(Material $material): RedirectResponse
    {
        // 画像があれば先に削除
        if (!empty($material->cover_image) && Storage::disk('public')->exists($material->cover_image)) {
            Storage::disk('public')->delete($material->cover_image);
        }

        $material->delete();

        return redirect()->route('admin.materials.index')
            ->with('success', '教材を削除しました。');
    }
}
