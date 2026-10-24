<?php require __DIR__ . '/header.php'; ?>

<div style="margin-bottom: 16px;">
    <a href="index.php?action=patient_view&id=<?php echo urlencode($patient['id']); ?>" class="button secondary button-small">&larr; Back to Patient Record</a>
</div>

<div class="panel" style="max-width: 720px; margin: 0 auto 24px auto;">
    <div class="panel-header">
        <div>
            <h2>Create Blood Report</h2>
            <div class="form-hint">Enter observed laboratory test values for the selected category</div>
        </div>
    </div>

    <div class="info-card">
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Patient</span>
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

    <?php if (!empty($errorMessage)): ?>
        <div class="alert-error"><?php echo htmlspecialchars($errorMessage); ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php?action=report_create&patient_id=<?php echo urlencode($patient['id']); ?>">
        <?php require __DIR__ . '/report_fields.php'; ?>

        <div class="button-group" style="margin-top: 24px; padding-top: 16px; border-top: 1px solid #000000;">
            <button type="submit" class="button">Save Blood Report</button>
            <a href="index.php?action=patient_view&id=<?php echo urlencode($patient['id']); ?>" class="button secondary">Cancel</a>
        </div>
    </form>
</div>



<?php require __DIR__ . '/footer.php'; ?>
