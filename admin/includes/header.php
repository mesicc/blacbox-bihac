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
    <link rel="stylesheet" href="../css/admin.css">
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
