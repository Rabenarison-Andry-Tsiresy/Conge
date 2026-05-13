<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CongeModel;
use App\Models\SoldeModel;
use App\Models\TypeCongeModel;
use App\Models\EmployeModel;

class EmployeController extends BaseController
{
    
    public function login()
    {
        if ($this->request->getMethod() === 'POST') {
            $email    = $this->request->getPost('email');
            $password = $this->request->getPost('password');

            $employe = (new EmployeModel())->findByEmail($email);

            if ($employe && password_verify($password, $employe['password'])) {
                session()->set([
                    'employe_id' => $employe['id'],
                    'role'       => $employe['role'],
                    'nom'        => $employe['nom'],
                    'prenom'     => $employe['prenom'],
                ]);

                return redirect()->to('/employe/dashboard');
            }

            return redirect()->back()->with('error', 'Email ou mot de passe incorrect.');
        }

        return view('auth/login');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    public function consulterDemandes()
    {
        $employeId = session('employe_id');

        $data['conges'] = (new CongeModel())->findByEmploye($employeId);
        $data['soldes'] = (new SoldeModel())->findByEmploye($employeId, date('Y'));

        return view('employe/demandes', $data);
    }

    public function soumettreDemande()
    {
        $data['types_conge'] = (new TypeCongeModel())->findAllSorted();

        if ($this->request->getMethod() !== 'POST') {
            return view('employe/soumettre', $data);
        }

        $employeId    = session('employe_id');
        $typeCongeId  = $this->request->getPost('type_conge_id');
        $dateDebut    = $this->request->getPost('date_debut');
        $dateFin      = $this->request->getPost('date_fin');
        $motif        = $this->request->getPost('motif');

        if ($dateDebut >= $dateFin) {
            return redirect()->back()->with('error', 'La date de début doit être antérieure à la date de fin.');
        }

        $nbJours = (new CongeModel())->calculerNbJours($dateDebut, $dateFin);

        if ((new CongeModel())->aChevauchement($employeId, $dateDebut, $dateFin)) {
            return redirect()->back()->with('error', 'Vous avez déjà une demande sur cette période.');
        }

        if (!(new SoldeModel())->estSuffisant($employeId, $typeCongeId, date('Y'), $nbJours)) {
            return redirect()->back()->with('error', 'Solde de congé insuffisant.');
        }

        (new CongeModel())->insert([
            'employe_id'    => $employeId,
            'type_conge_id' => $typeCongeId,
            'date_debut'    => $dateDebut,
            'date_fin'      => $dateFin,
            'nb_jours'      => $nbJours,
            'motif'         => $motif,
            'statut'        => CongeModel::STATUT_EN_ATTENTE,
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/employe/demandes')->with('success', 'Demande soumise avec succès.');
    }

    public function cancelDemande(int $congeId)
    {
        $employe = (new CongeModel())->find($congeId);

        if (!$employe || $employe['employe_id'] !== session('employe_id')) {
            return redirect()->to('/employe/demandes')->with('error', 'Demande introuvable.');
        }

        if ($employe['statut'] !== CongeModel::STATUT_EN_ATTENTE) {
            return redirect()->to('/employe/demandes')->with('error', 'Seules les demandes en attente peuvent être annulées.');
        }

        (new CongeModel())->update($congeId, ['statut' => CongeModel::STATUT_ANNULE]);

        return redirect()->to('/employe/demandes')->with('success', 'Demande annulée.');
    }

    public function getSoldeConge()
    {
        $data['soldes'] = (new SoldeModel())->findByEmploye(session('employe_id'), date('Y'));

        return view('employe/solde', $data);
    }

    
    public function editProfil()
    {
        $employeModel = new EmployeModel();
        $employe      = $employeModel->find(session('employe_id'));

        if ($this->request->getMethod() !== 'POST') {
            return view('employe/profil', ['employe' => $employe]);
        }

        $update = [
            'nom'    => $this->request->getPost('nom'),
            'prenom' => $this->request->getPost('prenom'),
        ];

        $newPassword = $this->request->getPost('password');

        if (!empty($newPassword)) {
            $update['password'] = $newPassword; // hashé via beforeUpdate
        }

        $employeModel->update(session('employe_id'), $update);

        session()->set([
            'nom'    => $update['nom'],
            'prenom' => $update['prenom'],
        ]);

        return redirect()->to('/employe/profil')->with('success', 'Profil mis à jour.');
    }
}