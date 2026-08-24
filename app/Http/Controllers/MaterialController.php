<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Material;
use Illuminate\Contracts\View\View;

class MaterialController extends Controller
{
    public function index(): View
    {
        $materials = Material::query()->select([
            'id',
            'name',
            'description',
            'file_path',
            'created_at',
            'updated_at',
        ])->latest('id')->paginate(20);

        return view('materials.index', compact('materials'));
    }
}
