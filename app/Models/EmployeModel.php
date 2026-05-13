<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeModel extends Model
{
    protected $table            = 'conge_employes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

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

    protected $useTimestamps = false;

    protected $validationRules = [
        'nom'            => 'required|min_length[2]|max_length[100]',
        'prenom'         => 'required|min_length[2]|max_length[100]',
        'email'          => 'required|valid_email|is_unique[conge_employes.email,id,{id}]',
        'password'       => 'required|min_length[8]',
        'role'           => 'required|in_list[employe,rh,admin]',
        'departement_id' => 'permit_empty|integer',
        'date_embauche'  => 'required|valid_date',
        'actif'          => 'required|in_list[0,1]',
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'Cette adresse email est déjà utilisée.',
        ],
    ];

    protected $skipValidation = false;

    // -------------------------------------------------------------------------
    // Callbacks
    // -------------------------------------------------------------------------
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data): array
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }

        return $data;
    }

    /** Retourne tous les employés actifs, avec le nom du département. */
    public function findActifs(): array
    {
        return $this->select('conge_employes.*, conge_departements.nom AS departement_nom')
                    ->join('conge_departements', 'conge_departements.id = conge_employes.departement_id', 'left')
                    ->where('conge_employes.actif', 1)
                    ->orderBy('conge_employes.nom', 'ASC')
                    ->findAll();
    }

    /** Retourne un employé par email (pour l'authentification). */
    public function findByEmail(string $email): ?array
    {
        return $this->where('email', $email)
                    ->where('actif', 1)
                    ->first();
    }

    /** Retourne tous les employés d'un département donné. */
    public function findByDepartement(int $departementId): array
    {
        return $this->where('departement_id', $departementId)
                    ->where('actif', 1)
                    ->findAll();
    }
}