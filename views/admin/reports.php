<?php ob_start(); ?>
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1 text-dark">
                <i class="fas fa-file-invoice text-success me-2"></i>Reports & Analytics
            </h3>
            <p class="text-muted small mb-0">Generate comprehensive reports, filter applicants by criteria, and export complete student datasets to Excel or CSV.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-success btn-sm px-3 shadow-sm fw-semibold" onclick="exportReport('excel')">
                <i class="fas fa-file-excel me-1"></i> Export Full Excel
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm px-3 shadow-sm" onclick="exportReport('csv')">
                <i class="fas fa-file-csv me-1"></i> Export CSV
            </button>
        </div>
    </div>

    <!-- Quick Category Select Cards -->
    <div class="row g-3 mb-4">
        <!-- All Applications Card -->
        <div class="col-md-4">
            <div class="card h-100 shadow-sm report-type-card active" id="card-all" onclick="selectReportType('')">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge bg-secondary mb-1">ALL CATEGORIES</span>
                        <h6 class="fw-bold mb-1 text-dark">All Applications</h6>
                        <p class="text-muted small mb-0" id="cardTotalText"><?= (int)($totalCount ?? 0) ?> applications submitted</p>
                    </div>
                    <div class="text-end">
                        <i class="fas fa-layer-group text-primary fs-3"></i>
                    </div>
                </div>
                <i class="fas fa-check-circle check-indicator"></i>
            </div>
        </div>

        <!-- Student Assistant Card -->
        <div class="col-md-4">
            <div class="card h-100 shadow-sm report-type-card" id="card-student_assistant" onclick="selectReportType('student_assistant')">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge bg-success mb-1">ENABLED</span>
                        <h6 class="fw-bold mb-1 text-dark">Student Assistant</h6>
                        <p class="text-muted small mb-0" id="cardSaText"><?= (int)($saCount ?? 0) ?> applications submitted</p>
                    </div>
                    <div class="text-end">
                        <i class="fas fa-user-tie text-success fs-3"></i>
                    </div>
                </div>
                <i class="fas fa-check-circle check-indicator"></i>
            </div>
        </div>

        <!-- Academic Achiever Card -->
        <div class="col-md-4">
            <div class="card h-100 shadow-sm report-type-card" id="card-academic_achiever" onclick="selectReportType('academic_achiever')">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge bg-success mb-1">ENABLED</span>
                        <h6 class="fw-bold mb-1 text-dark">Academic Achiever</h6>
                        <p class="text-muted small mb-0" id="cardAaText"><?= (int)($aaCount ?? 0) ?> applications submitted</p>
                    </div>
                    <div class="text-end">
                        <i class="fas fa-graduation-cap text-warning fs-3"></i>
                    </div>
                </div>
                <i class="fas fa-check-circle check-indicator"></i>
            </div>
        </div>
    </div>

    <!-- Filter & Export Controls -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <h6 class="fw-bold text-success mb-0">
                <i class="fas fa-sliders-h me-2"></i>Filter Criteria & Parameters
            </h6>
        </div>
        <div class="card-body p-4">
            <form id="reportFilterForm" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Application Type</label>
                    <select name="type_slug" id="rfType" class="form-select form-select-sm" onchange="onTypeSelectChange(this.value)">
                        <option value="">-- All (Student Assistant & Academic Achiever) --</option>
                        <option value="student_assistant">Student Assistant</option>
                        <option value="academic_achiever">Academic Achiever</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Status</label>
                    <select name="status" id="rfStatus" class="form-select form-select-sm">
                        <option value="">-- All Statuses --</option>
                        <option value="Submitted">Submitted</option>
                        <option value="Under Review">Under Review</option>
                        <option value="For Revision">For Revision</option>
                        <option value="Qualified">Qualified</option>
                        <option value="Disqualified">Disqualified</option>
                        <option value="Approved">Approved</option>
                        <option value="Rejected">Rejected</option>
                        <option value="Draft">Draft</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Year Level</label>
                    <select name="year_level" id="rfYear" class="form-select form-select-sm">
                        <option value="">-- All Years --</option>
                        <?php for ($i=1; $i<=5; $i++): ?>
                        <option value="<?= $i ?>"><?= yearLevelLabel($i) ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Program / Major</label>
                    <input type="text" name="program" id="rfProgram" class="form-control form-control-sm" placeholder="e.g. Information Technology, Education">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Date From</label>
                    <input type="date" name="date_from" id="rfFrom" class="form-control form-control-sm">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Date To</label>
                    <input type="date" name="date_to" id="rfTo" class="form-control form-control-sm">
                </div>
                <div class="col-md-6 d-flex align-items-end gap-2 flex-wrap">
                    <button type="button" class="btn btn-success btn-sm px-3 fw-semibold" onclick="generateReport()">
                        <i class="fas fa-sync-alt me-1"></i> Generate Report
                    </button>
                    <button type="button" class="btn btn-success btn-sm px-3 fw-semibold" style="background-color: #107c41; border-color: #107c41;" onclick="exportReport('excel')">
                        <i class="fas fa-file-excel me-1"></i> Export Excel (.xls)
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" onclick="exportReport('csv')">
                        <i class="fas fa-file-csv me-1"></i> Export CSV
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-sm px-3" onclick="exportReport('pdf')">
                        <i class="fas fa-print me-1"></i> Print / Summary
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Table -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h6 class="fw-bold mb-0 text-dark d-inline-block">
                    <i class="fas fa-table text-success me-2"></i>Report Preview & Applicant Dataset
                </h6>
                <span class="text-muted small ms-2" id="filterSummaryText">(Showing All Active Applications)</span>
            </div>
            <span id="resultCountBadge" class="badge bg-success fs-6 py-1 px-3">0 Records</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="reportResultTable">
                    <thead class="table-light text-muted small">
                        <tr>
                            <th class="ps-3" style="width: 50px;">#</th>
                            <th>Application #</th>
                            <th>Student ID</th>
                            <th>Applicant Name</th>
                            <th>Program & Year</th>
                            <th>Category</th>
                            <th>Specific Details</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th class="text-center" style="width: 80px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="reportTbody">
                        <tr><td colspan="10" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin me-2"></i>Loading applicant data...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function selectReportType(slug) {
    document.getElementById('rfType').value = slug;
    updateCardHighlights(slug);
    generateReport();
}

function onTypeSelectChange(slug) {
    updateCardHighlights(slug);
    generateReport();
}

function updateCardHighlights(slug) {
    document.querySelectorAll('.report-type-card').forEach(c => c.classList.remove('active'));
    if (!slug) {
        document.getElementById('card-all').classList.add('active');
        document.getElementById('filterSummaryText').innerText = '(Showing All Active Applications)';
    } else if (slug === 'student_assistant') {
        document.getElementById('card-student_assistant').classList.add('active');
        document.getElementById('filterSummaryText').innerText = '(Filtered by Student Assistant)';
    } else if (slug === 'academic_achiever') {
        document.getElementById('card-academic_achiever').classList.add('active');
        document.getElementById('filterSummaryText').innerText = '(Filtered by Academic Achiever)';
    }
}

function getStatusBadgeClass(status) {
    switch (status) {
        case 'Qualified':
        case 'Approved': return 'badge bg-success';
        case 'Submitted': return 'badge bg-primary';
        case 'Under Review': return 'badge bg-warning text-dark';
        case 'For Revision': return 'badge bg-info text-dark';
        case 'Disqualified':
        case 'Rejected': return 'badge bg-danger';
        case 'Draft': return 'badge bg-secondary';
        default: return 'badge bg-secondary';
    }
}

function generateReport() {
    var form = document.getElementById('reportFilterForm');
    var formData = new FormData(form);
    var tbody = document.getElementById('reportTbody');
    tbody.innerHTML = '<tr><td colspan="10" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin me-2"></i>Loading applicant data...</td></tr>';

    fetch('<?= BASE_URL ?>/admin/reports/generate', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        tbody.innerHTML = '';
        if (res.success && res.data && res.data.length > 0) {
            document.getElementById('resultCountBadge').innerText = res.count + (res.count === 1 ? ' Record' : ' Records');
            res.data.forEach(function(row, idx) {
                var tr = document.createElement('tr');
                var typeBadge = row.type_slug === 'student_assistant' 
                    ? '<span class="badge bg-success"><i class="fas fa-user-tie me-1"></i>Student Assistant</span>'
                    : '<span class="badge bg-warning text-dark"><i class="fas fa-graduation-cap me-1"></i>Academic Achiever</span>';
                
                var statusBadge = `<span class="${getStatusBadgeClass(row.status)}">${row.status}</span>`;
                var submittedDate = row.submitted_at ? row.submitted_at.substring(0, 16) : '<span class="text-muted">Draft</span>';
                var fullName = `<strong>${row.last_name}</strong>, ${row.first_name} ${row.middle_name || ''} ${row.extension_name || ''}`;

                tr.innerHTML = `
                    <td class="ps-3 text-muted">${idx + 1}</td>
                    <td class="fw-bold text-success font-monospace">${row.application_number}</td>
                    <td class="font-monospace">${row.student_number}</td>
                    <td>${fullName}</td>
                    <td class="small">
                        <div>${row.degree_program || '-'}</div>
                        <span class="text-muted">Year ${row.year_level || 1}</span>
                    </td>
                    <td>${typeBadge}</td>
                    <td class="small text-secondary">${row.specific_details || '-'}</td>
                    <td>${statusBadge}</td>
                    <td class="small text-muted">${submittedDate}</td>
                    <td class="text-center">
                        <a href="<?= BASE_URL ?>/admin/applications/view?id=${row.id}" class="btn btn-sm btn-outline-primary" title="View Application Details">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        } else {
            document.getElementById('resultCountBadge').innerText = '0 Records';
            tbody.innerHTML = '<tr><td colspan="10" class="text-center py-5 text-muted"><i class="fas fa-inbox fa-2x mb-2 d-block text-secondary"></i>No application records match the specified filters.</td></tr>';
        }
    })
    .catch(err => {
        console.error(err);
        tbody.innerHTML = '<tr><td colspan="10" class="text-center py-4 text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Failed to load report data. Please try again.</td></tr>';
    });
}

function exportReport(format) {
    var form = document.getElementById('reportFilterForm');
    var params = new URLSearchParams(new FormData(form)).toString();
    window.location.href = '<?= BASE_URL ?>/admin/reports/export?format=' + format + '&' + params;
}

// Auto load reports on page ready
document.addEventListener('DOMContentLoaded', function() {
    generateReport();
});
</script>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/admin_layout.php'; ?>

