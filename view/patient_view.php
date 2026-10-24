<?php require __DIR__ . '/header.php'; ?>

<div style="margin-bottom: 16px;">
    <a href="index.php?action=patients" class="button secondary button-small">&larr; Back to Patient Directory</a>
</div>

<div class="panel">
    <div class="panel-header">
        <div>
            <h2><?php echo htmlspecialchars($patient['name']); ?></h2>
            <div class="form-hint">Patient Medical Record & Associated Blood Reports</div>
        </div>
        <div class="button-group">
            <a href="index.php?action=report_create&patient_id=<?php echo urlencode($patient['id']); ?>" class="button">+ Generate Blood Report</a>
        </div>
    </div>

    <div class="info-card">
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Patient ID</span>
                <span class="info-value">#<?php echo htmlspecialchars((string)$patient['id']); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Full Name</span>
                <span class="info-value"><?php echo htmlspecialchars($patient['name']); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Age</span>
                <span class="info-value"><?php echo htmlspecialchars((string)$patient['age']); ?> Years</span>
            </div>
            <div class="info-item">
                <span class="info-label">Gender</span>
                <span class="info-value"><span class="badge"><?php echo htmlspecialchars($patient['gender']); ?></span></span>
            </div>
        </div>
    </div>

    <h3>Blood Reports History</h3>

    <?php if (isset($_GET['report_created'])): ?>
        <div class="alert-success">Blood test report generated successfully.</div>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert-success">Report deleted successfully.</div>
    <?php endif; ?>

    <?php if (empty($reports)): ?>
        <div class="alert-warning" style="display: flex; justify-content: space-between; align-items: center;">
            <span>No blood reports have been created for this patient yet.</span>
            <a href="index.php?action=report_create&patient_id=<?php echo urlencode($patient['id']); ?>" class="button button-small">Create First Report</a>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th style="width: 80px; text-align: center;">Report ID</th>
                    <th style="width: 180px;">Test Category</th>
                    <th>Date Generated</th>
                    <th style="width: 270px; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reports as $report): ?>
                    <tr>
                        <td style="text-align: center; font-weight: 800;">#<?php echo htmlspecialchars((string)$report['id']); ?></td>
                        <td>
                            <span class="badge badge-dark"><?php echo htmlspecialchars($report['type']); ?></span>
                        </td>
                        <td><?php echo htmlspecialchars($report['created_at']); ?></td>
                        <td class="actions-cell">
                            <div class="button-group">
                                <a href="index.php?action=report_view&id=<?php echo urlencode($report['id']); ?>" class="button button-small">View</a>
                                <a href="index.php?action=report_edit&id=<?php echo urlencode($report['id']); ?>" class="button secondary button-small">Edit</a>
                                <button type="button" class="button secondary button-small" onclick="openPdfModal(<?php echo (int)$report['id']; ?>);">PDF</button>
                                <form method="POST" action="index.php?action=report_delete" class="inline-form" onsubmit="return confirm('Are you sure you want to permanently delete this report?');">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars((string)$report['id']); ?>">
                                    <button type="submit" class="button danger button-small">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div id="pdfModalOverlay" class="modal-overlay" onclick="handleBackdropClick(event);">
    <div class="modal-window">
        <div class="modal-header">
            <h3 id="pdfModalTitle" style="margin: 0;">Report PDF Preview</h3>
            <div class="button-group">
                <a id="modalDownloadButton" href="#" class="button button-small">Download PDF</a>
                <button type="button" class="button secondary button-small" onclick="closePdfModal();">Close</button>
            </div>
        </div>
        <div class="modal-body">
            <iframe id="modalPdfFrame" src="about:blank"></iframe>
        </div>
    </div>
</div>

<script>
function openPdfModal(reportId) {
    var overlay = document.getElementById('pdfModalOverlay');
    var iframe = document.getElementById('modalPdfFrame');
    var downloadButton = document.getElementById('modalDownloadButton');
    var title = document.getElementById('pdfModalTitle');

    title.textContent = 'Report PDF Preview - #' + reportId;
    iframe.src = 'index.php?action=report_pdf&id=' + encodeURIComponent(reportId);
    downloadButton.href = 'index.php?action=report_pdf&id=' + encodeURIComponent(reportId) + '&download=1';
    overlay.style.display = 'flex';
}

function closePdfModal() {
    var overlay = document.getElementById('pdfModalOverlay');
    var iframe = document.getElementById('modalPdfFrame');
    overlay.style.display = 'none';
    iframe.src = 'about:blank';
}

function handleBackdropClick(event) {
    if (event.target === document.getElementById('pdfModalOverlay')) {
        closePdfModal();
    }
}

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closePdfModal();
    }
});
</script>

<?php require __DIR__ . '/footer.php'; ?>
