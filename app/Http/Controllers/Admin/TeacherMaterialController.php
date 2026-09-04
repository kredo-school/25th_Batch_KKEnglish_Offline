<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherMaterialController extends Controller
{
    public function edit(Teacher $teacher)
    {
        $materials = Material::orderBy('material_id')->get(['material_id', 'name']);
        $selectedMaterialIds = $teacher->materials()->pluck('materials.material_id')->all();

        return view('admin.teachers.materials', compact('teacher', 'materials', 'selectedMaterialIds'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $data = $request->validate([
            'material_ids'   => ['nullable', 'array'],
            'material_ids.*' => ['integer', 'exists:materials,material_id'],
        ]);

        $teacher->materials()->sync($data['material_ids'] ?? []);

        return redirect()
            ->route('admin.teachers.materials.edit', $teacher)
            ->with('status', '科目割り当てを更新しました。');
    }
}
