<?php

namespace App\Models;

class RoleModel
{
	public function all(): array
	{
		return [
			'admin' => 'Administrateur',
			'rh' => 'Responsable RH',
			'employe' => 'Employe',
		];
	}
}
