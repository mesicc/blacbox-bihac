<?php
$naslovStranice = 'Statistika';
require_once 'includes/header.php';

$statistika = dohvatiStatistikuZaAdmina();

// Zarada po mjesecima (zadnjih 6 mjeseci)
global $konekcija;
$zaradaPoMjesecima = [];
for ($i = 5; $i >= 0; $i--) {
    $mj = date('n', strtotime("-$i months"));
    $god = date('Y', strtotime("-$i months"));
    
    $upit = $konekcija->prepare("SELECT COALESCE(SUM(iznos), 0) as zarada FROM clanarine WHERE placeno = 1 AND mjesec = ? AND godina = ?");
    $upit->execute([$mj, $god]);
    
    $zaradaPoMjesecima[] = [
        'mjesec' => imeMjeseca($mj) . ' ' . $god,
        'zarada' => $upit->fetch()['zarada']
    ];
}

// Top 10 klijenata po broju treninga ovog mjeseca
$upit = $konekcija->prepare("
    SELECT k.ime, k.prezime, COUNT(r.id) as broj_treninga
    FROM korisnici k
    LEFT JOIN rezervacije r ON k.id = r.korisnik_id AND r.status = 'prisutan'
    LEFT JOIN treninzi t ON r.trening_id = t.id AND MONTH(t.datum) = ? AND YEAR(t.datum) = ?
    WHERE k.uloga = 'klijent' AND k.aktivan = 1
    GROUP BY k.id
    ORDER BY broj_treninga DESC
    LIMIT 10
");
$upit->execute([date('n'), date('Y')]);
$topKlijenti = $upit->fetchAll();

// Klijenti po grupama
$klijentiPoGrupama = [];
$grupe = dohvatiSveGrupe();
foreach ($grupe as $grupa) {
    $klijentiPoGrupama[] = [
        'naziv' => $grupa['naziv'],
        'broj' => count(dohvatiKorisnikeGrupe($grupa['id']))
    ];
}
?>

<div class="stats-grid">
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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?= number_format($statistika['zarada_prosli_mjesec'], 2) ?> KM</div>
            <div class="stat-label">Zarada prosli mjesec</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?= $statistika['treninzi_ovaj_mjesec'] ?></div>
            <div class="stat-label">Treninga ovaj mjesec</div>
        </div>
    </div>
</div>

<div class="statistika-grid">
    <!-- Zarada po mjesecima -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Zarada po mjesecima</h2>
        </div>
        
        <div class="zarada-lista">
            <?php foreach ($zaradaPoMjesecima as $mj): ?>
                <div class="zarada-item">
                    <div class="zarada-info">
                        <span class="zarada-mjesec"><?= $mj['mjesec'] ?></span>
                        <span class="zarada-iznos"><?= number_format($mj['zarada'], 2) ?> KM</span>
                    </div>
                    <div class="zarada-bar">
                        <?php $maxZarada = max(array_column($zaradaPoMjesecima, 'zarada')) ?: 1; ?>
                        <div class="zarada-bar-fill" style="width: <?= ($mj['zarada'] / $maxZarada) * 100 ?>%;"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Top klijenti -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Top 10 klijenata (<?= imeMjeseca(date('n')) ?>)</h2>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Klijent</th>
                        <th>Treninzi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $rbr = 1; foreach ($topKlijenti as $klijent): ?>
                        <tr>
                            <td><?= $rbr++ ?></td>
                            <td><?= $klijent['ime'] . ' ' . $klijent['prezime'] ?></td>
                            <td><strong><?= $klijent['broj_treninga'] ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Mobile Top Klijenti -->
        <div class="mobile-top-list">
            <?php $rbr = 1; foreach ($topKlijenti as $klijent): ?>
                <div class="top-item">
                    <div class="top-rank"><?= $rbr++ ?></div>
                    <div class="top-info">
                        <span class="top-name"><?= $klijent['ime'] . ' ' . $klijent['prezime'] ?></span>
                        <span class="top-treninzi"><?= $klijent['broj_treninga'] ?> treninga</span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Klijenti po grupama -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Klijenti po grupama</h2>
        </div>
        
        <div class="grupe-lista">
            <?php foreach ($klijentiPoGrupama as $grupa): ?>
                <div class="grupa-item">
                    <span class="grupa-naziv"><?= $grupa['naziv'] ?></span>
                    <span class="badge badge-info"><?= $grupa['broj'] ?> clanova</span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
