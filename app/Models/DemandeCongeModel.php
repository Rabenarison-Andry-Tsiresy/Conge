<?php

namespace App\Models;

use CodeIgniter\Model;

class DemandeCongeModel extends Model
{
    protected $table = 'conge_conges';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'employe_id',
        'type_conge_id',
        'date_debut',
        'date_fin',
        'nb_jours',
        'motif',
        'statut',
        'commentaire_rh',
        'traite_par',
        'created_at',
    ];

    public function createDemande(array $data): int
    {
        $data['statut'] = 'en attente';

        return (int) $this->insert($data);
    }

    public function getDemandesByEmploye(int $employeId, ?string $statut = null, int $limit = 10): array
    {
        $builder = $this->select('conge_conges.*, conge_types_conge.libelle AS type_libelle')
            ->join('conge_types_conge', 'conge_types_conge.id = conge_conges.type_conge_id')
            ->where('conge_conges.employe_id', $employeId)
            ->orderBy('conge_conges.date_debut', 'DESC')
            ->limit($limit);

        if ($statut) {
            $builder->where('conge_conges.statut', $statut);
        }

        return $builder->findAll();
    }

    public function getStatsByEmploye(int $employeId): array
    {
        $stats = [
            'en_attente' => 0,
            'approuvee' => 0,
            'refusee' => 0,
            'annulee' => 0,
        ];

        $rows = $this->select('statut, COUNT(*) AS total')
            ->where('employe_id', $employeId)
            ->groupBy('statut')
            ->findAll();

        foreach ($rows as $row) {
            $key = str_replace(' ', '_', $row['statut']);
            if (array_key_exists($key, $stats)) {
                $stats[$key] = (int) $row['total'];
            }
        }

        return $stats;
    }

    public function getDemandesForRh(array $filters = []): array
    {
        $builder = $this->select('conge_conges.*, conge_employes.nom, conge_employes.prenom, conge_employes.departement_id, conge_departements.nom AS departement_nom, conge_types_conge.libelle AS type_libelle, conge_soldes.jours_attribues, conge_soldes.jours_pris')
            ->join('conge_employes', 'conge_employes.id = conge_conges.employe_id')
            ->join('conge_departements', 'conge_departements.id = conge_employes.departement_id', 'left')
            ->join('conge_types_conge', 'conge_types_conge.id = conge_conges.type_conge_id')
            ->join('conge_soldes', "conge_soldes.employe_id = conge_conges.employe_id AND conge_soldes.type_conge_id = conge_conges.type_conge_id AND conge_soldes.annee = " . (int) date('Y'), 'left')
            ->orderBy('conge_conges.created_at', 'DESC');

        if (!empty($filters['statut'])) {
            $builder->where('conge_conges.statut', $filters['statut']);
        }

        if (!empty($filters['departement_id'])) {
            $builder->where('conge_employes.departement_id', (int) $filters['departement_id']);
        }

        return $builder->findAll();
    }

    public function getRhStats(): array
    {
        $stats = [
            'total' => 0,
            'en_attente' => 0,
            'approuvee' => 0,
            'refusee' => 0,
        ];

        $rows = $this->select('statut, COUNT(*) AS total')
            ->groupBy('statut')
            ->findAll();

        foreach ($rows as $row) {
            $key = str_replace(' ', '_', $row['statut']);
            if ($key === 'en_attente') {
                $stats['en_attente'] = (int) $row['total'];
            } elseif ($key === 'approuvee') {
                $stats['approuvee'] = (int) $row['total'];
            } elseif ($key === 'refusee') {
                $stats['refusee'] = (int) $row['total'];
            }
            $stats['total'] += (int) $row['total'];
        }

        return $stats;
    }

    public function approveDemande(int $id, int $rhId, string $commentaire = ''): void
    {
        $this->update($id, [
            'statut' => 'approuvee',
            'commentaire_rh' => $commentaire,
            'traite_par' => $rhId,
        ]);
    }

    public function refuseDemande(int $id, int $rhId, string $commentaire = ''): void
    {
        $this->update($id, [
            'statut' => 'refusee',
            'commentaire_rh' => $commentaire,
            'traite_par' => $rhId,
        ]);
    }

    public function cancelDemande(int $id, int $employeId): void
    {
        $this->where('id', $id)
            ->where('employe_id', $employeId)
            ->where('statut', 'en attente')
            ->set(['statut' => 'annulee'])
            ->update();
    }
}
