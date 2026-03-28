CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100) NOT NULL,
  slug VARCHAR(100) UNIQUE NOT NULL,
  description TEXT
);

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(150) UNIQUE NOT NULL,
  mot_de_passe VARCHAR(255) NOT NULL,
  role ENUM('admin','editeur') DEFAULT 'editeur',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE articles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titre VARCHAR(255) NOT NULL,
  slug VARCHAR(255) UNIQUE NOT NULL,
  contenu LONGTEXT,
  resume TEXT,
  image VARCHAR(255),
  alt_image VARCHAR(255),
  date_publication DATETIME DEFAULT CURRENT_TIMESTAMP,
  statut ENUM('publie','brouillon') DEFAULT 'brouillon',

  categorie_id INT,
  user_id INT,

  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  FOREIGN KEY (categorie_id) REFERENCES categories(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
);


