<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'conge_notifications';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['employe_id', 'message', 'lu', 'created_at'];
}
