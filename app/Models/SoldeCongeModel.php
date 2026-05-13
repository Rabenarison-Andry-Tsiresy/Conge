<?php

namespace App\Models;

use CodeIgniter\Model;

class SoldeModel extends Model
{
    protected $table            = 'conge_solde';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'employe_id',
        'type_conge_id',
        'annee',
        'jours_attribues',
        'jours_pris',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'employe_id'     => 'required|integer',
        'type_conge_id'  => 'required|integer',
        'annee'          => 'required|integer|min_length[4]|max_length[4]',
        'jours_attribues' => 'required|integer|greater_than_equal_to[0]',
        'jours_pris'     => 'required|integer|greater_than_equal_to[0]',
    ];

    protected $skipValidation = false;

    public static function calculerRestant(array $solde): int
    {
        return (int) $solde['jours_attribues'] - (int) $solde['jours_pris'];
    }

    public function findSolde(int $employeId, int $typeCongeId, int $annee): ?array
    {
        $solde = $this->where('employe_id', $employeId)
                      ->where('type_conge_id', $typeCongeId)
                      ->where('annee', $annee)
                      ->first();

        if ($solde !== null) {
            $solde['jours_restants'] = self::calculerRestant($solde);
        }

        return $solde;
    }


    public function findByEmploye(int $employeId, int $annee): array
    {
        $soldes = $this->select('conge_solde.*, conge_types_conge.libelle AS type_libelle')
                       ->join('conge_types_conge', 'conge_types_conge.id = conge_solde.type_conge_id')
                       ->where('conge_solde.employe_id', $employeId)
                       ->where('conge_solde.annee', $annee)
                       ->findAll();

        return array_map(function ($solde) {
            $solde['jours_restants'] = self::calculerRestant($solde);
            return $solde;
        }, $soldes);
    }

    public function deduire(int $employeId, int $typeCongeId, int $annee, int $nbJours): bool
    {
        $solde = $this->findSolde($employeId, $typeCongeId, $annee);

        if ($solde === null) {
            throw new \RuntimeException("Aucun solde trouvé pour cet employé / type / année.");
        }

        if ($solde['jours_restants'] < $nbJours) {
            throw new \RuntimeException("Solde insuffisant : {$solde['jours_restants']} jour(s) disponible(s), {$nbJours} demandé(s).");
        }

        return $this->where('employe_id', $employeId)
                    ->where('type_conge_id', $typeCongeId)
                    ->where('annee', $annee)
                    ->set('jours_pris', "jours_pris + {$nbJours}", false)
                    ->update();
    }


    public function recrediter(int $employeId, int $typeCongeId, int $annee, int $nbJours): bool
    {
        return $this->where('employe_id', $employeId)
                    ->where('type_conge_id', $typeCongeId)
                    ->where('annee', $annee)
                    ->set('jours_pris', "jours_pris - {$nbJours}", false)
                    ->update();
    }


    public function estSuffisant(int $employeId, int $typeCongeId, int $annee, int $nbJours): bool
    {
        $solde = $this->findSolde($employeId, $typeCongeId, $annee);

        return $solde !== null && $solde['jours_restants'] >= $nbJours;
    }
}