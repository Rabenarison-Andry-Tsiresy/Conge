<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EmployeModel;
use App\Models\DepartementModel;
use App\Models\TypeCongeModel;
use App\Models\SoldeModel;
use App\Models\CongeModel;

class AdminController extends BaseController
{
    // =========================================================================
    // DASHBOARD
    // =========================================================================

    public function dashboard()
    {
        $data['absences_mois'] = (new CongeModel())->findAbsencesMoisCourant();
        $data['nb_employes']   = (new EmployeModel())->where('actif', 1)->countAllResults();
        $data['nb_en_attente'] = (new CongeModel())->where('statut', CongeModel::STATUT_EN_ATTENTE)->countAllResults();

        return view('admin/dashboard', $data);
    }

    // =========================================================================
    // CRUD EMPLOYÉ
    // =========================================================================

    public function employes()
    {
        $data['employes']     = (new EmployeModel())->findActifs();
        $data['departements'] = (new DepartementModel())->findAllSorted();

        return view('admin/employes/index', $data);
    }

    public function createEmploye()
    {
        $data['departements'] = (new DepartementModel())->findAllSorted();

        if ($this->request->getMethod() !== 'POST') {
            return view('admin/employes/form', $data);
        }

        $model = new EmployeModel();

        $model->insert([
            'nom'            => $this->request->getPost('nom'),
            'prenom'         => $this->request->getPost('prenom'),
            'email'          => $this->request->getPost('email'),
            'password'       => $this->request->getPost('password'), // hashé via beforeInsert
            'role'           => $this->request->getPost('role'),
            'departement_id' => $this->request->getPost('departement_id'),
            'date_embauche'  => $this->request->getPost('date_embauche'),
            'actif'          => 1,
        ]);

        if ($model->errors()) {
            return redirect()->back()->with('error', implode(' ', $model->errors()));
        }

        return redirect()->to('/admin/employes')->with('success', 'Employé créé avec succès.');
    }

    public function updateEmploye(int $id)
    {
        $model   = new EmployeModel();
        $employe = $model->find($id);

        if (!$employe) {
            return redirect()->to('/admin/employes')->with('error', 'Employé introuvable.');
        }

        $data['employe']      = $employe;
        $data['departements'] = (new DepartementModel())->findAllSorted();

        if ($this->request->getMethod() !== 'POST') {
            return view('admin/employes/form', $data);
        }

        $update = [
            'nom'            => $this->request->getPost('nom'),
            'prenom'         => $this->request->getPost('prenom'),
            'email'          => $this->request->getPost('email'),
            'role'           => $this->request->getPost('role'),
            'departement_id' => $this->request->getPost('departement_id'),
            'date_embauche'  => $this->request->getPost('date_embauche'),
        ];

        $newPassword = $this->request->getPost('password');
        if (!empty($newPassword)) {
            $update['password'] = $newPassword; // hashé via beforeUpdate
        }

        $model->update($id, $update);

        if ($model->errors()) {
            return redirect()->back()->with('error', implode(' ', $model->errors()));
        }

        return redirect()->to('/admin/employes')->with('success', 'Employé mis à jour.');
    }

    public function deactivateEmploye(int $id)
    {
        $model = new EmployeModel();

        if (!$model->find($id)) {
            return redirect()->to('/admin/employes')->with('error', 'Employé introuvable.');
        }

        $model->update($id, ['actif' => 0]);

        return redirect()->to('/admin/employes')->with('success', 'Employé désactivé.');
    }

    public function deleteEmploye(int $id)
    {
        $model = new EmployeModel();

        if (!$model->find($id)) {
            return redirect()->to('/admin/employes')->with('error', 'Employé introuvable.');
        }

        $model->delete($id);

        return redirect()->to('/admin/employes')->with('success', 'Employé supprimé.');
    }

    // =========================================================================
    // CRUD DÉPARTEMENT
    // =========================================================================

    public function departements()
    {
        $data['departements'] = (new DepartementModel())->findAllSorted();

        return view('admin/departements/index', $data);
    }

    public function createDepartement()
    {
        if ($this->request->getMethod() !== 'POST') {
            return view('admin/departements/form');
        }

        $model = new DepartementModel();

        $model->insert([
            'nom'         => $this->request->getPost('nom'),
            'description' => $this->request->getPost('description'),
        ]);

        if ($model->errors()) {
            return redirect()->back()->with('error', implode(' ', $model->errors()));
        }

        return redirect()->to('/admin/departements')->with('success', 'Département créé.');
    }

    public function updateDepartement(int $id)
    {
        $model       = new DepartementModel();
        $departement = $model->find($id);

        if (!$departement) {
            return redirect()->to('/admin/departements')->with('error', 'Département introuvable.');
        }

        if ($this->request->getMethod() !== 'POST') {
            return view('admin/departements/form', ['departement' => $departement]);
        }

        $model->update($id, [
            'nom'         => $this->request->getPost('nom'),
            'description' => $this->request->getPost('description'),
        ]);

        if ($model->errors()) {
            return redirect()->back()->with('error', implode(' ', $model->errors()));
        }

        return redirect()->to('/admin/departements')->with('success', 'Département mis à jour.');
    }

    public function deleteDepartement(int $id)
    {
        $model = new DepartementModel();

        if (!$model->find($id)) {
            return redirect()->to('/admin/departements')->with('error', 'Département introuvable.');
        }

        $model->delete($id);

        return redirect()->to('/admin/departements')->with('success', 'Département supprimé.');
    }

    // =========================================================================
    // CRUD TYPE DE CONGÉ
    // =========================================================================

    public function typesConge()
    {
        $data['types'] = (new TypeCongeModel())->findAllSorted();

        return view('admin/types_conge/index', $data);
    }

    public function createTypeConge()
    {
        if ($this->request->getMethod() !== 'POST') {
            return view('admin/types_conge/form');
        }

        $model = new TypeCongeModel();

        $model->insert([
            'libelle'       => $this->request->getPost('libelle'),
            'jours_annuels' => $this->request->getPost('jours_annuels'),
            'deductible'    => $this->request->getPost('deductible') ? 1 : 0,
        ]);

        if ($model->errors()) {
            return redirect()->back()->with('error', implode(' ', $model->errors()));
        }

        return redirect()->to('/admin/types-conge')->with('success', 'Type de congé créé.');
    }

    public function updateTypeConge(int $id)
    {
        $model = new TypeCongeModel();
        $type  = $model->find($id);

        if (!$type) {
            return redirect()->to('/admin/types-conge')->with('error', 'Type de congé introuvable.');
        }

        if ($this->request->getMethod() !== 'POST') {
            return view('admin/types_conge/form', ['type' => $type]);
        }

        $model->update($id, [
            'libelle'       => $this->request->getPost('libelle'),
            'jours_annuels' => $this->request->getPost('jours_annuels'),
            'deductible'    => $this->request->getPost('deductible') ? 1 : 0,
        ]);

        if ($model->errors()) {
            return redirect()->back()->with('error', implode(' ', $model->errors()));
        }

        return redirect()->to('/admin/types-conge')->with('success', 'Type de congé mis à jour.');
    }

    public function deleteTypeConge(int $id)
    {
        $model = new TypeCongeModel();

        if (!$model->find($id)) {
            return redirect()->to('/admin/types-conge')->with('error', 'Type de congé introuvable.');
        }

        $model->delete($id);

        return redirect()->to('/admin/types-conge')->with('success', 'Type de congé supprimé.');
    }
}