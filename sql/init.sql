SET NAMES utf8mb4;

-- ─────────────────────────────
--  CATÉGORIES
-- ─────────────────────────────
CREATE TABLE IF NOT EXISTS categories (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  nom         VARCHAR(100) NOT NULL,
  slug        VARCHAR(100) UNIQUE NOT NULL,
  description TEXT
);

-- ─────────────────────────────
--  USERS (admin, editeur, redacteur)
-- ─────────────────────────────
CREATE TABLE IF NOT EXISTS users (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  nom          VARCHAR(100) NOT NULL DEFAULT '',
  email        VARCHAR(150) UNIQUE NOT NULL,
  mot_de_passe VARCHAR(255) NOT NULL,
  role         ENUM('admin', 'editeur', 'redacteur') NOT NULL DEFAULT 'redacteur',
  actif        TINYINT(1) NOT NULL DEFAULT 1,
  created_at   DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ─────────────────────────────
--  MÉDIAS (médiathèque)
-- ─────────────────────────────
CREATE TABLE IF NOT EXISTS medias (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  filename    VARCHAR(255) NOT NULL,
  original_name VARCHAR(255) NOT NULL,
  alt_text    VARCHAR(255),
  mime_type   VARCHAR(100),
  size        INT,
  user_id     INT,
  created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ─────────────────────────────
--  ARTICLES
-- ─────────────────────────────
CREATE TABLE IF NOT EXISTS articles (
  id               INT AUTO_INCREMENT PRIMARY KEY,
  titre            VARCHAR(255) NOT NULL,
  slug             VARCHAR(255) UNIQUE NOT NULL,
  contenu          LONGTEXT,
  resume           TEXT,
  image            VARCHAR(255),
  alt_image        VARCHAR(255),
  date_publication DATETIME DEFAULT CURRENT_TIMESTAMP,
  statut           ENUM('publie','brouillon','planifie') DEFAULT 'brouillon',
  categorie_id     INT,
  user_id          INT,
  created_at       DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at       DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (categorie_id) REFERENCES categories(id) ON DELETE SET NULL,
  FOREIGN KEY (user_id)      REFERENCES users(id)      ON DELETE SET NULL
);

-- ─────────────────────────────
--  UTILISATEURS PAR DÉFAUT
--  Password : password (pour tous)
-- ─────────────────────────────
INSERT INTO users (nom, email, mot_de_passe, role) VALUES
('Administrateur', 'admin@site.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Éditeur Test', 'editeur@site.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'editeur'),
('Rédacteur Test', 'redacteur@site.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'redacteur')
ON DUPLICATE KEY UPDATE nom = VALUES(nom), role = VALUES(role);

-- ─────────────────────────────
--  CATÉGORIES DE TEST
-- ─────────────────────────────
INSERT INTO categories (nom, slug, description) VALUES
('Militaire',     'militaire',     'Opérations et offensives militaires'),
('Politique',     'politique',     'Diplomatie et décisions gouvernementales'),
('Humanitaire',   'humanitaire',   'Situation des civils et aide humanitaire'),
('International', 'international', 'Réactions de la communauté internationale');

-- ─────────────────────────────
--  ARTICLES DE TEST
-- ─────────────────────────────
INSERT INTO articles (titre, slug, resume, contenu, alt_image, categorie_id, user_id, statut) VALUES
(
  'Offensive militaire au nord de l Iran',
  'offensive-militaire-nord-iran',
  'Les forces armées ont lancé une nouvelle offensive dans le nord du pays.',
  '<h2>Contexte</h2><p>Depuis plusieurs semaines, les tensions ont atteint un seuil critique.</p><h2>Déroulement</h2><p>L offensive a débuté le 12 mars 2026 à l aube.</p>',
  'Soldats iraniens progressant dans une zone montagneuse',
  1, 1, 'publie'
),
(
  'Négociations diplomatiques à Genève',
  'negociations-diplomatiques-geneve',
  'Des pourparlers secrets auraient eu lieu à Genève entre les parties en conflit.',
  '<h2>Discussions discrètes</h2><p>Des représentants des deux camps se seraient rencontrés sous l égide de l ONU.</p>',
  'Table de négociation lors des pourparlers à Genève',
  2, 1, 'publie'
),
(
  'Crise humanitaire : 500 000 déplacés',
  'crise-humanitaire-500000-deplaces',
  'Le HCR tire la sonnette d alarme face à l afflux massif de déplacés.',
  '<h2>Un exode massif</h2><p>Plus de 500 000 personnes ont quitté leur foyer depuis le début des hostilités.</p>',
  'Familles de réfugiés installées dans un camp de fortune',
  3, 1, 'publie'
),
(
  'Article en brouillon : analyse stratégique',
  'analyse-strategique-brouillon',
  'Article en cours de rédaction.',
  '<h2>En cours</h2><p>Contenu à rédiger...</p>',
  'Carte stratégique de la région',
  1, 1, 'brouillon'
);