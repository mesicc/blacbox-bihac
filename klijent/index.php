<?php
$naslovStranice = 'Dashboard';
require_once 'includes/header.php';

$korisnikId = $trenutniKorisnik['id'];

// Statistika
$brojTreningaOvajMjesec = dohvatiBrojTreningaKorisnikaZaMjesec($korisnikId, date('n'), date('Y'));
$clanarinaTrenutna = dohvatiClanarinu($korisnikId, date('n'), date('Y'));
$mojeRezervacije = dohvatiRezervacijeKorisnika($korisnikId);
$nadolazeceRezervacije = array_filter($mojeRezervacije, function($r) {
    return $r['datum'] >= date('Y-m-d') && $r['status'] === 'rezervisano';
});
?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?= $brojTreningaOvajMjesec ?></div>
            <div class="stat-label">Treninga ovaj mjesec</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?= count($nadolazeceRezervacije) ?></div>
            <div class="stat-label">Nadolazeci treninzi</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-value">
                <?php if ($clanarinaTrenutna && $clanarinaTrenutna['placeno']): ?>
                    <span style="color: var(--green-600);">Placeno</span>
                <?php else: ?>
                    <span style="color: var(--red-500);">Neplaceno</span>
                <?php endif; ?>
            </div>
            <div class="stat-label">Clanarina <?= imeMjeseca(date('n')) ?></div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?= count($mojeGrupe) ?></div>
            <div class="stat-label">Mojih grupa</div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 1.5rem;">
    <!-- Moje grupe -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Moje grupe</h2>
        </div>
        
        <?php if ($mojeGrupe): ?>
            <?php foreach ($mojeGrupe as $grupa): ?>
                <div style="padding: 0.75rem; background: var(--zinc-800); margin-bottom: 0.5rem;">
                    <strong style="color: var(--white);"><?= $grupa['naziv'] ?></strong>
                    <?php if ($grupa['opis']): ?>
                        <p style="color: var(--zinc-500); font-size: 0.875rem; margin-top: 0.25rem;"><?= $grupa['opis'] ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color: var(--zinc-500); text-align: center;">Niste dodani u nijednu grupu.</p>
        <?php endif; ?>
    </div>
    
    <!-- Nadolazeci treninzi -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Nadolazeci treninzi</h2>
            <a href="rezervacije.php" class="btn btn-primary btn-small">Rezervisi</a>
        </div>
        
        <?php if ($nadolazeceRezervacije): ?>
            <?php foreach (array_slice($nadolazeceRezervacije, 0, 5) as $rez): ?>
                <div class="trening-kartica">
                    <div class="trening-info">
                        <h4><?= $rez['grupa_naziv'] ?></h4>
                        <p><?= formatirajDatum($rez['datum'], 'l, d.m.Y') ?> u <?= substr($rez['vrijeme_pocetka'], 0, 5) ?></p>
                    </div>
                    <span class="badge badge-warning">Rezervisano</span>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color: var(--zinc-500); text-align: center;">Nemate nadolazecih treninga.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>