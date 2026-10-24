<?php require __DIR__ . '/header.php'; ?>

<div style="margin-bottom: 16px;">
    <a href="index.php?action=patient_view&id=<?php echo urlencode($report['patient_id']); ?>" class="button secondary button-small">&larr; Back to Patient Record</a>
</div>

<div class="panel">
    <div class="panel-header">
        <div>
            <h2>Blood Test Report #<?php echo htmlspecialchars((string)$report['id']); ?></h2>
            <div class="form-hint">Generated on <?php echo htmlspecialchars($report['created_at']); ?></div>
        </div>
        <div class="button-group">
            <button type="button" class="button" onclick="openPdfModal(<?php echo (int)$report['id']; ?>);">Export PDF</button>
            <a href="index.php?action=report_edit&id=<?php echo urlencode($report['id']); ?>" class="button secondary">Edit Report</a>
            <form method="POST" action="index.php?action=report_delete" class="inline-form" onsubmit="return confirm('Are you sure you want to permanently delete this report?');">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars((string)$report['id']); ?>">
                <button type="submit" class="button danger">Delete</button>
            </form>
        </div>
    </div>

    <?php if (isset($_GET['updated'])): ?>
        <div class="alert-success">Report updated successfully.</div>
    <?php endif; ?>

    <div class="info-card">
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Patient Name</span>
                <span class="info-value"><?php echo htmlspecialchars($report['patient_name']); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Patient Age</span>
                <span class="info-value"><?php echo htmlspecialchars((string)$report['patient_age']); ?> Years</span>
            </div>
            <div class="info-item">
                <span class="info-label">Patient Gender</span>
                <span class="info-value"><span class="badge"><?php echo htmlspecialchars($report['patient_gender']); ?></span></span>
            </div>
            <div class="info-item">
                <span class="info-label">Report Category</span>
                <span class="info-value"><span class="badge badge-dark"><?php echo htmlspecialchars($report['type']); ?></span></span>
            </div>
            <div class="info-item">
                <span class="info-label">Attending Doctor</span>
                <span class="info-value"><?php echo htmlspecialchars(($_SESSION['doctor_display_name'] ?? $_SESSION['doctor_username'])); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Date Generated</span>
                <span class="info-value"><?php echo htmlspecialchars($report['created_at']); ?></span>
            </div>
        </div>
    </div>

    <h3>Observed Test Results</h3>
    <table>
        <thead>
            <tr>
                <th>Test Parameter</th>
                <th>Observed Reading</th>
                <th>Reference Range</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results as $fieldName => $reading): ?>
                <?php $check = TestRanges::check((string)$fieldName, $reading, (int)$report['patient_age'], $report['patient_gender']); ?>
                <tr>
                    <td>
                        <strong style="font-size: 14px;"><?php echo htmlspecialchars((string)$fieldName); ?></strong>
                    </td>
                    <td>
                        <strong style="font-size: 15px;"><?php echo htmlspecialchars((string)$reading); ?></strong>
                    </td>
                    <td><?php echo htmlspecialchars($check['range']); ?></td>
                    <td><span class="result-status result-<?php echo htmlspecialchars($check['style']); ?>"><?php echo htmlspecialchars($check['status']); ?></span></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
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
