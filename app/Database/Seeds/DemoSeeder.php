<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $year = (int) date('Y');

        $departements = [
            ['nom' => 'Informatique', 'description' => 'Equipe IT'],
            ['nom' => 'RH', 'description' => 'Ressources humaines'],
            ['nom' => 'Finance', 'description' => 'Comptabilite'],
        ];
        $db->table('conge_departements')->insertBatch($departements);

        $types = [
            ['libelle' => 'Conge annuel', 'jours_annuels' => 30, 'deductible' => 1],
            ['libelle' => 'Maladie', 'jours_annuels' => 10, 'deductible' => 1],
            ['libelle' => 'Special', 'jours_annuels' => 5, 'deductible' => 1],
            ['libelle' => 'Sans solde', 'jours_annuels' => 0, 'deductible' => 0],
        ];
        $db->table('conge_types_conge')->insertBatch($types);

        $departementRows = $db->table('conge_departements')->get()->getResultArray();
        $departementMap = [];
        foreach ($departementRows as $row) {
            $departementMap[$row['nom']] = (int) $row['id'];
        }

        $users = [
            [
                'nom' => 'Admin',
                'prenom' => 'Systeme',
                'email' => 'admin@techmada.mg',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role' => 'admin',
                'departement_id' => $departementMap['Informatique'] ?? null,
                'date_embauche' => '2020-01-10',
                'actif' => 1,
            ],
            [
                'nom' => 'Rabe',
                'prenom' => 'Marie',
                'email' => 'rh@techmada.mg',
                'password' => password_hash('rh123', PASSWORD_DEFAULT),
                'role' => 'rh',
                'departement_id' => $departementMap['RH'] ?? null,
                'date_embauche' => '2021-05-12',
                'actif' => 1,
            ],
            [
                'nom' => 'Rakoto',
                'prenom' => 'Soa',
                'email' => 'employe@techmada.mg',
                'password' => password_hash('emp123', PASSWORD_DEFAULT),
                'role' => 'employe',
                'departement_id' => $departementMap['Informatique'] ?? null,
                'date_embauche' => '2022-03-01',
                'actif' => 1,
            ],
        ];
        $db->table('conge_employes')->insertBatch($users);

        $typeRows = $db->table('conge_types_conge')->get()->getResultArray();
        $employes = $db->table('conge_employes')->get()->getResultArray();

        foreach ($employes as $employe) {
            foreach ($typeRows as $type) {
                $db->table('conge_soldes')->insert([
                    'employe_id' => (int) $employe['id'],
                    'type_conge_id' => (int) $type['id'],
                    'annee' => $year,
                    'jours_attribues' => (float) $type['jours_annuels'],
                    'jours_pris' => 0,
                ]);
            }
        }

        $employeId = null;
        $rhId = null;
        foreach ($employes as $employe) {
            if ($employe['role'] === 'employe') {
                $employeId = (int) $employe['id'];
            }
            if ($employe['role'] === 'rh') {
                $rhId = (int) $employe['id'];
            }
        }

        $typeMap = [];
        foreach ($typeRows as $type) {
            $typeMap[$type['libelle']] = (int) $type['id'];
        }

        if ($employeId && $rhId) {
            $db->table('conge_conges')->insertBatch([
                [
                    'employe_id' => $employeId,
                    'type_conge_id' => $typeMap['Conge annuel'] ?? 1,
                    'date_debut' => $year . '-06-10',
                    'date_fin' => $year . '-06-14',
                    'nb_jours' => 5,
                    'motif' => 'Vacances',
                    'statut' => 'en attente',
                ],
                [
                    'employe_id' => $employeId,
                    'type_conge_id' => $typeMap['Maladie'] ?? 2,
                    'date_debut' => $year . '-05-02',
                    'date_fin' => $year . '-05-03',
                    'nb_jours' => 2,
                    'motif' => 'Consultation',
                    'statut' => 'approuvee',
                    'commentaire_rh' => 'Bon retablissement',
                    'traite_par' => $rhId,
                ],
                [
                    'employe_id' => $employeId,
                    'type_conge_id' => $typeMap['Special'] ?? 3,
                    'date_debut' => $year . '-04-05',
                    'date_fin' => $year . '-04-05',
                    'nb_jours' => 1,
                    'motif' => 'Famille',
                    'statut' => 'refusee',
                    'commentaire_rh' => 'Solde insuffisant',
                    'traite_par' => $rhId,
                ],
            ]);
        }
    }
}
