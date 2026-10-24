<?php
$templates = ReportTemplateModel::all();
$selectedType = $_POST['report_type'] ?? $report['type'] ?? array_key_first($templates);
$formAge = (int)($patient['age'] ?? $report['patient_age']);
$formGender = $patient['gender'] ?? $report['patient_gender'];
?>

<div>
    <label for="report_type">Report Category</label>
    <select id="report_type" name="report_type" onchange="toggleTestPanels(this.value);" required>
        <?php foreach ($templates as $template): ?>
            <option value="<?php echo htmlspecialchars($template['type']); ?>" <?php echo $selectedType === $template['type'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($template['name']); ?></option>
        <?php endforeach; ?>
    </select>
</div>

<?php foreach ($templates as $template): ?>
    <?php $selected = $selectedType === $template['type']; ?>
    <div class="field-box" data-report-type="<?php echo htmlspecialchars($template['type']); ?>" style="display: <?php echo $selected ? 'block' : 'none'; ?>;">
        <h3><?php echo htmlspecialchars($template['name']); ?> Parameters</h3>
        <?php foreach ($template['fields'] as $field): ?>
            <?php
            $value = $_POST[$field['name']] ?? $results[$field['label']] ?? '';
            $value = is_scalar($value) ? (string)$value : '';
            $range = TestRanges::get($field['label'], $formAge, $formGender);
            ?>
            <div>
                <label for="<?php echo htmlspecialchars($field['name']); ?>"><?php echo htmlspecialchars($field['label']); ?></label>
                <input type="number" id="<?php echo htmlspecialchars($field['name']); ?>" name="<?php echo htmlspecialchars($field['name']); ?>" step="<?php echo htmlspecialchars((string)$field['step']); ?>" min="<?php echo htmlspecialchars((string)$field['min']); ?>" max="<?php echo htmlspecialchars((string)$field['max']); ?>" value="<?php echo htmlspecialchars($value); ?>" <?php echo $selected ? 'required' : 'disabled'; ?>>
                <span class="form-hint">Reference range: <?php echo htmlspecialchars($range['label'] ?? 'Not set - laboratory review needed'); ?></span>
                <?php if (isset($field['hint'])): ?>
                    <span class="form-hint"><?php echo htmlspecialchars($field['hint']); ?></span>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>

<script>
function toggleTestPanels(selectedType) {
    document.querySelectorAll('[data-report-type]').forEach(function (panel) {
        var selected = panel.dataset.reportType === selectedType;
        panel.style.display = selected ? 'block' : 'none';
        panel.querySelectorAll('input').forEach(function (input) {
            input.disabled = !selected;
            input.required = selected;
        });
    });
}
</script>
