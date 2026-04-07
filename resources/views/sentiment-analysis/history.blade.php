@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<style>
    .cursor-layout {
        display: flex;
        height: calc(100vh - 72px);
        background: #ffffff;
        overflow: hidden;
    }
    .cursor-sidebar {
        width: 400px;
        background: #fafafa;
        border-right: 1px solid #e5e7eb;
        display: flex;
        flex-direction: column;
        overflow-y: auto;
        position: relative;
        z-index: 1;
    }
    .cursor-main {
        flex: 1;
        background: #ffffff;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        border-left: 1px solid #e5e7eb;
        position: relative;
    }
    .cursor-main.scrollable {
        overflow-y: auto;
        overflow-x: hidden;
    }
    .cursor-sidebar-header {
        padding: 16px;
        padding-bottom: 16px;
        border-bottom: 1px solid #e5e7eb;
        background: #ffffff;
        margin-bottom: 16px;
    }
    .cursor-sidebar-content {
        flex: 1;
        padding: 0;
        padding-bottom: 16px;
        overflow-y: auto;
        overflow-x: visible;
        display: flex;
        flex-direction: column;
        min-height: 0;
    }
    .cursor-main-content {
        flex: 1;
        padding: 24px 16px;
        max-width: 100%;
        width: 100%;
        position: relative;
        z-index: 1;
        min-height: 100%;
    }
    .cursor-section {
        margin-bottom: 24px;
        padding: 0 16px;
    }
    .cursor-section:first-of-type { padding-top: 0; }
    .cursor-section-title {
        font-size: 11px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 12px;
        padding-bottom: 6px;
        border-bottom: 1px solid #e5e7eb;
    }
    .analysis-tile {
        padding: 12px;
        padding-right: 36px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: block;
        position: relative;
    }
    .analysis-tile:hover {
        background: #f8fafc;
        border-color: #667eea;
        transform: translateX(4px);
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
    }
    .analysis-tile-title {
        font-size: 13px;
        font-weight: 500;
        color: #374151;
        line-height: 1.4;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .analysis-tile-sub {
        font-size: 11px;
        color: #9ca3af;
        margin: 4px 0 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .analysis-tile-menu {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0;
        visibility: hidden;
        transition: all 0.2s ease;
        color: #64748b;
        font-size: 16px;
        cursor: pointer;
        z-index: 10;
        padding: 4px;
        border-radius: 4px;
    }
    .analysis-tile-wrapper:hover .analysis-tile-menu {
        opacity: 1;
        visibility: visible;
    }
    .analysis-tile-menu:hover {
        background: #f3f4f6;
        color: #374151;
    }
    .analysis-tile-wrapper {
        position: relative;
        margin-bottom: 8px;
    }
    .analysis-tile-context-menu {
        position: absolute;
        bottom: calc(100% + 4px);
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        min-width: 160px;
        z-index: 1000;
        display: none;
        overflow: hidden;
    }
    .analysis-tile-context-menu.show { display: block; }
    .analysis-tile-context-menu-item {
        padding: 10px 16px;
        font-size: 13px;
        color: #374151;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .analysis-tile-context-menu-item:hover { background: #f8fafc; }
    .analysis-tile-context-menu-item.delete { color: #ef4444; }
    .analysis-tile-context-menu-item.delete:hover { background: #fef2f2; }
    .analysis-tile.active {
        background: #eff6ff;
        border-color: #667eea;
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    @media (max-width: 1024px) {
        .cursor-layout {
            flex-direction: column;
            height: auto;
        }
        .cursor-sidebar {
            width: 100%;
            border-right: none;
            border-bottom: 1px solid #e5e7eb;
            order: 1;
            max-height: 50vh;
        }
        .cursor-main {
            border-left: none;
            border-top: 1px solid #e5e7eb;
            order: 2;
        }
    }
    @media (max-width: 768px) {
        .cursor-main-content { padding: 20px 16px; }
    }
</style>

<div class="cursor-layout">
    <div class="cursor-sidebar">
        <div class="cursor-sidebar-content">
            <div class="cursor-sidebar-header">
                <h2 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;">Sentiment History</h2>
                <p style="color: #6b7280; font-size: 12px; margin-top: 4px; margin-bottom: 0;">Filter and manage your analyses</p>
            </div>

            @if (session('status'))
                <div class="cursor-section" style="padding-top: 0;">
                    <div style="background: #d1fae5; border: 1px solid #6ee7b7; color: #065f46; padding: 10px 12px; border-radius: 6px; font-size: 12px;">{{ session('status') }}</div>
                </div>
            @endif

            <div class="cursor-section">
                <div class="cursor-section-title" style="display: flex; align-items: center; justify-content: space-between; cursor: pointer;" onclick="toggleFilterSection()">
                    <span>Search & Filter</span>
                    <i class="bi bi-chevron-down" id="filterToggleIcon" style="font-size: 12px; color: #6b7280; transition: transform 0.3s ease;"></i>
                </div>
                <div id="filterSectionContent" style="overflow: hidden; transition: max-height 0.3s ease;">
                    <form method="GET" action="{{ route('sentiment-analysis.history') }}" id="filterForm">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 10px;">
                            <div>
                                <label for="search" style="display: block; margin-bottom: 4px; font-weight: 600; color: #374151; font-size: 11px;">Search</label>
                                <div style="position: relative;">
                                    <input type="text"
                                           id="search"
                                           name="search"
                                           value="{{ request('search') }}"
                                           placeholder="Search..."
                                           style="width: 100%; padding: 6px 8px 6px 28px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 12px; transition: all 0.15s ease; background: #ffffff;">
                                    <i class="bi bi-search" style="position: absolute; left: 8px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 12px;"></i>
                                </div>
                            </div>
                            <div>
                                <label for="report_language" style="display: block; margin-bottom: 4px; font-weight: 600; color: #374151; font-size: 11px;">Report language</label>
                                <select id="report_language"
                                        name="report_language"
                                        style="width: 100%; padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 12px; transition: all 0.15s ease; background: #ffffff; cursor: pointer;">
                                    <option value="">All languages</option>
                                    <option value="en" {{ request('report_language') == 'en' ? 'selected' : '' }}>English</option>
                                    <option value="ms" {{ request('report_language') == 'ms' ? 'selected' : '' }}>Bahasa Melayu</option>
                                </select>
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 10px;">
                            <div>
                                <label for="date_from" style="display: block; margin-bottom: 4px; font-weight: 600; color: #374151; font-size: 11px;">From Date</label>
                                <input type="date"
                                       id="date_from"
                                       name="date_from"
                                       value="{{ request('date_from') }}"
                                       style="width: 100%; padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 12px; transition: all 0.15s ease; background: #ffffff;">
                            </div>
                            <div>
                                <label for="date_to" style="display: block; margin-bottom: 4px; font-weight: 600; color: #374151; font-size: 11px;">To Date</label>
                                <input type="date"
                                       id="date_to"
                                       name="date_to"
                                       value="{{ request('date_to') }}"
                                       style="width: 100%; padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 12px; transition: all 0.15s ease; background: #ffffff;">
                            </div>
                        </div>
                        <div style="display: flex; gap: 6px;">
                            <a href="{{ route('sentiment-analysis.history') }}"
                                onclick="sessionStorage.setItem('sentimentHistoryScrollPosition', window.pageYOffset || document.documentElement.scrollTop);"
                                style="flex: 1; padding: 6px 10px; background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; border-radius: 4px; font-weight: 500; font-size: 11px; text-decoration: none; text-align: center; transition: all 0.2s ease; display: inline-flex; align-items: center; justify-content: center; gap: 4px;">
                                <i class="bi bi-x-circle" style="font-size: 11px;"></i>Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="cursor-section">
                <div class="cursor-section-title">All comparisons</div>
                <div style="padding-right: 4px;">
                    @forelse($comparisons as $c)
                        @php
                            $tileTitle = \Illuminate\Support\Str::limit($c->ai_result['title'] ?? 'Sentiment comparison', 56);
                            $uA = $c->socialMediaAnalysisA?->profileDisplayLabel() ?? ($c->socialMediaAnalysisA->username ?? 'A');
                            $uB = $c->socialMediaAnalysisB?->profileDisplayLabel() ?? ($c->socialMediaAnalysisB->username ?? 'B');
                            $tileSub = $uA . ' vs ' . $uB . ' · ' . $c->created_at->format('M j, Y');
                        @endphp
                        <div class="analysis-tile-wrapper" data-comparison-id="{{ $c->id }}">
                            <a href="javascript:void(0);" class="analysis-tile"
                                onclick="event.preventDefault(); loadComparisonResults({{ $c->id }}, this);">
                                <p class="analysis-tile-title">{{ $tileTitle }}</p>
                                <p class="analysis-tile-sub">{{ $tileSub }}</p>
                            </a>
                            <i class="bi bi-three-dots analysis-tile-menu"
                                onclick="event.stopPropagation(); toggleContextMenu(event, {{ $c->id }}, {{ json_encode($tileTitle) }})"></i>
                            <div class="analysis-tile-context-menu" id="contextMenu{{ $c->id }}">
                                <div class="analysis-tile-context-menu-item" onclick="event.stopPropagation(); closeContextMenu(); confirmSentimentHistoryExport({{ $c->id }}, {{ json_encode($tileTitle) }}, {{ json_encode($uA.' vs '.$uB) }});">
                                    <i class="bi bi-download"></i>
                                    <span>Export</span>
                                </div>
                                <div class="analysis-tile-context-menu-item delete" onclick="event.stopPropagation(); closeContextMenu(); confirmDeleteComparison({{ $c->id }}, {{ json_encode($tileTitle) }});">
                                    <i class="bi bi-trash"></i>
                                    <span>Delete</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="text-align: center; padding: 24px 12px; color: #9ca3af; font-size: 12px;">
                            <i class="bi bi-inbox" style="font-size: 24px; display: block; margin-bottom: 8px; opacity: 0.5;"></i>
                            No comparisons found
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="cursor-main scrollable" id="comparisonResultsPanel">
        <div class="cursor-main-content" id="comparisonResultsContent" style="display: none;"></div>
        <div id="comparisonResultsEmpty" style="display: flex; align-items: center; justify-content: center; min-height: 280px; color: #9ca3af; font-size: 14px; padding: 24px;">
            <div style="text-align: center;">
                <i class="bi bi-emoji-smile" style="font-size: 48px; display: block; margin-bottom: 16px; opacity: 0.5;"></i>
                <p style="margin: 0;">Select a comparison to view the report</p>
            </div>
        </div>
    </div>
</div>

<div id="deleteModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; align-items: center; justify-content: center; padding: 16px;">
    <div class="modal-content" style="background: white; border-radius: 16px; padding: 32px; max-width: 400px; width: 100%; text-align: center; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);">
        <h3 style="color: #1e293b; margin-bottom: 16px; font-size: 20px; font-weight: 600;">Delete comparison?</h3>
        <p style="color: #64748b; margin-bottom: 16px; line-height: 1.6; font-size: 14px;">This cannot be undone.</p>
        <p id="deleteComparisonLabel" style="color: #1e293b; margin-bottom: 24px; font-weight: 600; font-style: italic; background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 13px;"></p>
        <div style="display: flex; gap: 16px; justify-content: center;">
            <button type="button" onclick="closeDeleteModal()" style="padding: 12px 24px; background: transparent; color: #64748b; border: 1px solid #d1d5db; border-radius: 8px; font-weight: 500; font-size: 14px; cursor: pointer;">Cancel</button>
            <button type="button" id="confirmDeleteComparisonBtn" style="padding: 12px 24px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border: none; border-radius: 8px; font-weight: 500; font-size: 14px; cursor: pointer; box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);">Delete</button>
        </div>
    </div>
</div>

<div id="sentimentHistoryExportModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; align-items: center; justify-content: center; padding: 16px;">
    <div style="background: white; border-radius: 16px; padding: 32px; max-width: 400px; width: 100%; text-align: center; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);">
        <div style="margin-bottom: 24px;"><span style="font-size: 48px; color: #10b981;">📄</span></div>
        <h3 style="color: #1e293b; margin-bottom: 16px; font-size: 20px; font-weight: 600;">Export PDF Report</h3>
        <p style="color: #64748b; margin-bottom: 24px; line-height: 1.6;">Are you ready to export this sentiment comparison as a PDF report?</p>
        <p id="sentimentHistoryExportLabel" style="color: #1e293b; margin-bottom: 24px; font-weight: 600; font-style: italic; background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0;"></p>
        <p style="color: #10b981; margin-bottom: 24px; font-size: 14px; font-weight: 500;">The report will include all comparison details and NUJUM insights.</p>
        <div style="display: flex; gap: 16px; justify-content: center;">
            <button type="button" onclick="closeSentimentHistoryExportModal()" style="padding: 12px 24px; background: transparent; color: #64748b; border: 1px solid #d1d5db; border-radius: 8px; font-weight: 500; font-size: 14px; cursor: pointer;">Cancel</button>
            <button type="button" id="confirmSentimentHistoryExportBtn" style="padding: 12px 24px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; border-radius: 8px; font-weight: 500; font-size: 14px; cursor: pointer; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);">Export PDF</button>
        </div>
    </div>
</div>

<script>
let currentDeleteComparisonId = null;
let currentSentimentHistoryExportId = null;
let currentDisplayedComparisonId = null;
let searchTimeout = null;

function toggleFilterSection() {
    const content = document.getElementById('filterSectionContent');
    const icon = document.getElementById('filterToggleIcon');
    const isCollapsed = content.style.maxHeight === '0px' || content.style.maxHeight === '';
    if (isCollapsed) {
        content.style.maxHeight = content.scrollHeight + 'px';
        icon.style.transform = 'rotate(0deg)';
        sessionStorage.setItem('sentimentFilterSectionCollapsed', 'false');
    } else {
        content.style.maxHeight = '0px';
        icon.style.transform = 'rotate(-90deg)';
        sessionStorage.setItem('sentimentFilterSectionCollapsed', 'true');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const filterContent = document.getElementById('filterSectionContent');
    const filterIcon = document.getElementById('filterToggleIcon');
    if (filterContent && filterIcon) {
        filterContent.style.maxHeight = '0px';
        filterIcon.style.transform = 'rotate(-90deg)';
    }
    const savedScroll = sessionStorage.getItem('sentimentHistoryScrollPosition');
    if (savedScroll) {
        setTimeout(function() {
            window.scrollTo(0, parseInt(savedScroll, 10));
            sessionStorage.removeItem('sentimentHistoryScrollPosition');
        }, 100);
    }
    const filterForm = document.getElementById('filterForm');
    const searchInput = document.getElementById('search');
    const reportLanguageSelect = document.getElementById('report_language');
    const dateFrom = document.getElementById('date_from');
    const dateTo = document.getElementById('date_to');
    function saveScroll() {
        sessionStorage.setItem('sentimentHistoryScrollPosition', window.pageYOffset || document.documentElement.scrollTop);
    }
    if (searchInput && filterForm) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() { saveScroll(); filterForm.submit(); }, 500);
        });
    }
    if (reportLanguageSelect && filterForm) {
        reportLanguageSelect.addEventListener('change', function() {
            saveScroll();
            filterForm.submit();
        });
    }
    if (dateFrom && filterForm) dateFrom.addEventListener('change', function() { saveScroll(); filterForm.submit(); });
    if (dateTo && filterForm) dateTo.addEventListener('change', function() { saveScroll(); filterForm.submit(); });
});

function loadComparisonResults(comparisonId, tileElement) {
    if (tileElement && tileElement.classList.contains('active') && currentDisplayedComparisonId === comparisonId) {
        return;
    }
    setActiveTile(tileElement);
    currentDisplayedComparisonId = comparisonId;
    const resultsContent = document.getElementById('comparisonResultsContent');
    const resultsEmpty = document.getElementById('comparisonResultsEmpty');
    resultsEmpty.style.display = 'none';
    resultsContent.style.display = 'block';
    resultsContent.style.width = '100%';
    resultsContent.innerHTML = '<div style="text-align: center; color: #64748b; padding: 48px;"><i class="bi bi-hourglass-split" style="font-size: 48px; display: block; margin-bottom: 16px; animation: spin 1s linear infinite;"></i><p>Loading report…</p></div>';

    fetch('{{ url("/sentiment-analysis") }}/' + comparisonId + '/content-html', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html',
        }
    })
    .then(function(r) {
        if (!r.ok) throw new Error('Failed to load');
        return r.text();
    })
    .then(function(html) {
        resultsContent.innerHTML = html;
        if (typeof window.initSentimentReportChartsFromDom === 'function') {
            setTimeout(function () {
                window.initSentimentReportChartsFromDom(resultsContent);
            }, 100);
        }
    })
    .catch(function(err) {
        resultsContent.innerHTML = '<div style="text-align: center; color: #ef4444; padding: 48px;"><i class="bi bi-exclamation-triangle" style="font-size: 48px; display: block; margin-bottom: 16px;"></i><p>Could not load comparison</p><p style="font-size: 12px; color: #9ca3af;">' + (err.message || '') + '</p></div>';
    });
}

function setActiveTile(element) {
    document.querySelectorAll('.analysis-tile').forEach(function(t) { t.classList.remove('active'); });
    if (element) element.classList.add('active');
}

function toggleContextMenu(event, comparisonId, title) {
    event.preventDefault();
    event.stopPropagation();
    closeContextMenu();
    const menu = document.getElementById('contextMenu' + comparisonId);
    if (!menu) return;
    menu.classList.add('show');
    const iconRect = event.target.getBoundingClientRect();
    const menuRect = menu.getBoundingClientRect();
    const wrapper = event.target.closest('.analysis-tile-wrapper');
    const wrapperRect = wrapper.getBoundingClientRect();
    const iconLeftRelative = iconRect.left - wrapperRect.left;
    menu.style.left = iconLeftRelative + 'px';
    menu.style.right = 'auto';
    if (iconLeftRelative + menuRect.width > wrapperRect.width) {
        menu.style.right = '8px';
        menu.style.left = 'auto';
    }
    if (iconRect.top - menuRect.height < 0) {
        menu.style.bottom = 'auto';
        menu.style.top = 'calc(100% + 4px)';
    } else {
        menu.style.bottom = 'calc(100% + 4px)';
        menu.style.top = 'auto';
    }
    setTimeout(function() {
        document.addEventListener('click', closeContextMenu, { once: true });
    }, 0);
}

function closeContextMenu() {
    document.querySelectorAll('.analysis-tile-context-menu').forEach(function(m) { m.classList.remove('show'); });
}

function confirmDeleteComparison(id, title) {
    currentDeleteComparisonId = id;
    document.getElementById('deleteComparisonLabel').textContent = title || ('#' + id);
    document.getElementById('deleteModal').style.display = 'flex';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
    currentDeleteComparisonId = null;
}

function deleteComparison() {
    if (!currentDeleteComparisonId) return;
    const id = currentDeleteComparisonId;
    closeDeleteModal();
    showToast('Deleting…', 'success');
    fetch('{{ url("/sentiment-analysis") }}/' + id, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        }
    })
    .then(async function(r) {
        let data = {};
        try { data = await r.json(); } catch (e) {}
        if (!r.ok) throw new Error(data.error || data.message || 'Delete failed');
        return data;
    })
    .then(function() {
        showToast('Comparison deleted', 'success');
        const wrap = document.querySelector('[data-comparison-id="' + id + '"]');
        if (wrap) {
            wrap.style.transition = 'opacity 0.3s ease';
            wrap.style.opacity = '0';
            setTimeout(function() { wrap.remove(); }, 300);
        }
        if (currentDisplayedComparisonId === id) {
            currentDisplayedComparisonId = null;
            document.getElementById('comparisonResultsContent').style.display = 'none';
            document.getElementById('comparisonResultsContent').innerHTML = '';
            document.getElementById('comparisonResultsEmpty').style.display = 'flex';
        }
    })
    .catch(function(e) {
        showToast(e.message || 'Delete failed', 'error');
    });
}

document.getElementById('confirmDeleteComparisonBtn').onclick = deleteComparison;
document.getElementById('deleteModal').onclick = function(e) {
    if (e.target === this) closeDeleteModal();
};

function confirmSentimentHistoryExport(id, title, profilesLine) {
    currentSentimentHistoryExportId = id;
    var el = document.getElementById('sentimentHistoryExportLabel');
    if (el) {
        el.textContent = profilesLine || title || ('#' + id);
    }
    var m = document.getElementById('sentimentHistoryExportModal');
    if (m) {
        m.style.display = 'flex';
    }
}

function closeSentimentHistoryExportModal() {
    currentSentimentHistoryExportId = null;
    var m = document.getElementById('sentimentHistoryExportModal');
    if (m) {
        m.style.display = 'none';
    }
}

function exportSentimentHistoryPdf() {
    if (!currentSentimentHistoryExportId) {
        showToast('Error: Comparison ID not found', 'error');
        return;
    }
    var n = parseInt(currentSentimentHistoryExportId, 10);
    closeSentimentHistoryExportModal();
    showToast('Exporting PDF...', 'success');
    setTimeout(function() {
        showToast('PDF exported successfully!', 'success');
    }, 1000);
    window.location.href = '{{ url('/sentiment-analysis') }}/' + n + '/export';
}

var confirmHistExportBtn = document.getElementById('confirmSentimentHistoryExportBtn');
if (confirmHistExportBtn) {
    confirmHistExportBtn.onclick = exportSentimentHistoryPdf;
}
var sentimentHistoryExportModal = document.getElementById('sentimentHistoryExportModal');
if (sentimentHistoryExportModal) {
    sentimentHistoryExportModal.onclick = function(e) {
        if (e.target === sentimentHistoryExportModal) {
            closeSentimentHistoryExportModal();
        }
    };
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
        closeSentimentHistoryExportModal();
        closeContextMenu();
    }
});

function showToast(message, type) {
    document.querySelectorAll('.toast-notification').forEach(function(toast) {
        if (toast.parentNode) toast.parentNode.removeChild(toast);
    });
    const toast = document.createElement('div');
    toast.className = 'toast-notification';
    const bg = type === 'success' ? '#10b981' : '#ef4444';
    toast.style.cssText = 'position:fixed;top:20px;right:20px;background:' + bg + ';color:white;padding:16px 20px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.15);z-index:10000;font-size:14px;font-weight:500;';
    const icon = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle';
    toast.innerHTML = '<i class="bi ' + icon + '" style="margin-right:8px;"></i>' + message;
    document.body.appendChild(toast);
    setTimeout(function() {
        if (toast.parentNode) toast.parentNode.removeChild(toast);
    }, 4000);
}
</script>
@include('sentiment-analysis.partials.sentiment-charts-init')
@endsection
