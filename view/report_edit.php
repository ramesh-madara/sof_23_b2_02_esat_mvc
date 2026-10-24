<?php require __DIR__ . '/header.php'; ?>

<div style="margin-bottom: 16px;">
    <a href="index.php?action=patient_view&id=<?php echo urlencode($report['patient_id']); ?>" class="button secondary button-small">&larr; Back to Patient Record</a>
</div>

<div class="panel" style="max-width: 720px; margin: 0 auto 24px auto;">
    <div class="panel-header">
        <div>
            <h2>Modify Blood Report #<?php echo htmlspecialchars((string)$report['id']); ?></h2>
            <div class="form-hint">Update test parameters and clinical observations</div>
        </div>
    </div>

    <div class="info-card">
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Patient</span>
                <span class="info-value"><?php echo htmlspecialchars($report['patient_name']); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Age</span>
                <span class="info-value"><?php echo htmlspecialchars((string)$report['patient_age']); ?> Years</span>
            </div>
            <div class="info-item">
                <span class="info-label">Gender</span>
                <span class="info-value"><span class="badge"><?php echo htmlspecialchars($report['patient_gender']); ?></span></span>
            </div>
        </div>
    </div>

    <?php if (!empty($errorMessage)): ?>
        <div class="alert-error"><?php echo htmlspecialchars($errorMessage); ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php?action=report_edit&id=<?php echo urlencode($report['id']); ?>">
        <?php require __DIR__ . '/report_fields.php'; ?>

        <div class="button-group" style="margin-top: 24px; padding-top: 16px; border-top: 1px solid #000000;">
            <button type="submit" class="button">Update Report</button>
            <a href="index.php?action=report_view&id=<?php echo urlencode($report['id']); ?>" class="button secondary">Cancel</a>
        </div>
    </form>
</div>



<?php require __DIR__ . '/footer.php'; ?>
