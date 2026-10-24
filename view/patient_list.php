<?php require __DIR__ . '/header.php'; ?>

<div class="panel">
    <div class="panel-header">
        <div>
            <h2>My Patients</h2>
            <div class="form-hint">Manage your registered patients and their blood test records</div>
        </div>
        <div>
            <a href="index.php?action=patient_create" class="button">Register New Patient</a>
        </div>
    </div>

    <?php if (isset($_GET['created'])): ?>
        <div class="alert-success">Patient registered successfully.</div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert-error">The requested record could not be found or access is denied.</div>
    <?php endif; ?>

    <div class="info-card" style="margin-bottom: 24px;">
        <form method="GET" action="index.php">
            <input type="hidden" name="action" value="patients">

            <div style="margin-bottom: 14px;">
                <label for="search" style="margin-top: 0;">Search Patients</label>
                <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>" placeholder="Search by patient full name or patient ID...">
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 14px; margin-bottom: 16px;">
                <div>
                    <label for="gender" style="margin-top: 0;">Gender</label>
                    <select id="gender" name="gender">
                        <option value="">All Genders</option>
                        <option value="Male" <?php echo ($genderFilter === 'Male') ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo ($genderFilter === 'Female') ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo ($genderFilter === 'Other') ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>

                <div>
                    <label for="min_age" style="margin-top: 0;">Min Age</label>
                    <input type="number" id="min_age" name="min_age" min="1" max="125" placeholder="e.g. 18" value="<?php echo ($minAge !== null) ? htmlspecialchars((string)$minAge) : ''; ?>">
                </div>

                <div>
                    <label for="max_age" style="margin-top: 0;">Max Age</label>
                    <input type="number" id="max_age" name="max_age" min="1" max="125" placeholder="e.g. 65" value="<?php echo ($maxAge !== null) ? htmlspecialchars((string)$maxAge) : ''; ?>">
                </div>

                <div>
                    <label for="report_status" style="margin-top: 0;">Report Status</label>
                    <select id="report_status" name="report_status">
                        <option value="">All Patients</option>
                        <option value="with_reports" <?php echo ($reportStatusFilter === 'with_reports') ? 'selected' : ''; ?>>With Reports</option>
                        <option value="without_reports" <?php echo ($reportStatusFilter === 'without_reports') ? 'selected' : ''; ?>>Without Reports</option>
                    </select>
                </div>

                <div>
                    <label for="sort" style="margin-top: 0;">Sort By</label>
                    <select id="sort" name="sort">
                        <option value="id_desc" <?php echo ($sortOption === 'id_desc') ? 'selected' : ''; ?>>Newest Registered</option>
                        <option value="id_asc" <?php echo ($sortOption === 'id_asc') ? 'selected' : ''; ?>>Oldest Registered</option>
                        <option value="name_asc" <?php echo ($sortOption === 'name_asc') ? 'selected' : ''; ?>>Name (A - Z)</option>
                        <option value="name_desc" <?php echo ($sortOption === 'name_desc') ? 'selected' : ''; ?>>Name (Z - A)</option>
                        <option value="age_asc" <?php echo ($sortOption === 'age_asc') ? 'selected' : ''; ?>>Age (Lowest First)</option>
                        <option value="age_desc" <?php echo ($sortOption === 'age_desc') ? 'selected' : ''; ?>>Age (Highest First)</option>
                        <option value="reports_desc" <?php echo ($sortOption === 'reports_desc') ? 'selected' : ''; ?>>Most Reports First</option>
                    </select>
                </div>
            </div>

            <div style="display: flex; gap: 10px; align-items: center; justify-content: space-between; border-top: 1px solid #cccccc; padding-top: 12px;">
                <div class="button-group">
                    <button type="submit" class="button">Apply Filter & Sort</button>
                    <?php if ($hasActiveFilters): ?>
                        <a href="index.php?action=patients" class="button secondary">Clear Filters</a>
                    <?php endif; ?>
                </div>
                <div style="font-size: 13px; font-weight: 700; color: #444444;">
                    Matching patients: <span class="badge badge-grey"><?php echo count($patients); ?></span>
                </div>
            </div>
        </form>
    </div>

    <?php if (empty($patients)): ?>
        <?php if ($hasActiveFilters): ?>
            <div class="alert-warning" style="display: flex; justify-content: space-between; align-items: center;">
                <span>No patients match your current search and filter criteria.</span>
                <a href="index.php?action=patients" class="button secondary button-small">Reset All Filters</a>
            </div>
        <?php else: ?>
            <div class="alert-warning" style="display: flex; justify-content: space-between; align-items: center;">
                <span>No patients have been registered yet under your account.</span>
                <a href="index.php?action=patient_create" class="button button-small">Register First Patient</a>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th style="width: 70px; text-align: center;">ID</th>
                    <th>Patient Full Name</th>
                    <th style="width: 80px; text-align: center;">Age</th>
                    <th style="width: 100px; text-align: center;">Gender</th>
                    <th style="width: 120px; text-align: center;">Reports</th>
                    <th style="width: 210px; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($patients as $patient): ?>
                    <tr>
                        <td style="text-align: center; font-weight: 800;">#<?php echo htmlspecialchars((string)$patient['id']); ?></td>
                        <td>
                            <strong style="font-size: 14px;"><?php echo htmlspecialchars($patient['name']); ?></strong>
                        </td>
                        <td style="text-align: center;"><?php echo htmlspecialchars((string)$patient['age']); ?> Yrs</td>
                        <td style="text-align: center;">
                            <span class="badge"><?php echo htmlspecialchars($patient['gender']); ?></span>
                        </td>
                        <td style="text-align: center;">
                            <?php $reportTotal = (int)($patient['report_count'] ?? 0); ?>
                            <?php if ($reportTotal > 0): ?>
                                <span class="badge badge-dark"><?php echo $reportTotal; ?> <?php echo ($reportTotal === 1) ? 'Report' : 'Reports'; ?></span>
                            <?php else: ?>
                                <span class="badge badge-grey">0 Reports</span>
                            <?php endif; ?>
                        </td>
                        <td class="actions-cell">
                            <div class="button-group">
                                <a href="index.php?action=patient_view&id=<?php echo urlencode($patient['id']); ?>" class="button button-small">View</a>
                                <a href="index.php?action=report_create&patient_id=<?php echo urlencode($patient['id']); ?>" class="button secondary button-small">+ Report</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/footer.php'; ?>
