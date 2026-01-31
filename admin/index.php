<?php
$naslovStranice = 'Dashboard';
require_once 'includes/header.php';

$statistika = dohvatiStatistikuZaAdmina();
?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?= $statistika['ukupno_klijenata'] ?></div>
            <div class="stat-label">Ukupno klijenata</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?= number_format($statistika['zarada_ovaj_mjesec'], 2) ?> KM</div>
            <div class="stat-label">Zarada ovaj mjesec</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?= $statistika['placeno_ovaj_mjesec'] ?></div>
            <div class="stat-label">Placenih clanarina</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?= $statistika['neplaceno_ovaj_mjesec'] ?></div>
            <div class="stat-label">Neplacenih clanarina</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Brzi pregled - <?= imeMjeseca(date('n')) ?> <?= date('Y') ?></h2>
    </div>
    
    <div class="dashboard-grid">
        <div class="dashboard-item">
            <p class="dashboard-label">Ukupno grupa</p>
            <p class="dashboard-value"><?= $statistika['ukupno_grupa'] ?></p>
        </div>
        <div class="dashboard-item">
            <p class="dashboard-label">Prosli mjesec zarada</p>
            <p class="dashboard-value"><?= number_format($statistika['zarada_prosli_mjesec'], 2) ?> KM</p>
        </div>
        <div class="dashboard-item">
            <p class="dashboard-label">Odrzanih treninga</p>
            <p class="dashboard-value"><?= $statistika['treninzi_ovaj_mjesec'] ?></p>
        </div>
        <div class="dashboard-item">
            <p class="dashboard-label">Ukupno admina</p>
            <p class="dashboard-value"><?= $statistika['ukupno_admina'] ?></p>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
