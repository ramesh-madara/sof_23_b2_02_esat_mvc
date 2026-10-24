<?php require __DIR__ . '/header.php'; ?>

<div class="panel" style="max-width: 460px; margin: 40px auto 24px auto;">
    <div class="panel-header">
        <div>
            <h2>Doctor Authentication</h2>
            <div class="form-hint">Sign in to manage your clinical patient records</div>
        </div>
    </div>

    <?php if (isset($_GET['registered'])): ?>
        <div class="alert-success">Account created successfully. You may now sign in.</div>
    <?php endif; ?>

    <?php if (!empty($errorMessage)): ?>
        <div class="alert-error"><?php echo htmlspecialchars($errorMessage); ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php?action=login">
        <div>
            <label for="username">Doctor Username</label>
            <input type="text" id="username" name="username" required minlength="3" maxlength="30" autocomplete="username" placeholder="Enter your username" autofocus>
        </div>

        <div>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required minlength="6" maxlength="64" autocomplete="current-password" placeholder="Enter your password">
        </div>

        <div style="margin-top: 24px; padding-top: 16px; border-top: 1px solid #000000;">
            <button type="submit" class="button" style="width: 100%;">Log In to System</button>
        </div>
    </form>

    <div style="margin-top: 16px; padding: 12px 14px; background: #f7f7f7; border: 1px solid #e5e5e5; text-align: center; font-size: 12px; line-height: 1.8; color: #555555;">
        <span style="display: block; font-size: 10px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: #666666;">Test account</span>
        <span style="display: inline-block; margin: 0 6px;">Username <code style="color: #222222; font-weight: 600;">demo_doctor</code></span>
        <span style="display: inline-block; margin: 0 6px;">Password <code style="color: #222222; font-weight: 600;">Doctor123!</code></span>
    </div>

    <div style="margin-top: 20px; font-size: 13px; text-align: center; color: #555555;">
        Need a medical doctor account? <a href="index.php?action=register" style="color: #000000; font-weight: 800;">Register here</a>
    </div>
</div>

<?php require __DIR__ . '/footer.php'; ?>
