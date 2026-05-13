<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
	protected $table = 'conge_employes';
	protected $primaryKey = 'id';
	protected $returnType = 'array';
	protected $allowedFields = [
		'nom',
		'prenom',
		'email',
		'password',
		'role',
		'departement_id',
		'date_embauche',
		'actif',
	];

	public function findByEmail(string $email): ?array
	{
		return $this->where('email', $email)->first();
	}

	public function verifyPassword(string $stored, string $plain): bool
	{
		if (strpos($stored, '$2y$') === 0 || strpos($stored, '$argon2') === 0) {
			return password_verify($plain, $stored);
		}

		return hash_equals($stored, $plain);
	}

	public function createUser(array $data): int
	{
		$data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
		$data['actif'] = $data['actif'] ?? 1;

		return (int) $this->insert($data);
	}

	public function getAllWithDepartement(): array
	{
		return $this->select('conge_employes.*, conge_departements.nom AS departement_nom')
			->join('conge_departements', 'conge_departements.id = conge_employes.departement_id', 'left')
			->orderBy('conge_employes.nom', 'ASC')
			->findAll();
	}
}
