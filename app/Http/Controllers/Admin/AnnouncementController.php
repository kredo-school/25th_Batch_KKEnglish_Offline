<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Announcement;

class AnnouncementController extends Controller
{
    public function index()
    {
        // データベースから最新順（created_atの降順）でページネーション付きで取得
        $announcements = Announcement::latest()->paginate(15);
        // 実際にはDBからデータを取得して渡します
        return view('admin.announcements.index', compact('announcements'));
    }
    public function create()
    {
        return view('admin.announcements.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'target' => 'required|string',
            'content' => 'required|string',
        ]);
        // ============================================
        // ここでDBに保存する処理を実装します
        // 例: Announcement::create($request->all());
        Announcement::create($request->all());
        // ============================================
        return redirect()->route('admin.announcements.index')
                         ->with('status', 'Created successfully.');
    }

    public function edit(Announcement $announcement)
    {
        // 編集画面を表示し、対象のお知らせデータを渡す
        return view('admin.announcements.edit', compact('announcement'));
    }
    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'target' => 'required|string',
            'content' => 'required|string',
        ]);
        // DBのデータを更新
        $announcement->update($request->all());
        return redirect()->route('admin.announcements.index')
                         ->with('status', 'Updated successfully.');
    }
    public function destroy(Announcement $announcement)
    {
        // DBからデータを削除
        $announcement->delete();
        return redirect()->route('admin.announcements.index')
                         ->with('status', 'Deleted successfully.');
    }
}
