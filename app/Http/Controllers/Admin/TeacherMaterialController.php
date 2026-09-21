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
            ->route('admin.teachers.show', $teacher)
            ->with('status', '科目割り当てを更新しました。');
    }

    /**
 * Materialに割り当てる先生を選択
 */
public function editTeachers(Material $material)
    {
        $teachers = Teacher::query()
            ->with('user')
            ->orderBy('id')
            ->get();

        $selectedTeacherIds = $material->teachers()
            ->pluck('teachers.id')
            ->all();

        return view(
            'admin.materials.teachers',
            compact(
                'material',
                'teachers',
                'selectedTeacherIds'
            )
        );
    }

    /**
     * Materialの先生割り当てを保存
     */
    public function updateTeachers(Request $request, Material $material)
    {
        $data = $request->validate([
            'teacher_ids'   => ['nullable', 'array'],
            'teacher_ids.*' => ['integer', 'exists:teachers,id'],
        ]);

        $material->teachers()->sync($data['teacher_ids'] ?? []);

        return redirect()
            ->route('admin.materials.show', $material)
            ->with('success', '先生の割り当てを更新しました。');
    }
}
