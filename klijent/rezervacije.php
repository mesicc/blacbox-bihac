<?php
$naslovStranice = 'Rezervisi trening';
require_once 'includes/header.php';

$korisnikId = $trenutniKorisnik['id'];

// Obrada rezervacije
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $akcija = $_POST['akcija'] ?? '';
    
    if ($akcija === 'rezervisi') {
        $treningId = (int)$_POST['trening_id'];
        
        try {
            kreirajRezervaciju($korisnikId, $treningId);
            prikaziPoruku('uspjeh', 'Trening uspjesno rezervisan!');
        } catch (PDOException $e) {
            prikaziPoruku('greska', 'Vec ste rezervisali ovaj trening.');
        }
        
        preusmjeri('rezervacije.php');
    }
    
    if ($akcija === 'otkazi') {
        $rezervacijaId = (int)$_POST['rezervacija_id'];
        otkaziRezervaciju($rezervacijaId);
        prikaziPoruku('uspjeh', 'Rezervacija otkazana.');
        preusmjeri('rezervacije.php');
    }
}

// Dohvati dostupne treninge
$dostupniTreninzi = dohvatiDostupneTreningeZaKorisnika($korisnikId);

// Dohvati moje rezervacije
$mojeRezervacije = dohvatiRezervacijeKorisnika($korisnikId);
$rezervisaniTreninzi = array_column(array_filter($mojeRezervacije, function($r) {
    return $r['status'] === 'rezervisano';
}), 'trening_id');
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Dostupni treninzi</h2>
    </div>
    
    <?php if ($dostupniTreninzi): ?>
        <?php 
        $treninziPoDatumu = [];
        foreach ($dostupniTreninzi as $t) {
            $treninziPoDatumu[$t['datum']][] = $t;
        }
        ?>
        
        <?php foreach ($treninziPoDatumu as $datum => $treninzi): ?>
            <div style="margin-bottom: 1.5rem;">
                <h3 style="color: var(--zinc-400); font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.75rem;">
                    <?= formatirajDatum($datum, 'l, d.m.Y') ?>
                    <?= $datum === date('Y-m-d') ? '<span class="badge badge-success" style="margin-left: 0.5rem;">Danas</span>' : '' ?>
                </h3>
                
                <?php foreach ($treninzi as $trening): ?>
                    <?php $jeRezervisano = in_array($trening['id'], $rezervisaniTreninzi); ?>
                    <div class="trening-kartica">
                        <div class="trening-info">
                            <h4><?= $trening['grupa_naziv'] ?></h4>
                            <p><?= substr($trening['vrijeme_pocetka'], 0, 5) ?> - <?= substr($trening['vrijeme_zavrsetka'], 0, 5) ?></p>
                        </div>
                        
                        <?php if ($jeRezervisano): ?>
                            <span class="badge badge-success">Rezervisano</span>
                        <?php else: ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="akcija" value="rezervisi">
                                <input type="hidden" name="trening_id" value="<?= $trening['id'] ?>">
                                <button type="submit" class="btn btn-primary btn-small">Rezervisi</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="color: var(--zinc-500); text-align: center; padding: 2rem;">Trenutno nema dostupnih treninga za vase grupe.</p>
    <?php endif; ?>
</div>

<!-- Moje aktivne rezervacije -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Moje aktivne rezervacije</h2>
    </div>
    
    <?php 
    $aktivneRezervacije = array_filter($mojeRezervacije, function($r) {
        return $r['datum'] >= date('Y-m-d') && $r['status'] === 'rezervisano';
    });
    ?>
    
    <?php if ($aktivneRezervacije): ?>
        <?php foreach ($aktivneRezervacije as $rez): ?>
            <div class="trening-kartica">
                <div class="trening-info">
                    <h4><?= $rez['grupa_naziv'] ?></h4>
                    <p><?= formatirajDatum($rez['datum'], 'l, d.m.Y') ?> u <?= substr($rez['vrijeme_pocetka'], 0, 5) ?></p>
                </div>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="akcija" value="otkazi">
                    <input type="hidden" name="rezervacija_id" value="<?= $rez['id'] ?>">
                    <button type="submit" class="btn btn-danger btn-small" onclick="return confirm('Da li ste sigurni?')">Otkazi</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="color: var(--zinc-500); text-align: center; padding: 1rem;">Nemate aktivnih rezervacija.</p>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>