<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Report Generator</title>
    <link rel="icon" type="image/svg+xml" href="favicon.svg">
    <style>
        * {
            box-sizing: border-box;
            box-shadow: none !important;
            transition: none !important;
            animation: none !important;
            border-radius: 0 !important;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            background-color: #fbfbfb;
            color: #000000;
            margin: 0;
            padding: 0;
            line-height: 1.5;
            font-size: 14px;
        }
        .header-bar {
            background-color: #000000;
            color: #ffffff;
            padding: 12px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #000000;
        }
        .brand-title {
            color: #ffffff;
            text-decoration: none;
            font-size: 16px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .brand-badge {
            background-color: #ffffff;
            color: #000000;
            font-size: 10px;
            font-weight: 900;
            padding: 2px 6px;
            letter-spacing: 0.5px;
        }
        .nav-links {
            display: flex;
            gap: 16px;
            align-items: center;
        }
        .nav-links a {
            color: #cccccc;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 6px 10px;
            border: 1px solid transparent;
        }
        .nav-links a:hover,
        .nav-links a.active {
            color: #ffffff;
            border-color: #ffffff;
            background-color: #1a1a1a;
        }
        .user-tag {
            color: #ffffff;
            font-size: 13px;
            border-right: 1px solid #444444;
            padding-right: 16px;
            margin-right: 4px;
        }
        .container {
            max-width: 1040px;
            margin: 24px auto;
            padding: 0 20px;
        }
        .panel {
            border: 1px solid #000000;
            padding: 24px;
            margin-bottom: 24px;
            background-color: #ffffff;
        }
        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #000000;
            padding-bottom: 14px;
            margin-bottom: 20px;
        }
        .panel-header h2,
        .panel-header h3 {
            margin: 0;
        }
        h1, h2, h3, h4 {
            margin-top: 0;
            color: #000000;
            font-weight: 800;
            letter-spacing: -0.2px;
        }
        h2 {
            font-size: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        h3 {
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            margin-bottom: 16px;
            font-size: 13px;
            background-color: #ffffff;
        }
        table, th, td {
            border: 1px solid #000000;
        }
        th {
            background-color: #000000;
            color: #ffffff;
            padding: 10px 14px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-weight: 800;
        }
        td {
            padding: 10px 14px;
            text-align: left;
            vertical-align: middle;
        }
        tr:nth-child(even) td {
            background-color: #f9f9f9;
        }
        tr:hover td {
            background-color: #f0f0f0;
        }
        label {
            display: block;
            margin-top: 14px;
            margin-bottom: 6px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #222222;
        }
        input[type="text"],
        input[type="number"],
        input[type="password"],
        select {
            width: 100%;
            height: 38px;
            padding: 8px 12px;
            border: 1px solid #000000;
            background-color: #ffffff;
            color: #000000;
            font-size: 14px;
            box-sizing: border-box;
            outline: none;
        }
        input[type="text"]:focus,
        input[type="number"]:focus,
        input[type="password"]:focus,
        select:focus {
            outline: 2px solid #000000;
            outline-offset: -1px;
            background-color: #ffffff;
        }
        input:disabled,
        select:disabled {
            background-color: #eeeeee;
            color: #777777;
            border-color: #cccccc;
            cursor: not-allowed;
        }
        .form-hint {
            font-size: 11px;
            color: #555555;
            margin-top: 4px;
            display: block;
        }
        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 38px;
            padding: 0 16px;
            background-color: #000000;
            color: #ffffff;
            border: 1px solid #000000;
            text-decoration: none;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            cursor: pointer;
            box-sizing: border-box;
            white-space: nowrap;
        }
        .button:hover {
            background-color: #222222;
            color: #ffffff;
        }
        .button.secondary {
            background-color: #ffffff;
            color: #000000;
            border: 1px solid #000000;
        }
        .button.secondary:hover {
            background-color: #eeeeee;
        }
        .button.danger {
            background-color: #ffffff;
            color: #dc3545;
            border: 1px solid #dc3545;
        }
        .button.danger:hover {
            background-color: #dc3545;
            color: #ffffff;
        }
        .button-small {
            height: 28px;
            padding: 0 10px;
            font-size: 11px;
        }
        .button-group {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #000000;
            background-color: #ffffff;
            color: #000000;
            white-space: nowrap;
        }
        .badge.badge-dark {
            background-color: #000000;
            color: #ffffff;
        }
        .badge.badge-grey {
            background-color: #eeeeee;
            border-color: #999999;
            color: #333333;
        }
        .alert-success {
            border: 1px solid #28a745;
            background-color: #d4edda;
            color: #155724;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 13px;
        }
        .alert-error {
            border: 1px solid #dc3545;
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 13px;
        }
        .alert-warning {
            border: 1px solid #ffc107;
            background-color: #fff3cd;
            color: #856404;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 13px;
        }
        .field-box {
            border: 1px solid #000000;
            padding: 16px;
            margin-top: 16px;
            margin-bottom: 16px;
            background-color: #fbfbfb;
        }
        .info-card {
            background-color: #f7f7f7;
            border: 1px solid #000000;
            padding: 16px;
            margin-bottom: 20px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
        }
        .info-item {
            font-size: 13px;
        }
        .info-label {
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 800;
            color: #666666;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 2px;
        }
        .info-value {
            font-weight: 700;
            color: #000000;
        }
        .inline-form {
            display: inline-block;
            margin: 0;
            padding: 0;
        }
        .actions-cell {
            white-space: nowrap;
            text-align: right;
        }
        .actions-cell .button-group {
            justify-content: flex-end;
        }
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.75);
            z-index: 10000;
            align-items: center;
            justify-content: center;
        }
        .modal-window {
            background-color: #ffffff;
            border: 2px solid #000000;
            width: 90%;
            max-width: 960px;
            height: 85vh;
            display: flex;
            flex-direction: column;
            padding: 16px;
            box-sizing: border-box;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
            padding-bottom: 12px;
            border-bottom: 1px solid #000000;
        }
        .modal-body {
            flex: 1;
            width: 100%;
            height: 100%;
        }
        .modal-body iframe {
            width: 100%;
            height: 100%;
            border: 1px solid #000000;
        }
    </style>
</head>
<body>
    <div class="header-bar">
        <div>
            <a href="index.php?action=patients" class="brand-title">
                BLOOD REPORT GENERATOR
                <span class="brand-badge">PRO</span>
            </a>
        </div>
        <div class="nav-links">
            <?php if (isset($_SESSION['doctor_id'])): ?>
                <span class="user-tag">Doctor: <strong><?php echo htmlspecialchars(($_SESSION['doctor_display_name'] ?? $_SESSION['doctor_username'])); ?></strong></span>
                <a href="index.php?action=patients" class="<?php echo (($_GET['action'] ?? 'patients') === 'patients') ? 'active' : ''; ?>">Patients</a>
                <a href="index.php?action=account" class="<?php echo (($_GET['action'] ?? '') === 'account') ? 'active' : ''; ?>">Account</a>
                <a href="index.php?action=logout">Logout</a>
            <?php else: ?>
                <a href="index.php?action=login" class="<?php echo (($_GET['action'] ?? '') === 'login') ? 'active' : ''; ?>">Login</a>
                <a href="index.php?action=register" class="<?php echo (($_GET['action'] ?? '') === 'register') ? 'active' : ''; ?>">Register</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="container">
