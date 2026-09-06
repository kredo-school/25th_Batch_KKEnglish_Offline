@extends('layouts.app')

@section('title', 'Admin Dashboard - Booking Schedule')

@section('content')
<style>
    /* メインコンテナ落ち防止 */
    .dashboard-main-container {
        width: 100%;
        max-width: 100%;
        min-width: 0;
    }

    /* ヘッダー・サマリーカード領域 */
    .dashboard-header-bar {
        background-color: #d1d5db;
        border-radius: 6px;
    }
    
    .summary-card-bg {
        background-color: #e5e7eb;
        border-radius: 6px;
    }

    /* タイムラインコンテナ (サイドメニューバーの右側の枠内で横スクロール) */
    .timeline-container {
        position: relative;
        width: 100%;
        max-width: 100%;
        overflow-x: auto;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        min-height: 320px;
    }
    
    /* タイムラインテーブル */
    .timeline-table {
        width: 100%;
        min-width: 1100px;
        border-collapse: collapse;
        table-layout: fixed;
    }
    .timeline-table th, .timeline-table td {
        border: 1px solid #e2e8f0;
        height: 54px;
        vertical-align: middle;
    }
    .timeline-table th {
        background-color: #f8fafc;
        text-align: center;
        font-size: 0.8rem;
        color: #475569;
        font-weight: 600;
        padding: 6px 0;
    }

    /* 固定Y軸（先生・場所名） */
    .timeline-label-col {
        width: 160px;
        position: sticky;
        left: 0;
        z-index: 10;
        background-color: #ffffff !important;
        border-right: 2px solid #cbd5e1 !important;
        padding: 4px 8px !important;
    }

    /* 予約ブロックセル */
    .timeline-cell-wrapper {
        position: relative;
        padding: 0 !important;
        height: 54px;
    }

    /* 予約ブロック */
    .booking-block {
        position: absolute;
        top: 5px;
        height: 42px;
        background-color: #ffffff;
        border: 1px solid #cbd5e1;
        border-left: 4px solid #2563eb;
        border-radius: 5px;
        padding: 2px 6px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        font-size: 0.7rem;
        line-height: 1.3;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        cursor: pointer;
        transition: transform 0.15s ease;
        z-index: 5;
    }
    .booking-block:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.15);
        z-index: 8;
    }
    .booking-block.location-type {
        border-left-color: #10b981;
    }

    /* 赤色の現在時刻線 */
    .current-time-line {
        position: absolute;
        top: 0;
        bottom: 0;
        width: 2px;
        background-color: #ef4444;
        z-index: 15;
        pointer-events: none;
    }
    .current-time-badge {
        position: absolute;
        top: -18px;
        transform: translateX(-50%);
        background-color: #ef4444;
        color: #ffffff;
        font-size: 0.65rem;
        font-weight: 700;
        padding: 1px 5px;
        border-radius: 3px;
        white-space: nowrap;
    }

    /* 切替ボタン */
    .mode-switch-btn {
        background-color: #ffffff;
        border: 1px solid #cbd5e1;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 4px 14px;
        border-radius: 20px;
        cursor: pointer;
    }
    .mode-switch-btn.active {
        background-color: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
    }
</style>

<div class="dashboard-main-container">
    <div class="row g-3 m-0">
        
        {{-- 1. 上部バー: 検索 & +新規作成 --}}
        <div class="col-12 p-0">
            <div class="dashboard-header-bar p-3">
                <div class="row align-items-center justify-content-between g-2">
                    <div class="col-md-6 col-8">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-0"><i class="fa-solid fa-magnifying-glass text-secondary"></i></span>
                            <input type="text" class="form-control border-0" placeholder="検索">
                        </div>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-dark fw-bold px-3 py-1">
                            ＋新規作成
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. サマリー領域 & モード切り替え --}}
        <div class="col-12 p-0">
            <div class="summary-card-bg p-3">
                <div class="row align-items-center mb-3 g-2">
                    <div class="col-xl-7 col-lg-6 col-md-12">
                        <h6 class="fw-bold mb-2">各時間の情報まとめ</h6>
                        <div class="d-flex flex-wrap gap-3 text-dark small">
                            <div><strong>全レッスン可能数 :</strong> {{ $capacity ?? 100 }}</div>
                            <div><strong>予約済数 :</strong> {{ $booked ?? 45 }}</div>
                            <div><strong>自動予約生徒数 :</strong> {{ $autoBooked ?? 20 }}</div>
                        </div>
                    </div>
                    
                    {{-- 切り替えボタン --}}
                    <div class="col-xl-5 col-lg-6 col-md-12 text-lg-end text-start">
                        <div class="d-inline-flex align-items-center gap-2">
                            <button type="button" id="btn-mode-teacher" class="mode-switch-btn active" onclick="switchMode('teacher')">
                                Teacher
                            </button>
                            <button type="button" id="btn-mode-location" class="mode-switch-btn" onclick="switchMode('location')">
                                Station
                            </button>
                            {{-- <span class="text-secondary small">（切り替えボタン）</span> --}}
                        </div>
                    </div>
                </div>

                {{-- 3. タイムラインテーブル --}}
                <div class="row m-0">
                    <div class="col-12 p-0">
                        <div class="timeline-container">
                            <table class="timeline-table">
                                <thead>
                                    <tr>
                                        <th class="timeline-label-col" id="mode-header-label">Teacher</th>
                                        @for($h = 0; $h < 24; $h++)
                                            <th>{{ sprintf('%02d:00', $h) }}</th>
                                        @endfor
                                    </tr>
                                </thead>
                                <tbody id="timeline-tbody">
                                    <!-- JSにて動的描画 -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

{{-- 詳細表示モーダル --}}
<div class="modal fade" id="bookingDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalBookingTitle">予約詳細</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2"><strong>時間:</strong> <span id="modalBookingTime"></span></p>
                <p class="mb-0"><strong>詳細:</strong> <span id="modalBookingDesc"></span></p>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript --}}
<script>
    const serverTeacherData = @json($teacherTimeline ?? []);
    const serverLocationData = @json($locationTimeline ?? []);

    const fallbackTeachers = [
        { id: 1, name: 'Erik Sorensen', role: 'Instructor', blocks: [{ title: 'John Doe (English A)', start_time: '04:00', end_time: '08:00', start_minutes: 240, duration_minutes: 240 }] },
        { id: 2, name: 'Maria Rossi', role: 'Instructor', blocks: [{ title: 'Jane Smith (English B)', start_time: '00:00', end_time: '04:00', start_minutes: 0, duration_minutes: 240 }, { title: 'Alex Brown (English C)', start_time: '12:00', end_time: '16:00', start_minutes: 720, duration_minutes: 240 }] },
        { id: 3, name: 'Hans Müller', role: 'Instructor', blocks: [{ title: 'Tom Wilson (Speaking)', start_time: '08:00', end_time: '12:00', start_minutes: 480, duration_minutes: 240 }] },
        { id: 4, name: 'Lucas Martin', role: 'Instructor', blocks: [] },
        { id: 5, name: 'Igor Volkov', role: 'Instructor', blocks: [{ title: 'Anna Lee (Grammar)', start_time: '07:00', end_time: '16:00', start_minutes: 420, duration_minutes: 540 }] }
    ];

    const fallbackLocations = [
        { id: 1, name: 'Station A (Room 101)', role: 'Main Building', blocks: [{ title: 'Erik Sorensen / John Doe', start_time: '04:00', end_time: '08:00', start_minutes: 240, duration_minutes: 240 }] },
        { id: 2, name: 'Station B (Room 102)', role: 'Main Building', blocks: [{ title: 'Maria Rossi / Jane Smith', start_time: '00:00', end_time: '04:00', start_minutes: 0, duration_minutes: 240 }] },
        { id: 3, name: 'Station C (Room 103)', role: 'Annex', blocks: [] },
        { id: 4, name: 'Online Booth 1', role: 'Remote', blocks: [{ title: 'Hans Müller / Tom Wilson', start_time: '08:00', end_time: '12:00', start_minutes: 480, duration_minutes: 240 }] }
    ];

    const teacherData = (serverTeacherData && serverTeacherData.length > 0) ? serverTeacherData : fallbackTeachers;
    const locationData = (serverLocationData && serverLocationData.length > 0) ? serverLocationData : fallbackLocations;

    const serverCurrentTimeMinutes = @json($currentTimeMinutes ?? null);
    const serverCurrentTimeStr = @json($currentTimeStr ?? null);

    const now = new Date();
    const currentTimeMinutes = serverCurrentTimeMinutes !== null 
        ? serverCurrentTimeMinutes 
        : (now.getHours() * 60 + now.getMinutes());

    const currentTimeStr = serverCurrentTimeStr !== null 
        ? serverCurrentTimeStr 
        : `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}`;

    let currentMode = 'teacher';

    function switchMode(mode) {
        currentMode = mode;
        document.getElementById('btn-mode-teacher').classList.toggle('active', mode === 'teacher');
        document.getElementById('btn-mode-location').classList.toggle('active', mode === 'location');
        document.getElementById('mode-header-label').innerText = mode === 'teacher' ? '先生' : '場所';
        renderTimeline();
    }

    function renderTimeline() {
        const tbody = document.getElementById('timeline-tbody');
        if (!tbody) return;
        tbody.innerHTML = '';

        const dataList = currentMode === 'teacher' ? teacherData : locationData;

        dataList.forEach((item, index) => {
            const tr = document.createElement('tr');

            // 左側Y軸ラベル
            const labelTd = document.createElement('td');
            labelTd.className = 'timeline-label-col';
            labelTd.innerHTML = `
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center fw-bold" style="width:24px; height:24px; font-size:10px; flex-shrink:0;">
                        ${(item.name || '').charAt(0)}
                    </div>
                    <div class="text-truncate">
                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.78rem;">${item.name}</div>
                        <div class="small text-muted text-truncate" style="font-size: 0.65rem;">${item.role || ''}</div>
                    </div>
                </div>
            `;
            tr.appendChild(labelTd);

            // 右側タイムラインセル
            const timelineTd = document.createElement('td');
            timelineTd.colSpan = 24;
            timelineTd.className = 'timeline-cell-wrapper';

            // 赤色の現在時刻線
            const timeRatio = (currentTimeMinutes / (24 * 60)) * 100;
            const timeLine = document.createElement('div');
            timeLine.className = 'current-time-line';
            timeLine.style.left = timeRatio + '%';
            
            if (index === 0) {
                const badge = document.createElement('div');
                badge.className = 'current-time-badge';
                badge.innerText = currentTimeStr;
                timeLine.appendChild(badge);
            }
            timelineTd.appendChild(timeLine);

            // 予定判定
            if (!item.blocks || item.blocks.length === 0) {
                const emptyMsg = document.createElement('div');
                emptyMsg.className = 'text-muted small position-absolute top-50 start-50 translate-middle';
                emptyMsg.style.pointerEvents = 'none';
                emptyMsg.style.zIndex = '4';
                emptyMsg.innerHTML = '<span class="badge bg-light text-secondary border px-2 py-1" style="font-size:0.65rem;"><i class="fa-regular fa-calendar-xmark me-1"></i>本日の予定はありません</span>';
                timelineTd.appendChild(emptyMsg);
            } else {
                item.blocks.forEach(block => {
                    const startPercent = (block.start_minutes / (24 * 60)) * 100;
                    const durationPercent = (block.duration_minutes / (24 * 60)) * 100;

                    const blockEl = document.createElement('div');
                    blockEl.className = `booking-block ${currentMode === 'location' ? 'location-type' : ''}`;
                    blockEl.style.left = startPercent + '%';
                    blockEl.style.width = Math.max(durationPercent, 4) + '%';
                    blockEl.innerHTML = `
                        <div class="fw-bold text-truncate">${block.title}</div>
                        <div class="small text-muted" style="font-size:0.65rem;"><i class="fa-regular fa-clock me-1"></i>${block.start_time} - ${block.end_time}</div>
                    `;

                    blockEl.onclick = () => showBookingDetail(block);
                    timelineTd.appendChild(blockEl);
                });
            }

            tr.appendChild(timelineTd);
            tbody.appendChild(tr);
        });
    }

    function showBookingDetail(block) {
        document.getElementById('modalBookingTitle').innerText = block.title;
        document.getElementById('modalBookingTime').innerText = `${block.start_time} - ${block.end_time}`;
        document.getElementById('modalBookingDesc').innerText = block.title;
        
        if (window.bootstrap && window.bootstrap.Modal) {
            const modal = new bootstrap.Modal(document.getElementById('bookingDetailModal'));
            modal.show();
        } else {
            alert(`予約詳細:\n${block.title}\n时间: ${block.start_time} - ${block.end_time}`);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', renderTimeline);
    } else {
        renderTimeline();
    }
</script>
@endsection