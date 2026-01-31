<?php
$naslovStranice = 'Mjesecni izvjestaj';
require_once 'includes/header.php';

$mjesec = isset($_GET['mjesec']) ? (int)$_GET['mjesec'] : date('n');
$godina = isset($_GET['godina']) ? (int)$_GET['godina'] : date('Y');

$izvjestaj = dohvatiMjesecniIzvjestaj($mjesec, $godina);

// Statistika za mjesec
$ukupnoTreninga = 0;
$ukupnoPlaceno = 0;
$ukupnoNeplaceno = 0;

foreach ($izvjestaj as $red) {
    $ukupnoTreninga += $red['broj_treninga'];
    if ($red['placeno']) {
        $ukupnoPlaceno++;
    } else {
        $ukupnoNeplaceno++;
    }
}
?>

<div class="card navigation-card">
    <div class="navigation-header">
        <h2 class="card-title">Izvjestaj - <?= imeMjeseca($mjesec) ?> <?= $godina ?></h2>
        <div class="navigation-buttons">
            <a href="?mjesec=<?= $mjesec == 1 ? 12 : $mjesec - 1 ?>&godina=<?= $mjesec == 1 ? $godina - 1 : $godina ?>" class="btn btn-secondary btn-small">&larr; Prethodni</a>
            <a href="?mjesec=<?= $mjesec == 12 ? 1 : $mjesec + 1 ?>&godina=<?= $mjesec == 12 ? $godina + 1 : $godina ?>" class="btn btn-secondary btn-small">Sljedeci &rarr;</a>
        </div>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?= count($izvjestaj) ?></div>
            <div class="stat-label">Aktivnih klijenata</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?= $ukupnoTreninga ?></div>
            <div class="stat-label">Odradjenih treninga</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?= $ukupnoPlaceno ?></div>
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
            <div class="stat-value"><?= $ukupnoNeplaceno ?></div>
            <div class="stat-label">Neplacenih clanarina</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Detaljan izvjestaj po klijentima</h2>
    </div>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Klijent</th>
                    <th>Broj treninga</th>
                    <th>Clanarina</th>
                </tr>
            </thead>
            <tbody>
                <?php $rbr = 1; foreach ($izvjestaj as $red): ?>
                    <tr>
                        <td><?= $rbr++ ?></td>
                        <td><strong><?= $red['ime'] . ' ' . $red['prezime'] ?></strong></td>
                        <td>
                            <span class="treninzi-broj"><?= $red['broj_treninga'] ?></span>
                        </td>
                        <td>
                            <?php if ($red['placeno']): ?>
                                <span class="badge badge-success">Placeno</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Neplaceno</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Mobile Cards View -->
<div class="mobile-cards">
    <?php $rbr = 1; foreach ($izvjestaj as $red): ?>
        <div class="mobile-card">
            <div class="mobile-card-header">
                <div class="mobile-card-rank"><?= $rbr++ ?></div>
                <div class="mobile-card-user">
                    <strong><?= $red['ime'] . ' ' . $red['prezime'] ?></strong>
                    <?php if ($red['placeno']): ?>
                        <span class="badge badge-success">Placeno</span>
                    <?php else: ?>
                        <span class="badge badge-danger">Neplaceno</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="mobile-card-body">
                <div class="mobile-card-row">
                    <span class="mobile-card-label">Broj treninga:</span>
                    <span class="treninzi-broj"><?= $red['broj_treninga'] ?></span>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<style>
    .navigation-card {
        margin-bottom: 1.5rem;
    }
    
    .navigation-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .navigation-header .card-title {
        margin: 0;
    }
    
    .navigation-buttons {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }
    
    .treninzi-broj {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--white);
    }
    
    .mobile-cards {
        display: none;
    }
    
    .mobile-card {
        background: var(--zinc-800);
        border: 1px solid var(--zinc-700);
        margin-bottom: 0.75rem;
        padding: 1rem;
    }
    
    .mobile-card-header {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 0.75rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--zinc-700);
    }
    
    .mobile-card-rank {
        width: 2rem;
        height: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--red-600), var(--orange-600));
        color: var(--white);
        font-weight: 700;
        flex-shrink: 0;
    }
    
    .mobile-card-user {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .mobile-card-user strong {
        color: var(--white);
        font-size: 1rem;
    }
    
    .mobile-card-body {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .mobile-card-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: var(--zinc-300);
        font-size: 0.875rem;
    }
    
    .mobile-card-label {
        color: var(--zinc-500);
    }
    
    @media (max-width: 768px) {
        .navigation-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .navigation-header .card-title {
            font-size: 1rem;
        }
        
        .navigation-buttons {
            width: 100%;
            justify-content: space-between;
        }
    }
    
    @media (max-width: 600px) {
        .table-container {
            display: none;
        }
        
        .mobile-cards {
            display: block;
        }
        
        .treninzi-broj {
            font-size: 1.1rem;
        }
    }
    
    @media (max-width: 480px) {
        .mobile-card {
            padding: 0.75rem;
        }
        
        .mobile-card-rank {
            width: 1.75rem;
            height: 1.75rem;
            font-size: 0.875rem;
        }
        
        .mobile-card-user strong {
            font-size: 0.9rem;
        }
    }
</style>

<?php require_once 'includes/footer.php'; ?>
