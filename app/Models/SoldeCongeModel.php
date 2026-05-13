<?php

namespace App\Models;

use CodeIgniter\Model;

class SoldeCongeModel extends Model
{
	protected $table = 'conge_soldes';
	protected $primaryKey = 'id';
	protected $returnType = 'array';
	protected $allowedFields = [
		'employe_id',
		'type_conge_id',
		'annee',
		'jours_attribues',
		'jours_pris',
	];

	public function getSoldesByEmploye(int $employeId, int $annee): array
	{
		return $this->select('conge_soldes.*, conge_types_conge.libelle, conge_types_conge.jours_annuels')
			->join('conge_types_conge', 'conge_types_conge.id = conge_soldes.type_conge_id')
			->where('conge_soldes.employe_id', $employeId)
			->where('conge_soldes.annee', $annee)
			->orderBy('conge_types_conge.libelle', 'ASC')
			->findAll();
	}

	public function getSoldeForType(int $employeId, int $typeId, int $annee): ?array
	{
		return $this->where('employe_id', $employeId)
			->where('type_conge_id', $typeId)
			->where('annee', $annee)
			->first();
	}

	public function deductSolde(int $employeId, int $typeId, float $jours, int $annee): void
	{
		$solde = $this->getSoldeForType($employeId, $typeId, $annee);
		if (!$solde) {
			return;
		}

		$this->update($solde['id'], [
			'jours_pris' => (float) $solde['jours_pris'] + $jours,
		]);
	}

	public function getSoldesResume(int $annee): array
	{
		return $this->select('conge_employes.id AS employe_id, conge_employes.nom, conge_employes.prenom, conge_departements.nom AS departement_nom, SUM(conge_soldes.jours_attribues - conge_soldes.jours_pris) AS jours_restants')
			->join('conge_employes', 'conge_employes.id = conge_soldes.employe_id')
			->join('conge_departements', 'conge_departements.id = conge_employes.departement_id', 'left')
			->where('conge_soldes.annee', $annee)
			->groupBy('conge_employes.id, conge_employes.nom, conge_employes.prenom, conge_departements.nom')
			->orderBy('conge_employes.nom', 'ASC')
			->findAll();
	}

	public function getSoldesAll(int $annee): array
	{
		return $this->select('conge_soldes.*, conge_employes.nom, conge_employes.prenom, conge_departements.nom AS departement_nom, conge_types_conge.libelle AS type_libelle')
			->join('conge_employes', 'conge_employes.id = conge_soldes.employe_id')
			->join('conge_departements', 'conge_departements.id = conge_employes.departement_id', 'left')
			->join('conge_types_conge', 'conge_types_conge.id = conge_soldes.type_conge_id')
			->where('conge_soldes.annee', $annee)
			->orderBy('conge_employes.nom', 'ASC')
			->orderBy('conge_types_conge.libelle', 'ASC')
			->findAll();
	}
}
