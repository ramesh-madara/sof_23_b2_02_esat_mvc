<?php require __DIR__ . '/header.php'; ?>

<div style="margin-bottom: 16px;">
    <a href="index.php?action=patients" class="button secondary button-small">&larr; Back to Patient Directory</a>
</div>

<div class="panel" style="max-width: 580px; margin: 0 auto 24px auto;">
    <div class="panel-header">
        <div>
            <h2>Register New Patient</h2>
            <div class="form-hint">Create a patient demographic record under your clinical account</div>
        </div>
    </div>

    <?php if (!empty($errorMessage)): ?>
        <div class="alert-error"><?php echo htmlspecialchars($errorMessage); ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php?action=patient_create">
        <div>
            <label for="name">Patient Full Name</label>
            <input type="text" id="name" name="name" required minlength="2" maxlength="100" pattern="^[a-zA-Z\s\.\-']{2,100}$" title="Patient name must contain only letters, spaces, hyphens, and periods (2 to 100 characters)." placeholder="e.g. Eleanor Vance" autofocus>
            <span class="form-hint">Accepted characters: Letters, spaces, hyphens, and periods (2-100 characters)</span>
        </div>

        <div>
            <label for="age">Age</label>
            <input type="number" id="age" name="age" min="1" max="125" step="1" required placeholder="e.g. 42">
            <span class="form-hint">Must be a valid integer between 1 and 125</span>
        </div>

        <div>
            <label for="gender">Biological Gender</label>
            <select id="gender" name="gender" required>
                <option value="">-- Select Gender --</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
            <span class="form-hint">Required for standard clinical laboratory reference calculations</span>
        </div>

        <div class="button-group" style="margin-top: 24px; padding-top: 16px; border-top: 1px solid #000000;">
            <button type="submit" class="button">Save Patient Record</button>
            <a href="index.php?action=patients" class="button secondary">Cancel</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/footer.php'; ?>
