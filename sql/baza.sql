-- ============================================
-- BLACKBOX BIHAC - BAZA PODATAKA
-- ============================================

-- Kreiranje baze
CREATE DATABASE IF NOT EXISTS blackbox_bihac CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE blackbox_bihac;

-- ============================================
-- TABELA: korisnici (admini i klijenti)
-- ============================================
CREATE TABLE IF NOT EXISTS korisnici (
    id INT AUTO_INCREMENT PRIMARY KEY,
    korisnicko_ime VARCHAR(50) NOT NULL UNIQUE,
    lozinka VARCHAR(255) NOT NULL,
    ime VARCHAR(100) NOT NULL,
    prezime VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    telefon VARCHAR(20),
    uloga ENUM('glavni_admin', 'admin', 'klijent') NOT NULL DEFAULT 'klijent',
    kreirao_id INT DEFAULT NULL,
    aktivan TINYINT(1) DEFAULT 1,
    datum_kreiranja DATETIME DEFAULT CURRENT_TIMESTAMP,
    datum_azuriranja DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (kreirao_id) REFERENCES korisnici(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABELA: grupe (grupe treninga)
-- ============================================
CREATE TABLE IF NOT EXISTS grupe (
    id INT AUTO_INCREMENT PRIMARY KEY,
    naziv VARCHAR(100) NOT NULL,
    opis TEXT,
    kapacitet INT DEFAULT 20,
    aktivna TINYINT(1) DEFAULT 1,
    datum_kreiranja DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABELA: korisnik_grupe (veza korisnik-grupa)
-- ============================================
CREATE TABLE IF NOT EXISTS korisnik_grupe (
    id INT AUTO_INCREMENT PRIMARY KEY,
    korisnik_id INT NOT NULL,
    grupa_id INT NOT NULL,
    datum_dodavanja DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (korisnik_id) REFERENCES korisnici(id) ON DELETE CASCADE,
    FOREIGN KEY (grupa_id) REFERENCES grupe(id) ON DELETE CASCADE,
    UNIQUE KEY jedinstvena_veza (korisnik_id, grupa_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABELA: termini (dostupni termini treninga)
-- ============================================
CREATE TABLE IF NOT EXISTS termini (
    id INT AUTO_INCREMENT PRIMARY KEY,
    grupa_id INT NOT NULL,
    dan_u_sedmici ENUM('ponedjeljak', 'utorak', 'srijeda', 'cetvrtak', 'petak', 'subota', 'nedjelja') NOT NULL,
    vrijeme_pocetka TIME NOT NULL,
    vrijeme_zavrsetka TIME NOT NULL,
    aktivan TINYINT(1) DEFAULT 1,
    FOREIGN KEY (grupa_id) REFERENCES grupe(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABELA: treninzi (odrzani treninzi)
-- ============================================
CREATE TABLE IF NOT EXISTS treninzi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    termin_id INT NOT NULL,
    datum DATE NOT NULL,
    status ENUM('zakazan', 'odrzan', 'otkazan') DEFAULT 'zakazan',
    napomena TEXT,
    datum_kreiranja DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (termin_id) REFERENCES termini(id) ON DELETE CASCADE,
    UNIQUE KEY jedinstveni_trening (termin_id, datum)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABELA: rezervacije (prijave na trening)
-- ============================================
CREATE TABLE IF NOT EXISTS rezervacije (
    id INT AUTO_INCREMENT PRIMARY KEY,
    korisnik_id INT NOT NULL,
    trening_id INT NOT NULL,
    status ENUM('rezervisano', 'prisutan', 'odsutan', 'otkazano') DEFAULT 'rezervisano',
    datum_rezervacije DATETIME DEFAULT CURRENT_TIMESTAMP,
    datum_azuriranja DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (korisnik_id) REFERENCES korisnici(id) ON DELETE CASCADE,
    FOREIGN KEY (trening_id) REFERENCES treninzi(id) ON DELETE CASCADE,
    UNIQUE KEY jedinstvena_rezervacija (korisnik_id, trening_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABELA: clanarine (placanja)
-- ============================================
CREATE TABLE IF NOT EXISTS clanarine (
    id INT AUTO_INCREMENT PRIMARY KEY,
    korisnik_id INT NOT NULL,
    mjesec INT NOT NULL,
    godina INT NOT NULL,
    iznos DECIMAL(10, 2) NOT NULL,
    placeno TINYINT(1) DEFAULT 0,
    datum_uplate DATE DEFAULT NULL,
    napomena TEXT,
    upisao_admin_id INT,
    datum_kreiranja DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (korisnik_id) REFERENCES korisnici(id) ON DELETE CASCADE,
    FOREIGN KEY (upisao_admin_id) REFERENCES korisnici(id) ON DELETE SET NULL,
    UNIQUE KEY jedinstvena_clanarina (korisnik_id, mjesec, godina)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- UMETNI GLAVNOG ADMINA (lozinka: admin123)
-- ============================================
INSERT INTO korisnici (korisnicko_ime, lozinka, ime, prezime, email, uloga) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Glavni', 'Admin', 'admin@blackbox.ba', 'glavni_admin');

-- ============================================
-- PRIMJER GRUPE
-- ============================================
INSERT INTO grupe (naziv, opis, kapacitet) VALUES 
('Grupa 1', 'Grupa za pocetnike u fitnesu', 12),
('Grupa 2', 'Grupa za napredne u fitnesu', 12),
('Grupa 3', 'Grupa za dizanje tegova', 12),
('Grupa 4', 'Grupa za olimpijsko dizanje tegova', 12);