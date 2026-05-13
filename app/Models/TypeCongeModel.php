<?php

namespace App\Models;

use CodeIgniter\Model;

class TypeCongeModel extends Model
{
	protected $table = 'conge_types_conge';
	protected $primaryKey = 'id';
	protected $returnType = 'array';
	protected $allowedFields = ['libelle', 'jours_annuels', 'deductible'];
}
