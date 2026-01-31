<?php
$naslovStranice = 'Clanarine';
require_once 'includes/header.php';

// Trenutni mjesec i godina
$mjesec = isset($_GET['mjesec']) ? (int)$_GET['mjesec'] : date('n');
$godina = isset($_GET['godina']) ? (int)$_GET['godina'] : date('Y');

// Obrada forme
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $korisnikId = (int)$_POST['korisnik_id'];
    $iznos = (float)$_POST['iznos'];
    $placeno = isset($_POST['placeno']) ? 1 : 0;
    $napomena = ocistiUnos($_POST['napomena'] ?? '');
    
    kreirajIliAzurirajClanarinu($korisnikId, $mjesec, $godina, $iznos, $placeno, $_SESSION['korisnik_id'], $napomena);
    
    prikaziPoruku('uspjeh', 'Clanarina uspjesno azurirana.');
    preusmjeri("clanarine.php?mjesec=$mjesec&godina=$godina");
}

// Dohvati sve klijente
$klijenti = dohvatiSveKorisnike('klijent');

// Dohvati clanarine za mjesec
$clanarine = dohvatiSveClanarineZaMjesec($mjesec, $godina);
$clanarinePoKorisniku = [];
foreach ($clanarine as $c) {
    $clanarinePoKorisniku[$c['korisnik_id']] = $c;
}
?>

<div class="card navigation-card">
    <div class="navigation-header">
        <h2 class="card-title">Clanarine - <?= imeMjeseca($mjesec) ?> <?= $godina ?></h2>
        <div class="navigation-buttons">
            <a href="?mjesec=<?= $mjesec == 1 ? 12 : $mjesec - 1 ?>&godina=<?= $mjesec == 1 ? $godina - 1 : $godina ?>" class="btn btn-secondary btn-small">&larr; Prethodni</a>
            <a href="?mjesec=<?= $mjesec == 12 ? 1 : $mjesec + 1 ?>&godina=<?= $mjesec == 12 ? $godina + 1 : $godina ?>" class="btn btn-secondary btn-small">Sljedeci &rarr;</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Klijent</th>
                    <th>Iznos (KM)</th>
                    <th>Status</th>
                    <th>Datum uplate</th>
                    <th>Napomena</th>
                    <th>Akcije</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($klijenti as $klijent): ?>
                    <?php $clanarina = $clanarinePoKorisniku[$klijent['id']] ?? null; ?>
                    <tr>
                        <td><strong><?= $klijent['ime'] . ' ' . $klijent['prezime'] ?></strong></td>
                        <td><?= $clanarina ? number_format($clanarina['iznos'], 2) : '-' ?></td>
                        <td>
                            <?php if ($clanarina && $clanarina['placeno']): ?>
                                <span class="badge badge-success">Placeno</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Neplaceno</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $clanarina && $clanarina['datum_uplate'] ? formatirajDatum($clanarina['datum_uplate']) : '-' ?></td>
                        <td><?= $clanarina ? ($clanarina['napomena'] ?: '-') : '-' ?></td>
                        <td>
                            <button class="btn btn-secondary btn-small" onclick="urediClanarinu(<?= $klijent['id'] ?>, '<?= $klijent['ime'] . ' ' . $klijent['prezime'] ?>', <?= $clanarina ? htmlspecialchars(json_encode($clanarina)) : 'null' ?>)">Uredi</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Mobile Cards View -->
<div class="mobile-cards">
    <?php foreach ($klijenti as $klijent): ?>
        <?php $clanarina = $clanarinePoKorisniku[$klijent['id']] ?? null; ?>
        <div class="mobile-card">
            <div class="mobile-card-header">
                <div>
                    <strong><?= $klijent['ime'] . ' ' . $klijent['prezime'] ?></strong>
                    <div class="mobile-card-status">
                        <?php if ($clanarina && $clanarina['placeno']): ?>
                            <span class="badge badge-success">Placeno</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Neplaceno</span>
                        <?php endif; ?>
                    </div>
                </div>
                <button class="btn btn-secondary btn-small" onclick="urediClanarinu(<?= $klijent['id'] ?>, '<?= $klijent['ime'] . ' ' . $klijent['prezime'] ?>', <?= $clanarina ? htmlspecialchars(json_encode($clanarina)) : 'null' ?>)">Uredi</button>
            </div>
            <div class="mobile-card-body">
                <div class="mobile-card-row">
                    <span class="mobile-card-label">Iznos:</span>
                    <span><?= $clanarina ? number_format($clanarina['iznos'], 2) . ' KM' : '-' ?></span>
                </div>
                <div class="mobile-card-row">
                    <span class="mobile-card-label">Datum uplate:</span>
                    <span><?= $clanarina && $clanarina['datum_uplate'] ? formatirajDatum($clanarina['datum_uplate']) : '-' ?></span>
                </div>
                <?php if ($clanarina && $clanarina['napomena']): ?>
                <div class="mobile-card-row">
                    <span class="mobile-card-label">Napomena:</span>
                    <span><?= $clanarina['napomena'] ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Modal: Uredi clanarinu -->
<div class="modal-overlay" id="modalClanarina">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Clanarina - <span id="modal_ime_klijenta"></span></h3>
            <button class="modal-close" onclick="zatvoriModal('modalClanarina')">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="korisnik_id" id="clanarina_korisnik_id">
            <div class="modal-body">
                <div class="form-grupa">
                    <label>Iznos (KM) *</label>
                    <input type="number" name="iznos" id="clanarina_iznos" step="0.01" value="50.00" required>
                </div>
                <div class="form-grupa">
                    <label class="checkbox-item">
                        <input type="checkbox" name="placeno" id="clanarina_placeno" value="1">
                        Placeno
                    </label>
                </div>
                <div class="form-grupa">
                    <label>Napomena</label>
                    <textarea name="napomena" id="clanarina_napomena" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-small" onclick="zatvoriModal('modalClanarina')">Odustani</button>
                <button type="submit" class="btn btn-primary btn-small">Sacuvaj</button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
