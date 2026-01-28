<?php
$naslovStranice = 'Historija treninga';
require_once 'includes/header.php';

$korisnikId = $trenutniKorisnik['id'];

$mjesec = isset($_GET['mjesec']) ? (int)$_GET['mjesec'] : date('n');
$godina = isset($_GET['godina']) ? (int)$_GET['godina'] : date('Y');

$historija = dohvatiRezervacijeKorisnika($korisnikId, $mjesec, $godina);
$brojTreninga = count(array_filter($historija, function($h) { return $h['status'] === 'prisutan'; }));
?>

<div class="card" style="margin-bottom: 1.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 class="card-title" style="margin: 0;"><?= imeMjeseca($mjesec) ?> <?= $godina ?></h2>
            <p style="color: var(--zinc-500); margin-top: 0.25rem;">Odradjeno treninga: <strong style="color: var(--white);"><?= $brojTreninga ?></strong></p>
        </div>
        <div style="display: flex; gap: 0.5rem;">
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
                    <th>Datum</th>
                    <th>Grupa</th>
                    <th>Vrijeme</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($historija): ?>
                    <?php foreach ($historija as $h): ?>
                        <tr>
                            <td><?= formatirajDatum($h['datum']) ?></td>
                            <td><strong><?= $h['grupa_naziv'] ?></strong></td>
                            <td><?= substr($h['vrijeme_pocetka'], 0, 5) ?> - <?= substr($h['vrijeme_zavrsetka'], 0, 5) ?></td>
                            <td>
                                <?php if ($h['status'] === 'prisutan'): ?>
                                    <span class="badge badge-success">Prisutan</span>
                                <?php elseif ($h['status'] === 'odsutan'): ?>
                                    <span class="badge badge-danger">Odsutan</span>
                                <?php elseif ($h['status'] === 'otkazano'): ?>
                                    <span class="badge badge-warning">Otkazano</span>
                                <?php else: ?>
                                    <span class="badge badge-info">Rezervisano</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; color: var(--zinc-500);">Nema podataka za ovaj mjesec.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>