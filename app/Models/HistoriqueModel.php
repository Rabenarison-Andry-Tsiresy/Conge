<?php

namespace App\Models;

use CodeIgniter\Model;

class HistoriqueModel extends Model
{
    protected $table = 'conge_historiques';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['employe_id', 'action', 'details', 'created_at'];
}
