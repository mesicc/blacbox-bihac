<?php
require_once __DIR__ . '/../../includes/autentifikacija.php';
zahtijevajKlijenta();

$trenutniKorisnik = dohvatiTrenutnogKorisnika();
$poruka = dohvatiPoruku();
$trenutnaStranica = basename($_SERVER['PHP_SELF'], '.php');
$mojeGrupe = dohvatiGrupeKorisnika($trenutniKorisnik['id']);
?>
<!DOCTYPE html>
<html lang="bs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $naslovStranice ?? 'Moj Profil' ?> - <?= APP_NAZIV ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        :root {
            --sidebar-width: 250px;
        }
        
        .klijent-layout {
            display: flex;
            min-height: 100vh;
        }
        
        .klijent-sidebar {
            width: var(--sidebar-width);
            background: var(--zinc-900);
            border-right: 1px solid var(--zinc-800);
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            overflow-y: auto;
            z-index: 100;
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
        }
        
        .sidebar-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--zinc-800);
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: var(--zinc-900);
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
        }
        
        .user-info {
            flex: 1;
        }
        
        .user-name {
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--white);
        }
        
        .user-role {
            font-size: 0.75rem;
            color: var(--zinc-500);
        }
        
        .klijent-main {
            flex: 1;
            margin-left: var(--sidebar-width);
            background: var(--black);
            min-height: 100vh;
        }
        
        .klijent-header {
            padding: 1.5rem 2rem;
            background: var(--zinc-900);
            border-bottom: 1px solid var(--zinc-800);
        }
        
        .page-title {
            font-weight: 900;
            font-size: 1.5rem;
            color: var(--white);
        }
        
        .klijent-content {
            padding: 2rem;
        }
        
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
        }
        
        .card-title {
            font-weight: 700;
            font-size: 1.125rem;
            color: var(--white);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
        }
        
        .stat-icon svg {
            width: 1.5rem;
            height: 1.5rem;
            color: var(--white);
        }
        
        .stat-value {
            font-weight: 900;
            font-size: 1.5rem;
            color: var(--white);
        }
        
        .stat-label {
            font-size: 0.875rem;
            color: var(--zinc-500);
        }
        
        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .badge-success { background: rgba(22, 163, 74, 0.2); color: var(--green-600); }
        .badge-danger { background: rgba(220, 38, 38, 0.2); color: var(--red-500); }
        .badge-warning { background: rgba(234, 179, 8, 0.2); color: var(--yellow-500); }
        .badge-info { background: rgba(37, 99, 235, 0.2); color: var(--blue-600); }
        
        .table-container { overflow-x: auto; }
        
        table { width: 100%; border-collapse: collapse; }
        
        th, td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--zinc-800); }
        
        th { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--zinc-500); background: var(--zinc-950); }
        
        td { color: var(--zinc-300); }
        
        tr:hover td { background: rgba(255, 255, 255, 0.02); }
        
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
        
        .form-grupa input {
            padding: 0.75rem 1rem;
            background: var(--black);
            border: 1px solid var(--zinc-800);
            color: var(--white);
            font-size: 1rem;
        }
        
        .form-grupa input:focus {
            outline: none;
            border-color: var(--red-600);
        }
        
        .btn-small { padding: 0.5rem 1rem; font-size: 0.875rem; }
        .btn-danger { background: var(--red-600); color: var(--white); }
        .btn-secondary { background: var(--zinc-700); color: var(--white); }
        
        .alert {
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .alert-success { background: rgba(22, 163, 74, 0.1); border: 1px solid rgba(22, 163, 74, 0.3); color: var(--green-600); }
        .alert-error { background: rgba(220, 38, 38, 0.1); border: 1px solid rgba(220, 38, 38, 0.3); color: var(--red-500); }
        
        .trening-kartica {
            background: var(--zinc-800);
            padding: 1rem;
            margin-bottom: 0.75rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .trening-info h4 {
            color: var(--white);
            margin-bottom: 0.25rem;
        }
        
        .trening-info p {
            color: var(--zinc-500);
            font-size: 0.875rem;
        }
        
        @media (max-width: 768px) {
            .klijent-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .klijent-sidebar.active {
                transform: translateX(0);
            }
            
            .klijent-main {
                margin-left: 0;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="dark-mode">
    <div class="klijent-layout">
        <aside class="klijent-sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">BLACKBOX BIHAC</div>
                <div class="sidebar-subtitle">Klijent Panel</div>
            </div>
            
            <nav class="sidebar-nav">
                <a href="index.php" class="nav-link <?= $trenutnaStranica === 'index' ? 'active' : '' ?>">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Dashboard
                </a>
                
                <a href="rezervacije.php" class="nav-link <?= $trenutnaStranica === 'rezervacije' ? 'active' : '' ?>">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Rezervisi trening
                </a>
                
                <a href="historija.php" class="nav-link <?= $trenutnaStranica === 'historija' ? 'active' : '' ?>">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Historija treninga
                </a>
                
                <a href="profil.php" class="nav-link <?= $trenutnaStranica === 'profil' ? 'active' : '' ?>">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Moj profil
                </a>
            </nav>
            
            <div class="sidebar-footer">
                <div class="sidebar-user">
                    <div class="user-avatar"><?= strtoupper(substr($trenutniKorisnik['ime'], 0, 1) . substr($trenutniKorisnik['prezime'], 0, 1)) ?></div>
                    <div class="user-info">
                        <div class="user-name"><?= $trenutniKorisnik['ime'] . ' ' . $trenutniKorisnik['prezime'] ?></div>
                        <div class="user-role">Klijent</div>
                    </div>
                </div>
                <a href="../odjava.php" class="btn btn-outline btn-small" style="width: 100%; text-align: center;">Odjavi se</a>
            </div>
        </aside>
        
        <main class="klijent-main">
            <header class="klijent-header">
                <h1 class="page-title"><?= $naslovStranice ?? 'Dashboard' ?></h1>
            </header>
            
            <div class="klijent-content">
                <?php if ($poruka): ?>
                    <div class="alert alert-<?= $poruka['tip'] === 'uspjeh' ? 'success' : 'error' ?>">
                        <?= $poruka['tekst'] ?>
                    </div>
                <?php endif; ?>