<?php

namespace App\Models;

use CodeIgniter\Model;

class CongeModel extends Model
{
    protected $table            = 'conge_conges';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'employe_id',
        'type_conge_id',
        'date_debut',
        'date_fin',
        'nb_jours',
        'motif',
        'statut',
        'commentaire_rh',
        'created_at',
        'traite_par',
    ];

    const STATUT_EN_ATTENTE = 'en_attente';
    const STATUT_APPROUVE   = 'approuve';
    const STATUT_REFUSE     = 'refuse';
    const STATUT_ANNULE     = 'annule';

    protected $useTimestamps  = false;
    protected $dateFormat     = 'datetime';

    protected $validationRules = [
        'employe_id'     => 'required|integer',
        'type_conge_id'  => 'required|integer',
        'date_debut'     => 'required|valid_date[Y-m-d]',
        'date_fin'       => 'required|valid_date[Y-m-d]',
        'nb_jours'       => 'required|integer|greater_than[0]',
        'motif'          => 'permit_empty|max_length[500]',
        'statut'         => 'required|in_list[en_attente,approuve,refuse,annule]',
        'commentaire_rh' => 'permit_empty|max_length[500]',
        'traite_par'     => 'permit_empty|integer',
    ];

    protected $skipValidation = false;

    protected $beforeInsert = ['setDefaultStatut', 'setCreatedAt'];

    public function findByEmploye(int $employeId): array
    {
        return $this->select('conge_conges.*, conge_types_conge.libelle AS type_libelle')
                    ->join('conge_types_conge', 'conge_types_conge.id = conge_conges.type_conge_id')
                    ->where('conge_conges.employe_id', $employeId)
                    ->orderBy('conge_conges.created_at', 'DESC')
                    ->findAll();
    }

    public function findEnAttente(): array
    {
        return $this->select('conge_conges.*, conge_types_conge.libelle AS type_libelle,
                              conge_employes.nom, conge_employes.prenom,
                              conge_departements.nom AS departement_nom')
                    ->join('conge_types_conge', 'conge_types_conge.id = conge_conges.type_conge_id')
                    ->join('conge_employes', 'conge_employes.id = conge_conges.employe_id')
                    ->join('conge_departements', 'conge_departements.id = conge_employes.departement_id', 'left')
                    ->where('conge_conges.statut', self::STATUT_EN_ATTENTE)
                    ->orderBy('conge_conges.created_at', 'ASC')
                    ->findAll();
    }

    public function findFiltrees(?string $statut = null, ?int $departementId = null): array
    {
        $builder = $this->select('conge_conges.*, conge_types_conge.libelle AS type_libelle,
                                  conge_employes.nom, conge_employes.prenom,
                                  conge_departements.nom AS departement_nom')
                        ->join('conge_types_conge', 'conge_types_conge.id = conge_conges.type_conge_id')
                        ->join('conge_employes', 'conge_employes.id = conge_conges.employe_id')
                        ->join('conge_departements', 'conge_departements.id = conge_employes.departement_id', 'left');

        if ($statut !== null) {
            $builder->where('conge_conges.statut', $statut);
        }

        if ($departementId !== null) {
            $builder->where('conge_employes.departement_id', $departementId);
        }

        return $builder->orderBy('conge_conges.created_at', 'DESC')->findAll();
    }


    public function findAbsencesMoisCourant(): array
    {
        $debut = date('Y-m-01');
        $fin   = date('Y-m-t');

        return $this->select('conge_conges.*, conge_types_conge.libelle AS type_libelle,
                              conge_employes.nom, conge_employes.prenom,
                              conge_departements.nom AS departement_nom')
                    ->join('conge_types_conge', 'conge_types_conge.id = conge_conges.type_conge_id')
                    ->join('conge_employes', 'conge_employes.id = conge_conges.employe_id')
                    ->join('conge_departements', 'conge_departements.id = conge_employes.departement_id', 'left')
                    ->where('conge_conges.statut', self::STATUT_APPROUVE)
                    ->where('conge_conges.date_debut <=', $fin)
                    ->where('conge_conges.date_fin >=', $debut)
                    ->orderBy('conge_conges.date_debut', 'ASC')
                    ->findAll();
    }


    public function aChevauchement(int $employeId, string $dateDebut, string $dateFin, ?int $excludeId = null): bool
    {
        $builder = $this->where('employe_id', $employeId)
                        ->groupStart()
                            ->whereIn('statut', [self::STATUT_EN_ATTENTE, self::STATUT_APPROUVE])
                        ->groupEnd()
                        ->where('date_debut <=', $dateFin)
                        ->where('date_fin >=', $dateDebut);

        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }

    // Only RH can
    public function approuver(int $congeId, int $rhId, ?string $commentaire = null): bool
    {
        return $this->update($congeId, [
            'statut'         => self::STATUT_APPROUVE,
            'traite_par'     => $rhId,
            'commentaire_rh' => $commentaire,
        ]);
    }

    // Only RH can
    public function refuser(int $congeId, int $rhId, ?string $commentaire = null): bool
    {
        return $this->update($congeId, [
            'statut'         => self::STATUT_REFUSE,
            'traite_par'     => $rhId,
            'commentaire_rh' => $commentaire,
        ]);
    }

    // Only RH can
    public function annuler(int $congeId): bool
    {
        return $this->where('id', $congeId)
                    ->where('statut', self::STATUT_EN_ATTENTE)
                    ->update($congeId, ['statut' => self::STATUT_ANNULE]);
    }
}