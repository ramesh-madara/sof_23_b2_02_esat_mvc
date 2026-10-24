<?php require __DIR__ . '/header.php'; ?>

<div class="panel" style="max-width: 580px; margin: 0 auto 24px auto;">
    <div class="panel-header">
        <div>
            <h2>Doctor Account Management</h2>
            <div class="form-hint">Manage your credentials and security settings</div>
        </div>
        <div>
            <a href="index.php?action=patients" class="button secondary button-small">&larr; Patient Directory</a>
        </div>
    </div>

    <?php if (!empty($successMessage)): ?>
        <div class="alert-success"><?php echo htmlspecialchars($successMessage); ?></div>
    <?php endif; ?>

    <?php if (!empty($errorMessage)): ?>
        <div class="alert-error"><?php echo htmlspecialchars($errorMessage); ?></div>
    <?php endif; ?>

    <div class="info-card">
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Doctor Name</span>
                <span class="info-value"><?php echo htmlspecialchars($doctor['display_name'] ?? ''); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">System Identification</span>
                <span class="info-value">#<?php echo htmlspecialchars((string)($doctor['id'] ?? '')); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Isolation Scope</span>
                <span class="info-value"><span class="badge badge-dark">Private Doctor Sandbox</span></span>
            </div>
        </div>
    </div>

    <h3>Display Name</h3>
    <form method="POST" action="index.php?action=account" style="margin-bottom: 28px;">
        <input type="hidden" name="account_action" value="profile">
        <label for="display_name">Name shown in the app and on reports</label>
        <input type="text" id="display_name" name="display_name" required maxlength="100" autocomplete="name" value="<?php echo htmlspecialchars($displayName ?? $doctor['display_name'] ?? ''); ?>">
        <button type="submit" class="button" style="margin-top: 16px;">Save Display Name</button>
    </form>

    <h3>Update Security Password</h3>
    <form method="POST" action="index.php?action=account">
        <div>
            <label for="current_password">Current Password</label>
            <input type="password" id="current_password" name="current_password" required minlength="6" maxlength="64" autocomplete="current-password" placeholder="Verify your current password">
        </div>

        <div>
            <label for="new_password">New Password</label>
            <input type="password" id="new_password" name="new_password" required minlength="6" maxlength="64" autocomplete="new-password" placeholder="Enter new password (min. 6 chars)">
            <span class="form-hint">Must be at least 6 characters in length</span>
        </div>

        <div>
            <label for="confirm_password">Confirm New Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required minlength="6" maxlength="64" autocomplete="new-password" placeholder="Confirm your new password">
        </div>

        <div class="button-group" style="margin-top: 24px; padding-top: 16px; border-top: 1px solid #000000;">
            <button type="submit" class="button">Update Password</button>
            <a href="index.php?action=patients" class="button secondary">Cancel</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/footer.php'; ?>
