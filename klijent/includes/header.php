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
    <link rel="stylesheet" href="../css/klijent.css">
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
                <button class="hamburger" id="hamburgerBtn">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <h1 class="page-title"><?= $naslovStranice ?? 'Dashboard' ?></h1>
            </header>

            <div class="sidebar-overlay" id="sidebarOverlay"></div>
            
            <div class="klijent-content">
                <?php if ($poruka): ?>
                    <div class="alert alert-<?= $poruka['tip'] === 'uspjeh' ? 'success' : 'error' ?>">
                        <?= $poruka['tekst'] ?>
                    </div>
                <?php endif; ?>