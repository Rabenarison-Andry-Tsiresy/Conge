<?php

namespace App\Models;

use CodeIgniter\Model;

class DepartementModel extends Model
{
	protected $table = 'conge_departements';
	protected $primaryKey = 'id';
	protected $returnType = 'array';
	protected $allowedFields = ['nom', 'description'];
}
