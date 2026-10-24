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
        <div>
            <label for="report_type">Report Category</label>
            <select id="report_type" name="report_type" onchange="toggleTestPanels(this.value);" required>
                <option value="FBC" <?php echo ($report['type'] === 'FBC') ? 'selected' : ''; ?>>Full Blood Count (FBC)</option>
                <option value="Lipid Profile" <?php echo ($report['type'] === 'Lipid Profile') ? 'selected' : ''; ?>>Lipid Profile</option>
                <option value="Blood Sugar" <?php echo ($report['type'] === 'Blood Sugar') ? 'selected' : ''; ?>>Blood Sugar</option>
            </select>
        </div>

        <div id="panel_fbc" class="field-box" style="<?php echo ($report['type'] === 'FBC') ? 'display: block;' : 'display: none;'; ?>">
            <h3>Full Blood Count (FBC) Parameters</h3>
            <div>
                <label for="hemoglobin">Hemoglobin (g/dL)</label>
                <input type="number" id="hemoglobin" name="hemoglobin" step="0.01" min="0.1" max="35" value="<?php echo htmlspecialchars($results['Hemoglobin (g/dL)'] ?? ''); ?>" <?php echo ($report['type'] === 'FBC') ? 'required' : 'disabled'; ?>>
                <span class="form-hint">Standard Reference Range: 13.5 - 17.5 g/dL</span>
            </div>
            <div>
                <label for="white_blood_cells">White Blood Cell Count (x10^9/L)</label>
                <input type="number" id="white_blood_cells" name="white_blood_cells" step="0.01" min="0.1" max="150" value="<?php echo htmlspecialchars($results['White Blood Cell Count (x10^9/L)'] ?? ''); ?>" <?php echo ($report['type'] === 'FBC') ? 'required' : 'disabled'; ?>>
                <span class="form-hint">Standard Reference Range: 4.5 - 11.0 x10^9/L</span>
            </div>
            <div>
                <label for="red_blood_cells">Red Blood Cell Count (x10^12/L)</label>
                <input type="number" id="red_blood_cells" name="red_blood_cells" step="0.01" min="0.1" max="15" value="<?php echo htmlspecialchars($results['Red Blood Cell Count (x10^12/L)'] ?? ''); ?>" <?php echo ($report['type'] === 'FBC') ? 'required' : 'disabled'; ?>>
                <span class="form-hint">Standard Reference Range: 4.5 - 5.9 x10^12/L</span>
            </div>
            <div>
                <label for="platelets">Platelet Count (x10^9/L)</label>
                <input type="number" id="platelets" name="platelets" step="1" min="1" max="2500" value="<?php echo htmlspecialchars($results['Platelet Count (x10^9/L)'] ?? ''); ?>" <?php echo ($report['type'] === 'FBC') ? 'required' : 'disabled'; ?>>
                <span class="form-hint">Standard Reference Range: 150 - 450 x10^9/L</span>
            </div>
            <div>
                <label for="hematocrit">Hematocrit (%)</label>
                <input type="number" id="hematocrit" name="hematocrit" step="0.01" min="1" max="100" value="<?php echo htmlspecialchars($results['Hematocrit (%)'] ?? ''); ?>" <?php echo ($report['type'] === 'FBC') ? 'required' : 'disabled'; ?>>
                <span class="form-hint">Standard Reference Range: 41 - 50 %</span>
            </div>
        </div>

        <div id="panel_lipid" class="field-box" style="<?php echo ($report['type'] === 'Lipid Profile') ? 'display: block;' : 'display: none;'; ?>">
            <h3>Lipid Profile Parameters</h3>
            <div>
                <label for="total_cholesterol">Total Cholesterol (mg/dL)</label>
                <input type="number" id="total_cholesterol" name="total_cholesterol" step="0.01" min="1" max="1200" value="<?php echo htmlspecialchars($results['Total Cholesterol (mg/dL)'] ?? ''); ?>" <?php echo ($report['type'] === 'Lipid Profile') ? 'required' : 'disabled'; ?>>
                <span class="form-hint">Desirable: Below 200 mg/dL</span>
            </div>
            <div>
                <label for="triglycerides">Triglycerides (mg/dL)</label>
                <input type="number" id="triglycerides" name="triglycerides" step="0.01" min="1" max="2500" value="<?php echo htmlspecialchars($results['Triglycerides (mg/dL)'] ?? ''); ?>" <?php echo ($report['type'] === 'Lipid Profile') ? 'required' : 'disabled'; ?>>
                <span class="form-hint">Normal: Below 150 mg/dL</span>
            </div>
            <div>
                <label for="hdl_cholesterol">HDL Cholesterol (mg/dL)</label>
                <input type="number" id="hdl_cholesterol" name="hdl_cholesterol" step="0.01" min="1" max="300" value="<?php echo htmlspecialchars($results['HDL Cholesterol (mg/dL)'] ?? ''); ?>" <?php echo ($report['type'] === 'Lipid Profile') ? 'required' : 'disabled'; ?>>
                <span class="form-hint">Optimal: Above 40 mg/dL</span>
            </div>
            <div>
                <label for="ldl_cholesterol">LDL Cholesterol (mg/dL)</label>
                <input type="number" id="ldl_cholesterol" name="ldl_cholesterol" step="0.01" min="1" max="800" value="<?php echo htmlspecialchars($results['LDL Cholesterol (mg/dL)'] ?? ''); ?>" <?php echo ($report['type'] === 'Lipid Profile') ? 'required' : 'disabled'; ?>>
                <span class="form-hint">Optimal: Below 100 mg/dL</span>
            </div>
        </div>

        <div id="panel_sugar" class="field-box" style="<?php echo ($report['type'] === 'Blood Sugar') ? 'display: block;' : 'display: none;'; ?>">
            <h3>Blood Sugar Parameters</h3>
            <div>
                <label for="fasting_blood_sugar">Fasting Blood Sugar (mg/dL)</label>
                <input type="number" id="fasting_blood_sugar" name="fasting_blood_sugar" step="0.01" min="10" max="1200" value="<?php echo htmlspecialchars($results['Fasting Blood Sugar (mg/dL)'] ?? ''); ?>" <?php echo ($report['type'] === 'Blood Sugar') ? 'required' : 'disabled'; ?>>
                <span class="form-hint">Normal: 70 - 99 mg/dL</span>
            </div>
            <div>
                <label for="postprandial_blood_sugar">Postprandial Blood Sugar (mg/dL)</label>
                <input type="number" id="postprandial_blood_sugar" name="postprandial_blood_sugar" step="0.01" min="10" max="1500" value="<?php echo htmlspecialchars($results['Postprandial Blood Sugar (mg/dL)'] ?? ''); ?>" <?php echo ($report['type'] === 'Blood Sugar') ? 'required' : 'disabled'; ?>>
                <span class="form-hint">Normal: Below 140 mg/dL</span>
            </div>
            <div>
                <label for="hba1c">HbA1c (%)</label>
                <input type="number" id="hba1c" name="hba1c" step="0.01" min="1" max="25" value="<?php echo htmlspecialchars($results['HbA1c (%)'] ?? ''); ?>" <?php echo ($report['type'] === 'Blood Sugar') ? 'required' : 'disabled'; ?>>
                <span class="form-hint">Normal: Below 5.7 %</span>
            </div>
        </div>

        <div class="button-group" style="margin-top: 24px; padding-top: 16px; border-top: 1px solid #000000;">
            <button type="submit" class="button">Update Report</button>
            <a href="index.php?action=report_view&id=<?php echo urlencode($report['id']); ?>" class="button secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
function toggleTestPanels(selectedType) {
    var panelFbc = document.getElementById('panel_fbc');
    var panelLipid = document.getElementById('panel_lipid');
    var panelSugar = document.getElementById('panel_sugar');

    var isFbc = (selectedType === 'FBC');
    var isLipid = (selectedType === 'Lipid Profile');
    var isSugar = (selectedType === 'Blood Sugar');

    panelFbc.style.display = isFbc ? 'block' : 'none';
    panelLipid.style.display = isLipid ? 'block' : 'none';
    panelSugar.style.display = isSugar ? 'block' : 'none';

    setInputsState(panelFbc, isFbc);
    setInputsState(panelLipid, isLipid);
    setInputsState(panelSugar, isSugar);
}

function setInputsState(container, isEnabled) {
    var inputs = container.querySelectorAll('input');
    for (var i = 0; i < inputs.length; i++) {
        inputs[i].disabled = !isEnabled;
        inputs[i].required = isEnabled;
    }
}
</script>

<?php require __DIR__ . '/footer.php'; ?>
