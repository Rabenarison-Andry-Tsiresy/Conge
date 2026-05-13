-- Création de la base de données SQLite3

PRAGMA foreign_keys = ON;

-- =========================
-- TABLE : departements
-- =========================
CREATE TABLE conge_departements (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT NOT NULL,
    description TEXT
);

-- =========================
-- TABLE : types_conge
-- =========================
CREATE TABLE conge_types_conge (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    libelle TEXT NOT NULL,
    jours_annuels INTEGER NOT NULL,
    deductible INTEGER NOT NULL CHECK (deductible IN (0,1))
);

-- =========================
-- TABLE : employes
-- =========================
CREATE TABLE conge_employes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT NOT NULL,
    prenom TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    role TEXT NOT NULL,
    departement_id INTEGER,
    date_embauche DATE,
    actif INTEGER NOT NULL DEFAULT 1 CHECK (actif IN (0,1)),
    FOREIGN KEY (departement_id)
        REFERENCES departements(id)
);

-- =========================
-- TABLE : soldes
-- =========================
CREATE TABLE conge_soldes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    employe_id INTEGER NOT NULL,
    type_conge_id INTEGER NOT NULL,
    annee INTEGER NOT NULL,
    jours_attribues REAL NOT NULL,
    jours_pris REAL NOT NULL DEFAULT 0,

    FOREIGN KEY (employe_id)
        REFERENCES employes(id),

    FOREIGN KEY (type_conge_id)
        REFERENCES types_conge(id)
);

-- =========================
-- TABLE : conges
-- =========================
CREATE TABLE conge_conges (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    employe_id INTEGER NOT NULL,
    type_conge_id INTEGER NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    nb_jours REAL NOT NULL,
    motif TEXT,
    statut TEXT NOT NULL,
    commentaire_rh TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    traite_par INTEGER,

    FOREIGN KEY (employe_id)
        REFERENCES employes(id),

    FOREIGN KEY (type_conge_id)
        REFERENCES types_conge(id),

    FOREIGN KEY (traite_par)
        REFERENCES employes(id)
);

-- =========================
-- VUE : reste_conges
-- =========================
CREATE VIEW reste_conges AS
SELECT
    s.id,
    e.nom || ' ' || e.prenom AS employe,
    tc.libelle AS type_conge,
    s.annee,
    s.jours_attribues,
    s.jours_pris,
    (s.jours_attribues - s.jours_pris) AS reste
FROM soldes s
JOIN employes e ON s.employe_id = e.id
JOIN types_conge tc ON s.type_conge_id = tc.id;