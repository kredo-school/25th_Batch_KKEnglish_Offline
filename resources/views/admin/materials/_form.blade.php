<div class="mb-3">
    <label class="form-label">Material Name</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $material->name ?? '') }}" required>
</div>
<div class="mb-3">
    <label class="form-label">Material Picture</label>
    <input type="file" name="cover_image_file" class="form-control" accept="image/*">

    @if(!empty($material?->cover_image))
        <div class="mt-2">
            <img src="{{ asset('storage/' . $material->cover_image) }}" alt="cover" width="120" class="rounded border">
        </div>
    @endif
</div>

<div class="mb-3">
    <label class="form-label">Description</label>
    <textarea name="description" class="form-control">{{ old('description', $material->description ?? '') }}</textarea>
</div>
<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">Level</label>
        <input type="text" name="level" class="form-control" value="{{ old('level', $material->level ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Target Level</label>
        <input type="text" name="target_level" class="form-control" value="{{ old('target_level', $material->target_level ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Duration</label>
        <input type="number" name="duration" class="form-control" value="{{ old('duration', $material->duration ?? '') }}">
    </div>
</div>
<div class="mb-3 mt-3">
    <label class="form-label">Status</label>
    <select name="status" class="form-select">
        <option value="active" @selected(old('status', $material->status ?? 'active')==='active')>active</option>
        <option value="inactive" @selected(old('status', $material->status ?? '')==='inactive')>inactive</option>
    </select>
</div>
