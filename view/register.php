<?php require __DIR__ . '/header.php'; ?>

<div class="panel" style="max-width: 480px; margin: 40px auto 24px auto;">
    <div class="panel-header">
        <div>
            <h2>Register Doctor Account</h2>
            <div class="form-hint">Create your clinical login for strict data isolation</div>
        </div>
    </div>

    <?php if (!empty($errorMessage)): ?>
        <div class="alert-error"><?php echo htmlspecialchars($errorMessage); ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php?action=register">
        <div>
            <label for="display_name">Display Name</label>
            <input type="text" id="display_name" name="display_name" required maxlength="100" autocomplete="name" placeholder="e.g. Dr. Amaya Silva" value="<?php echo htmlspecialchars($displayName ?? ''); ?>">
            <span class="form-hint">Shown in the app and on reports</span>
        </div>
        <div>
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required minlength="3" maxlength="30" pattern="^[a-zA-Z0-9_]{3,30}$" title="Username must be 3-30 characters long and contain only letters, numbers, and underscores." autocomplete="username" placeholder="e.g. dr_smith" autofocus>
            <span class="form-hint">3 to 30 alphanumeric characters and underscores</span>
        </div>

        <div>
            <label for="password">Account Password</label>
            <input type="password" id="password" name="password" required minlength="6" maxlength="64" autocomplete="new-password" placeholder="Choose a secure password">
            <span class="form-hint">Minimum 6 characters</span>
        </div>

        <div>
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required minlength="6" maxlength="64" autocomplete="new-password" placeholder="Re-enter your password">
        </div>

        <div style="margin-top: 24px; padding-top: 16px; border-top: 1px solid #000000;">
            <button type="submit" class="button" style="width: 100%;">Create Doctor Account</button>
        </div>
    </form>

    <div style="margin-top: 20px; font-size: 13px; text-align: center; color: #555555;">
        Already registered? <a href="index.php?action=login" style="color: #000000; font-weight: 800;">Sign in here</a>
    </div>
</div>

<?php require __DIR__ . '/footer.php'; ?>
