<?php

namespace App\Models;

use CodeIgniter\Model;

class TypeCongeModel extends Model
{
    protected $table            = 'conge_types_conge';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'libelle',
        'jours_annuels',
        'deductible',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'libelle'       => 'required|min_length[2]|max_length[150]|is_unique[conge_types_conge.libelle,id,{id}]',
        'jours_annuels' => 'required|integer|greater_than[0]',
        'deductible'    => 'required|in_list[0,1]',
    ];

    protected $validationMessages = [
        'libelle' => [
            'is_unique' => 'Ce type de congé existe déjà.',
        ],
    ];

    protected $skipValidation = false;

    public function findDeductibles(): array
    {
        return $this->where('deductible', 1)
                    ->orderBy('libelle', 'ASC')
                    ->findAll();
    }

    public function findAllSorted(): array
    {
        return $this->orderBy('libelle', 'ASC')->findAll();
    }
}