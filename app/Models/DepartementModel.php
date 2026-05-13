<?php

namespace App\Models;

use CodeIgniter\Model;

class DepartementModel extends Model
{
    protected $table            = 'conge_departements';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'nom',
        'description',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'nom'         => 'required|min_length[2]|max_length[150]|is_unique[conge_departements.nom,id,{id}]',
        'description' => 'permit_empty|max_length[500]',
    ];

    protected $validationMessages = [
        'nom' => [
            'is_unique' => 'Un département avec ce nom existe déjà.',
        ],
    ];

    protected $skipValidation = false;

    public function findAllSorted(): array
    {
        return $this->orderBy('nom', 'ASC')->findAll();
    }

    public function findWithEmployes(int $id): ?array
    {
        $departement = $this->find($id);

        if ($departement === null) {
            return null;
        }

        $employeModel = new EmployeModel();
        $departement['employes'] = $employeModel->findByDepartement($id);

        return $departement;
    }
}