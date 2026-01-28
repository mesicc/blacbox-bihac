<?php
// ============================================
// BLACKBOX BIHAC - POMOCNE FUNKCIJE
// ============================================

require_once __DIR__ . '/konekcija.php';

// ============================================
// FUNKCIJE ZA KORISNIKE
// ============================================

function dohvatiKorisnikaPoId($korisnikId) {
    global $konekcija;
    
    $upit = $konekcija->prepare("SELECT * FROM korisnici WHERE id = ?");
    $upit->execute([$korisnikId]);
    
    return $upit->fetch();
}

function dohvatiKorisnikaPoKorisnickomImenu($korisnickoIme) {
    global $konekcija;
    
    $upit = $konekcija->prepare("SELECT * FROM korisnici WHERE korisnicko_ime = ?");
    $upit->execute([$korisnickoIme]);
    
    return $upit->fetch();
}

function dohvatiSveKorisnike($uloga = null) {
    global $konekcija;
    
    if ($uloga) {
        $upit = $konekcija->prepare("SELECT * FROM korisnici WHERE uloga = ? ORDER BY ime, prezime");
        $upit->execute([$uloga]);
    } else {
        $upit = $konekcija->query("SELECT * FROM korisnici ORDER BY uloga, ime, prezime");
    }
    
    return $upit->fetchAll();
}

function kreirajKorisnika($podaci) {
    global $konekcija;
    
    $lozinkaHash = password_hash($podaci['lozinka'], PASSWORD_DEFAULT);
    
    $upit = $konekcija->prepare("
        INSERT INTO korisnici (korisnicko_ime, lozinka, ime, prezime, email, telefon, uloga, kreirao_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $upit->execute([
        $podaci['korisnicko_ime'],
        $lozinkaHash,
        $podaci['ime'],
        $podaci['prezime'],
        $podaci['email'] ?? null,
        $podaci['telefon'] ?? null,
        $podaci['uloga'],
        $podaci['kreirao_id']
    ]);
    
    return $konekcija->lastInsertId();
}

function azurirajKorisnika($korisnikId, $podaci) {
    global $konekcija;
    
    $poljaZaAzuriranje = [];
    $vrijednosti = [];
    
    foreach (['ime', 'prezime', 'email', 'telefon', 'aktivan'] as $polje) {
        if (isset($podaci[$polje])) {
            $poljaZaAzuriranje[] = "$polje = ?";
            $vrijednosti[] = $podaci[$polje];
        }
    }
    
    if (isset($podaci['lozinka']) && !empty($podaci['lozinka'])) {
        $poljaZaAzuriranje[] = "lozinka = ?";
        $vrijednosti[] = password_hash($podaci['lozinka'], PASSWORD_DEFAULT);
    }
    
    $vrijednosti[] = $korisnikId;
    
    $upit = $konekcija->prepare("UPDATE korisnici SET " . implode(', ', $poljaZaAzuriranje) . " WHERE id = ?");
    
    return $upit->execute($vrijednosti);
}

function obrisiKorisnika($korisnikId) {
    global $konekcija;
    
    $upit = $konekcija->prepare("DELETE FROM korisnici WHERE id = ?");
    
    return $upit->execute([$korisnikId]);
}

// ============================================
// FUNKCIJE ZA GRUPE
// ============================================

function dohvatiSveGrupe() {
    global $konekcija;
    
    return $konekcija->query("SELECT * FROM grupe WHERE aktivna = 1 ORDER BY naziv")->fetchAll();
}

function dohvatiGrupu($grupaId) {
    global $konekcija;
    
    $upit = $konekcija->prepare("SELECT * FROM grupe WHERE id = ?");
    $upit->execute([$grupaId]);
    
    return $upit->fetch();
}

function kreirajGrupu($podaci) {
    global $konekcija;
    
    $upit = $konekcija->prepare("INSERT INTO grupe (naziv, opis, kapacitet) VALUES (?, ?, ?)");
    $upit->execute([$podaci['naziv'], $podaci['opis'] ?? '', $podaci['kapacitet'] ?? 20]);
    
    return $konekcija->lastInsertId();
}

function dohvatiGrupeKorisnika($korisnikId) {
    global $konekcija;
    
    $upit = $konekcija->prepare("
        SELECT g.* FROM grupe g
        JOIN korisnik_grupe kg ON g.id = kg.grupa_id
        WHERE kg.korisnik_id = ? AND g.aktivna = 1
    ");
    $upit->execute([$korisnikId]);
    
    return $upit->fetchAll();
}

function dodajKorisnikaUGrupu($korisnikId, $grupaId) {
    global $konekcija;
    
    $upit = $konekcija->prepare("INSERT IGNORE INTO korisnik_grupe (korisnik_id, grupa_id) VALUES (?, ?)");
    
    return $upit->execute([$korisnikId, $grupaId]);
}

function ukloniKorisnikaIzGrupe($korisnikId, $grupaId) {
    global $konekcija;
    
    $upit = $konekcija->prepare("DELETE FROM korisnik_grupe WHERE korisnik_id = ? AND grupa_id = ?");
    
    return $upit->execute([$korisnikId, $grupaId]);
}

function dohvatiKorisnikeGrupe($grupaId) {
    global $konekcija;
    
    $upit = $konekcija->prepare("
        SELECT k.* FROM korisnici k
        JOIN korisnik_grupe kg ON k.id = kg.korisnik_id
        WHERE kg.grupa_id = ? AND k.uloga = 'klijent' AND k.aktivan = 1
        ORDER BY k.ime, k.prezime
    ");
    $upit->execute([$grupaId]);
    
    return $upit->fetchAll();
}

// ============================================
// FUNKCIJE ZA TERMINE I TRENINGE
// ============================================

function dohvatiTermineGrupe($grupaId) {
    global $konekcija;
    
    $upit = $konekcija->prepare("SELECT * FROM termini WHERE grupa_id = ? AND aktivan = 1 ORDER BY FIELD(dan_u_sedmici, 'ponedjeljak', 'utorak', 'srijeda', 'cetvrtak', 'petak', 'subota', 'nedjelja'), vrijeme_pocetka");
    $upit->execute([$grupaId]);
    
    return $upit->fetchAll();
}

function kreirajTermin($podaci) {
    global $konekcija;
    
    $upit = $konekcija->prepare("INSERT INTO termini (grupa_id, dan_u_sedmici, vrijeme_pocetka, vrijeme_zavrsetka) VALUES (?, ?, ?, ?)");
    $upit->execute([
        $podaci['grupa_id'],
        $podaci['dan_u_sedmici'],
        $podaci['vrijeme_pocetka'],
        $podaci['vrijeme_zavrsetka']
    ]);
    
    return $konekcija->lastInsertId();
}

function dohvatiTreningeZaDatum($datum) {
    global $konekcija;
    
    $upit = $konekcija->prepare("
        SELECT t.*, te.vrijeme_pocetka, te.vrijeme_zavrsetka, te.dan_u_sedmici, g.naziv as grupa_naziv
        FROM treninzi t
        JOIN termini te ON t.termin_id = te.id
        JOIN grupe g ON te.grupa_id = g.id
        WHERE t.datum = ?
        ORDER BY te.vrijeme_pocetka
    ");
    $upit->execute([$datum]);
    
    return $upit->fetchAll();
}

function dohvatiDostupneTreningeZaKorisnika($korisnikId) {
    global $konekcija;
    
    $upit = $konekcija->prepare("
        SELECT t.*, te.vrijeme_pocetka, te.vrijeme_zavrsetka, te.dan_u_sedmici, g.naziv as grupa_naziv, g.id as grupa_id
        FROM treninzi t
        JOIN termini te ON t.termin_id = te.id
        JOIN grupe g ON te.grupa_id = g.id
        JOIN korisnik_grupe kg ON g.id = kg.grupa_id
        WHERE kg.korisnik_id = ? 
        AND t.datum >= CURDATE()
        AND t.status = 'zakazan'
        ORDER BY t.datum, te.vrijeme_pocetka
    ");
    $upit->execute([$korisnikId]);
    
    return $upit->fetchAll();
}

function kreirajTrening($terminId, $datum) {
    global $konekcija;
    
    $upit = $konekcija->prepare("INSERT IGNORE INTO treninzi (termin_id, datum) VALUES (?, ?)");
    $upit->execute([$terminId, $datum]);
    
    return $konekcija->lastInsertId();
}

// ============================================
// FUNKCIJE ZA REZERVACIJE
// ============================================

function kreirajRezervaciju($korisnikId, $treningId) {
    global $konekcija;
    
    $upit = $konekcija->prepare("INSERT INTO rezervacije (korisnik_id, trening_id) VALUES (?, ?)");
    
    return $upit->execute([$korisnikId, $treningId]);
}

function otkaziRezervaciju($rezervacijaId) {
    global $konekcija;
    
    $upit = $konekcija->prepare("UPDATE rezervacije SET status = 'otkazano' WHERE id = ?");
    
    return $upit->execute([$rezervacijaId]);
}

function azurirajStatusRezervacije($rezervacijaId, $status) {
    global $konekcija;
    
    $upit = $konekcija->prepare("UPDATE rezervacije SET status = ? WHERE id = ?");
    
    return $upit->execute([$status, $rezervacijaId]);
}

function dohvatiRezervacijeKorisnika($korisnikId, $mjesec = null, $godina = null) {
    global $konekcija;
    
    $sql = "
        SELECT r.*, t.datum, te.vrijeme_pocetka, te.vrijeme_zavrsetka, g.naziv as grupa_naziv
        FROM rezervacije r
        JOIN treninzi t ON r.trening_id = t.id
        JOIN termini te ON t.termin_id = te.id
        JOIN grupe g ON te.grupa_id = g.id
        WHERE r.korisnik_id = ?
    ";
    
    $params = [$korisnikId];
    
    if ($mjesec && $godina) {
        $sql .= " AND MONTH(t.datum) = ? AND YEAR(t.datum) = ?";
        $params[] = $mjesec;
        $params[] = $godina;
    }
    
    $sql .= " ORDER BY t.datum DESC, te.vrijeme_pocetka DESC";
    
    $upit = $konekcija->prepare($sql);
    $upit->execute($params);
    
    return $upit->fetchAll();
}

function dohvatiRezervacijeTreninga($treningId) {
    global $konekcija;
    
    $upit = $konekcija->prepare("
        SELECT r.*, k.ime, k.prezime, k.korisnicko_ime
        FROM rezervacije r
        JOIN korisnici k ON r.korisnik_id = k.id
        WHERE r.trening_id = ?
        ORDER BY k.ime, k.prezime
    ");
    $upit->execute([$treningId]);
    
    return $upit->fetchAll();
}

// ============================================
// FUNKCIJE ZA CLANARINE
// ============================================

function dohvatiClanarinu($korisnikId, $mjesec, $godina) {
    global $konekcija;
    
    $upit = $konekcija->prepare("SELECT * FROM clanarine WHERE korisnik_id = ? AND mjesec = ? AND godina = ?");
    $upit->execute([$korisnikId, $mjesec, $godina]);
    
    return $upit->fetch();
}

function dohvatiClanarineKorisnika($korisnikId) {
    global $konekcija;
    
    $upit = $konekcija->prepare("SELECT * FROM clanarine WHERE korisnik_id = ? ORDER BY godina DESC, mjesec DESC");
    $upit->execute([$korisnikId]);
    
    return $upit->fetchAll();
}

function dohvatiSveClanarineZaMjesec($mjesec, $godina) {
    global $konekcija;
    
    $upit = $konekcija->prepare("
        SELECT c.*, k.ime, k.prezime, k.korisnicko_ime
        FROM clanarine c
        JOIN korisnici k ON c.korisnik_id = k.id
        WHERE c.mjesec = ? AND c.godina = ?
        ORDER BY k.ime, k.prezime
    ");
    $upit->execute([$mjesec, $godina]);
    
    return $upit->fetchAll();
}

function kreirajIliAzurirajClanarinu($korisnikId, $mjesec, $godina, $iznos, $placeno, $adminId, $napomena = '') {
    global $konekcija;
    
    $datumUplate = $placeno ? date('Y-m-d') : null;
    
    $upit = $konekcija->prepare("
        INSERT INTO clanarine (korisnik_id, mjesec, godina, iznos, placeno, datum_uplate, upisao_admin_id, napomena)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
        iznos = VALUES(iznos),
        placeno = VALUES(placeno),
        datum_uplate = VALUES(datum_uplate),
        upisao_admin_id = VALUES(upisao_admin_id),
        napomena = VALUES(napomena)
    ");
    
    return $upit->execute([$korisnikId, $mjesec, $godina, $iznos, $placeno, $datumUplate, $adminId, $napomena]);
}

// ============================================
// FUNKCIJE ZA STATISTIKU I IZVJESTAJE
// ============================================

function dohvatiBrojTreningaKorisnikaZaMjesec($korisnikId, $mjesec, $godina) {
    global $konekcija;
    
    $upit = $konekcija->prepare("
        SELECT COUNT(*) as broj_treninga
        FROM rezervacije r
        JOIN treninzi t ON r.trening_id = t.id
        WHERE r.korisnik_id = ? 
        AND r.status = 'prisutan'
        AND MONTH(t.datum) = ? 
        AND YEAR(t.datum) = ?
    ");
    $upit->execute([$korisnikId, $mjesec, $godina]);
    
    return $upit->fetch()['broj_treninga'];
}

function dohvatiMjesecniIzvjestaj($mjesec, $godina) {
    global $konekcija;
    
    $upit = $konekcija->prepare("
        SELECT k.id, k.ime, k.prezime, k.korisnicko_ime,
               (SELECT COUNT(*) FROM rezervacije r 
                JOIN treninzi t ON r.trening_id = t.id 
                WHERE r.korisnik_id = k.id AND r.status = 'prisutan' 
                AND MONTH(t.datum) = ? AND YEAR(t.datum) = ?) as broj_treninga,
               (SELECT placeno FROM clanarine c 
                WHERE c.korisnik_id = k.id AND c.mjesec = ? AND c.godina = ?) as placeno
        FROM korisnici k
        WHERE k.uloga = 'klijent' AND k.aktivan = 1
        ORDER BY broj_treninga DESC, k.ime, k.prezime
    ");
    $upit->execute([$mjesec, $godina, $mjesec, $godina]);
    
    return $upit->fetchAll();
}

function dohvatiStatistikuZaAdmina() {
    global $konekcija;
    
    $statistika = [];
    
    // Ukupno klijenata
    $upit = $konekcija->query("SELECT COUNT(*) as broj FROM korisnici WHERE uloga = 'klijent' AND aktivan = 1");
    $statistika['ukupno_klijenata'] = $upit->fetch()['broj'];
    
    // Ukupno admina
    $upit = $konekcija->query("SELECT COUNT(*) as broj FROM korisnici WHERE uloga IN ('admin', 'glavni_admin') AND aktivan = 1");
    $statistika['ukupno_admina'] = $upit->fetch()['broj'];
    
    // Ukupno grupa
    $upit = $konekcija->query("SELECT COUNT(*) as broj FROM grupe WHERE aktivna = 1");
    $statistika['ukupno_grupa'] = $upit->fetch()['broj'];
    
    // Zarada ovog mjeseca
    $upit = $konekcija->prepare("
        SELECT COALESCE(SUM(iznos), 0) as zarada 
        FROM clanarine 
        WHERE placeno = 1 AND mjesec = ? AND godina = ?
    ");
    $upit->execute([date('n'), date('Y')]);
    $statistika['zarada_ovaj_mjesec'] = $upit->fetch()['zarada'];
    
    // Zarada proslog mjeseca
    $prosliMjesec = date('n') - 1;
    $proslaGodina = date('Y');
    if ($prosliMjesec == 0) {
        $prosliMjesec = 12;
        $proslaGodina--;
    }
    $upit = $konekcija->prepare("
        SELECT COALESCE(SUM(iznos), 0) as zarada 
        FROM clanarine 
        WHERE placeno = 1 AND mjesec = ? AND godina = ?
    ");
    $upit->execute([$prosliMjesec, $proslaGodina]);
    $statistika['zarada_prosli_mjesec'] = $upit->fetch()['zarada'];
    
    // Treninzi ovog mjeseca
    $upit = $konekcija->prepare("
        SELECT COUNT(*) as broj 
        FROM treninzi 
        WHERE status = 'odrzan' AND MONTH(datum) = ? AND YEAR(datum) = ?
    ");
    $upit->execute([date('n'), date('Y')]);
    $statistika['treninzi_ovaj_mjesec'] = $upit->fetch()['broj'];
    
    // Placeno ovaj mjesec (broj klijenata)
    $upit = $konekcija->prepare("
        SELECT COUNT(*) as broj 
        FROM clanarine 
        WHERE placeno = 1 AND mjesec = ? AND godina = ?
    ");
    $upit->execute([date('n'), date('Y')]);
    $statistika['placeno_ovaj_mjesec'] = $upit->fetch()['broj'];
    
    // Neplaceno ovaj mjesec
    $statistika['neplaceno_ovaj_mjesec'] = $statistika['ukupno_klijenata'] - $statistika['placeno_ovaj_mjesec'];
    
    return $statistika;
}

// ============================================
// POMOCNE FUNKCIJE
// ============================================

function ocistiUnos($podatak) {
    return htmlspecialchars(trim($podatak), ENT_QUOTES, 'UTF-8');
}

function preusmjeri($url) {
    header("Location: $url");
    exit;
}

function prikaziPoruku($tip, $tekst) {
    $_SESSION['poruka'] = ['tip' => $tip, 'tekst' => $tekst];
}

function dohvatiPoruku() {
    if (isset($_SESSION['poruka'])) {
        $poruka = $_SESSION['poruka'];
        unset($_SESSION['poruka']);
        return $poruka;
    }
    return null;
}

function formatirajDatum($datum, $format = 'd.m.Y') {
    return date($format, strtotime($datum));
}

function imeMjeseca($mjesec) {
    $mjeseci = [
        1 => 'Januar', 2 => 'Februar', 3 => 'Mart', 4 => 'April',
        5 => 'Maj', 6 => 'Juni', 7 => 'Juli', 8 => 'August',
        9 => 'Septembar', 10 => 'Oktobar', 11 => 'Novembar', 12 => 'Decembar'
    ];
    return $mjeseci[$mjesec] ?? '';
}

function imeDana($dan) {
    $dani = [
        'ponedjeljak' => 'Ponedjeljak',
        'utorak' => 'Utorak',
        'srijeda' => 'Srijeda',
        'cetvrtak' => 'Cetvrtak',
        'petak' => 'Petak',
        'subota' => 'Subota',
        'nedjelja' => 'Nedjelja'
    ];
    return $dani[$dan] ?? $dan;
}