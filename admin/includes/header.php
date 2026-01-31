<?php
require_once __DIR__ . '/../../includes/autentifikacija.php';
zahtijevajAdmina();

$trenutniKorisnik = dohvatiTrenutnogKorisnika();
$poruka = dohvatiPoruku();
$trenutnaStranica = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="bs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $naslovStranice ?? 'Admin Panel' ?> - <?= APP_NAZIV ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        :root {
            --sidebar-width: 250px;
        }
        
        * {
            box-sizing: border-box;
        }
        
        .admin-layout {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar */
        .admin-sidebar {
            width: var(--sidebar-width);
            background: var(--zinc-900);
            border-right: 1px solid var(--zinc-800);
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            overflow-y: auto;
            z-index: 100;
            display: flex;
            flex-direction: column;
        }
        
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--zinc-800);
        }
        
        .sidebar-logo {
            font-weight: 900;
            font-size: 1.25rem;
            background: linear-gradient(135deg, var(--red-500), var(--orange-500));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .sidebar-subtitle {
            font-size: 0.75rem;
            color: var(--zinc-500);
            margin-top: 0.25rem;
        }
        
        .sidebar-nav {
            padding: 1rem 0;
            flex: 1;
        }
        
        .nav-section {
            padding: 0.5rem 1.5rem;
            margin-top: 1rem;
        }
        
        .nav-section-title {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--zinc-600);
            margin-bottom: 0.5rem;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.5rem;
            color: var(--zinc-400);
            text-decoration: none;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }
        
        .nav-link:hover {
            color: var(--white);
            background: rgba(255, 255, 255, 0.05);
        }
        
        .nav-link.active {
            color: var(--red-500);
            background: rgba(220, 38, 38, 0.1);
            border-left-color: var(--red-500);
        }
        
        .nav-link svg {
            width: 1.25rem;
            height: 1.25rem;
            flex-shrink: 0;
        }
        
        .sidebar-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--zinc-800);
            margin-top: auto;
        }
        
        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }
        
        .user-avatar {
            width: 2.5rem;
            height: 2.5rem;
            background: linear-gradient(135deg, var(--red-600), var(--orange-600));
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: var(--white);
            flex-shrink: 0;
            border-radius: 50%;
        }
        
        .user-info {
            flex: 1;
            min-width: 0;
        }
        
        .user-name {
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--white);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .user-role {
            font-size: 0.75rem;
            color: var(--zinc-500);
        }
        
        /* Main Content */
        .admin-main {
            flex: 1;
            margin-left: var(--sidebar-width);
            background: var(--black);
            min-height: 100vh;
        }
        
        .admin-header {
            padding: 1.5rem 2rem;
            background: var(--zinc-900);
            border-bottom: 1px solid var(--zinc-800);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }
        
        .page-title {
            font-weight: 900;
            font-size: 1.5rem;
            color: var(--white);
        }
        
        /* Mobile Menu Toggle */
        .menu-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--white);
            cursor: pointer;
            padding: 0.5rem;
            margin-right: 0.5rem;
        }
        
        .menu-toggle svg {
            width: 1.5rem;
            height: 1.5rem;
        }
        
        /* Sidebar Overlay for Mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 99;
        }
        
        .sidebar-overlay.active {
            display: block;
        }
        
        .admin-content {
            padding: 2rem;
        }
        
        /* Cards */
        .card {
            background: var(--zinc-900);
            border: 1px solid var(--zinc-800);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--zinc-800);
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .card-title {
            font-weight: 700;
            font-size: 1.125rem;
            color: var(--white);
        }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: var(--zinc-900);
            border: 1px solid var(--zinc-800);
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .stat-icon {
            width: 3rem;
            height: 3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--red-600), var(--orange-600));
            flex-shrink: 0;
        }
        
        .stat-icon svg {
            width: 1.5rem;
            height: 1.5rem;
            color: var(--white);
        }
        
        .stat-content {
            flex: 1;
            min-width: 0;
        }
        
        .stat-value {
            font-weight: 900;
            font-size: 1.5rem;
            color: var(--white);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .stat-label {
            font-size: 0.875rem;
            color: var(--zinc-500);
        }
        
        /* Tables */
        .table-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }
        
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--zinc-800);
        }
        
        th {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--zinc-500);
            background: var(--zinc-950);
            white-space: nowrap;
        }
        
        td {
            color: var(--zinc-300);
        }
        
        tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }
        
        /* Badges */
        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            white-space: nowrap;
        }
        
        .badge-success {
            background: rgba(22, 163, 74, 0.2);
            color: var(--green-600);
        }
        
        .badge-danger {
            background: rgba(220, 38, 38, 0.2);
            color: var(--red-500);
        }
        
        .badge-warning {
            background: rgba(234, 179, 8, 0.2);
            color: var(--yellow-500);
        }
        
        .badge-info {
            background: rgba(37, 99, 235, 0.2);
            color: var(--blue-600);
        }
        
        /* Forms */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        
        .form-grupa {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .form-grupa label {
            font-size: 0.875rem;
            color: var(--zinc-400);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .form-grupa input,
        .form-grupa select,
        .form-grupa textarea {
            padding: 0.75rem 1rem;
            background: var(--black);
            border: 1px solid var(--zinc-800);
            color: var(--white);
            font-size: 1rem;
            font-family: inherit;
            width: 100%;
        }
        
        .form-grupa input:focus,
        .form-grupa select:focus,
        .form-grupa textarea:focus {
            outline: none;
            border-color: var(--red-600);
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
            flex-wrap: wrap;
        }
        
        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            cursor: pointer;
            border: none;
            font-family: inherit;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        
        .btn-small {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }
        
        .btn-primary {
            background: var(--red-600);
            color: var(--white);
        }
        
        .btn-primary:hover {
            background: var(--red-700);
        }
        
        .btn-danger {
            background: var(--red-600);
            color: var(--white);
        }
        
        .btn-secondary {
            background: var(--zinc-700);
            color: var(--white);
        }
        
        .btn-secondary:hover {
            background: var(--zinc-600);
        }
        
        .btn-outline {
            background: transparent;
            border: 1px solid var(--zinc-700);
            color: var(--white);
        }
        
        .btn-outline:hover {
            background: var(--zinc-800);
        }
        
        /* Alert Messages */
        .alert {
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .alert-success {
            background: rgba(22, 163, 74, 0.1);
            border: 1px solid rgba(22, 163, 74, 0.3);
            color: var(--green-600);
        }
        
        .alert-error {
            background: rgba(220, 38, 38, 0.1);
            border: 1px solid rgba(220, 38, 38, 0.3);
            color: var(--red-500);
        }
        
        /* Modal */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.8);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 1rem;
            overflow-y: auto;
        }
        
        .modal-overlay.active {
            display: flex;
        }
        
        .modal {
            background: var(--zinc-900);
            border: 1px solid var(--zinc-800);
            width: 100%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
            margin: auto;
        }
        
        .modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--zinc-800);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            background: var(--zinc-900);
            z-index: 1;
        }
        
        .modal-title {
            font-weight: 700;
            color: var(--white);
            font-size: 1rem;
        }
        
        .modal-close {
            background: none;
            border: none;
            color: var(--zinc-400);
            cursor: pointer;
            padding: 0.5rem;
            font-size: 1.5rem;
            line-height: 1;
        }
        
        .modal-close:hover {
            color: var(--white);
        }
        
        .modal-body {
            padding: 1.5rem;
        }
        
        .modal-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--zinc-800);
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            flex-wrap: wrap;
            position: sticky;
            bottom: 0;
            background: var(--zinc-900);
        }
        
        /* Checkbox Group */
        .checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        
        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: var(--zinc-800);
            cursor: pointer;
        }
        
        .checkbox-item input {
            accent-color: var(--red-600);
            width: auto;
        }
        
        .checkbox-item:hover {
            background: var(--zinc-700);
        }
        
        /* ==================== */
        /* RESPONSIVE STYLES    */
        /* ==================== */
        
        /* Large tablets and small desktops */
        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        /* Tablets */
        @media (max-width: 992px) {
            :root {
                --sidebar-width: 220px;
            }
            
            .admin-content {
                padding: 1.5rem;
            }
            
            .admin-header {
                padding: 1rem 1.5rem;
            }
            
            .page-title {
                font-size: 1.25rem;
            }
            
            .stat-card {
                padding: 1rem;
            }
            
            .stat-value {
                font-size: 1.25rem;
            }
        }
        
        /* Small tablets and large phones */
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                width: 280px;
            }
            
            .admin-sidebar.active {
                transform: translateX(0);
            }
            
            .admin-main {
                margin-left: 0;
            }
            
            .menu-toggle {
                display: block;
            }
            
            .admin-header {
                padding: 1rem;
            }
            
            .page-title {
                font-size: 1.125rem;
            }
            
            .admin-content {
                padding: 1rem;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.75rem;
            }
            
            .stat-card {
                padding: 1rem;
                flex-direction: column;
                text-align: center;
                gap: 0.5rem;
            }
            
            .stat-icon {
                width: 2.5rem;
                height: 2.5rem;
            }
            
            .stat-icon svg {
                width: 1.25rem;
                height: 1.25rem;
            }
            
            .stat-value {
                font-size: 1.125rem;
            }
            
            .stat-label {
                font-size: 0.75rem;
            }
            
            .card {
                padding: 1rem;
            }
            
            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
            }
            
            .card-title {
                font-size: 1rem;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .modal {
                max-width: 100%;
                margin: 0;
                max-height: 100vh;
                border-radius: 0;
            }
            
            .modal-overlay {
                padding: 0;
                align-items: flex-end;
            }
            
            .modal-footer {
                flex-direction: column-reverse;
            }
            
            .modal-footer .btn {
                width: 100%;
            }
            
            th, td {
                padding: 0.75rem 0.5rem;
                font-size: 0.875rem;
            }
            
            table {
                min-width: 500px;
            }
            
            .btn-small {
                padding: 0.4rem 0.75rem;
                font-size: 0.8rem;
            }
            
            /* Fix for card header buttons */
            .card-header > div {
                display: flex;
                flex-wrap: wrap;
                gap: 0.5rem;
                width: 100%;
            }
            
            .card-header > div select {
                flex: 1;
                min-width: 120px;
            }
        }
        
        /* Mobile phones */
        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .stat-card {
                flex-direction: row;
                text-align: left;
            }
            
            .admin-header {
                padding: 0.75rem 1rem;
            }
            
            .page-title {
                font-size: 1rem;
            }
            
            .admin-content {
                padding: 0.75rem;
            }
            
            .card {
                padding: 0.75rem;
                margin-bottom: 1rem;
            }
            
            .card-header {
                margin-bottom: 1rem;
                padding-bottom: 0.75rem;
            }
            
            .card-title {
                font-size: 0.9rem;
            }
            
            .badge {
                padding: 0.2rem 0.5rem;
                font-size: 0.65rem;
            }
            
            table {
                min-width: 400px;
            }
            
            th, td {
                padding: 0.5rem 0.4rem;
                font-size: 0.8rem;
            }
            
            .form-grupa label {
                font-size: 0.75rem;
            }
            
            .form-grupa input,
            .form-grupa select,
            .form-grupa textarea {
                padding: 0.6rem 0.75rem;
                font-size: 0.9rem;
            }
            
            .checkbox-group {
                gap: 0.5rem;
            }
            
            .checkbox-item {
                padding: 0.4rem 0.75rem;
                font-size: 0.85rem;
            }
            
            .sidebar-header {
                padding: 1rem;
            }
            
            .sidebar-logo {
                font-size: 1.1rem;
            }
            
            .nav-link {
                padding: 0.6rem 1rem;
                font-size: 0.9rem;
            }
            
            .sidebar-footer {
                padding: 1rem;
            }
            
            .user-avatar {
                width: 2rem;
                height: 2rem;
                font-size: 0.75rem;
            }
            
            .user-name {
                font-size: 0.8rem;
            }
            
            .user-role {
                font-size: 0.7rem;
            }
            
            .alert {
                padding: 0.75rem 1rem;
                font-size: 0.875rem;
            }
        }
        
        /* Extra small phones */
        @media (max-width: 360px) {
            .admin-sidebar {
                width: 100%;
            }
            
            .stat-card {
                padding: 0.75rem;
            }
            
            .stat-icon {
                width: 2rem;
                height: 2rem;
            }
            
            .stat-icon svg {
                width: 1rem;
                height: 1rem;
            }
            
            .stat-value {
                font-size: 1rem;
            }
            
            .stat-label {
                font-size: 0.7rem;
            }
            
            table {
                min-width: 350px;
            }
            
            th, td {
                padding: 0.4rem 0.3rem;
                font-size: 0.75rem;
            }
        }
        
        /* Print styles */
        @media print {
            .admin-sidebar,
            .menu-toggle,
            .btn,
            .modal-overlay {
                display: none !important;
            }
            
            .admin-main {
                margin-left: 0 !important;
            }
            
            .admin-content {
                padding: 0 !important;
            }
            
            .card {
                border: 1px solid #ccc;
                break-inside: avoid;
            }
        }
    </style>
</head>
<body class="dark-mode">
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">BLACKBOX BIHAC</div>
                <div class="sidebar-subtitle">Admin Panel</div>
            </div>
            
            <nav class="sidebar-nav">
                <a href="index.php" class="nav-link <?= $trenutnaStranica === 'index' ? 'active' : '' ?>">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Dashboard
                </a>
                
                <div class="nav-section">
                    <div class="nav-section-title">Upravljanje</div>
                </div>
                
                <a href="korisnici.php" class="nav-link <?= $trenutnaStranica === 'korisnici' ? 'active' : '' ?>">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    Korisnici
                </a>
                
                <a href="grupe.php" class="nav-link <?= $trenutnaStranica === 'grupe' ? 'active' : '' ?>">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Grupe
                </a>
                
                <a href="termini.php" class="nav-link <?= $trenutnaStranica === 'termini' ? 'active' : '' ?>">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Termini
                </a>
                
                <a href="clanarine.php" class="nav-link <?= $trenutnaStranica === 'clanarine' ? 'active' : '' ?>">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Clanarine
                </a>
                
                <div class="nav-section">
                    <div class="nav-section-title">Izvjestaji</div>
                </div>
                
                <a href="izvjestaji.php" class="nav-link <?= $trenutnaStranica === 'izvjestaji' ? 'active' : '' ?>">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Mjesecni izvjestaj
                </a>
                
                <a href="statistika.php" class="nav-link <?= $trenutnaStranica === 'statistika' ? 'active' : '' ?>">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Statistika
                </a>
            </nav>
            
            <div class="sidebar-footer">
                <div class="sidebar-user">
                    <div class="user-avatar"><?= strtoupper(substr($trenutniKorisnik['ime'], 0, 1) . substr($trenutniKorisnik['prezime'], 0, 1)) ?></div>
                    <div class="user-info">
                        <div class="user-name"><?= $trenutniKorisnik['ime'] . ' ' . $trenutniKorisnik['prezime'] ?></div>
                        <div class="user-role"><?= $trenutniKorisnik['uloga'] === 'glavni_admin' ? 'Glavni Admin' : 'Admin' ?></div>
                    </div>
                </div>
                <a href="../odjava.php" class="btn btn-outline btn-small" style="width: 100%; text-align: center;">Odjavi se</a>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="admin-main">
            <header class="admin-header">
                <div style="display: flex; align-items: center;">
                    <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <h1 class="page-title"><?= $naslovStranice ?? 'Dashboard' ?></h1>
                </div>
            </header>
            
            <div class="admin-content">
                <?php if ($poruka): ?>
                    <div class="alert alert-<?= $poruka['tip'] === 'uspjeh' ? 'success' : 'error' ?>">
                        <?= $poruka['tekst'] ?>
                    </div>
                <?php endif; ?>
