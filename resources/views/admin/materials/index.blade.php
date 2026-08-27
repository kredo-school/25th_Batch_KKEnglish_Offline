@extends('layouts.app')

@section('title', 'Material management')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Teaching Materials</h2>
        <a href="{{ route('admin.materials.create') }}" class="btn btn-primary btn-sm">＋ 新規登録</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- 教材一覧 --}}
    <div class="row g-4">
        @forelse ($materials as $material)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body d-flex align-items-start">

                        {{-- 教材画像 --}}
                        <img src="{{ $material->cover_image ? asset('storage/' . $material->cover_image) : asset('images/no-image.png') }}"
                             alt="{{ $material->name }}"
                             width="90"
                             height="90"
                             class="rounded me-3"
                             style="object-fit: cover;">

                        <div class="flex-grow-1">

                            {{-- ステータス --}}
                            <div class="mb-2">
                                <span class="badge {{ $material->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $material->status }}
                                </span>
                            </div>

                            {{-- レベル --}}
                            <span class="badge bg-primary mb-2">
                                {{ $material->level ?? 'N/A' }}
                            </span>

                            {{-- 教材名 --}}
                            <h5 class="fw-bold mb-2">
                                {{ $material->name }}
                            </h5>

                            {{-- 説明 --}}
                            <p class="text-secondary mb-3">
                                {{ \Illuminate\Support\Str::limit($material->description ?? '', 90) }}
                            </p>

                            {{-- 操作ボタン --}}
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('admin.materials.edit', $material) }}" class="btn btn-outline-primary btn-sm">
                                    編集
                                </a>

                                @if($material->status === 'active')
                                    <form method="POST" action="{{ route('admin.materials.suspend', $material) }}" onsubmit="return confirm('この教材を一時停止しますか？');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-outline-warning btn-sm">一時停止</button>
                                    </form>
                                @endif

                                <form method="POST" action="{{ route('admin.materials.destroy', $material) }}" onsubmit="return confirm('この教材を削除しますか？');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">削除</button>
                                </form>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light border text-muted mb-0">教材がありません。</div>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $materials->links() }}
    </div>

</div>
@endsection