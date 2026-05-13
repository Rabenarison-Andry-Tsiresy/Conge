<?php

namespace App\Controllers;

use App\Models\DashboardModel;
use App\Models\DemandeCongeModel;
use App\Models\DepartementModel;
use App\Models\SoldeCongeModel;
use App\Models\TypeCongeModel;
use App\Models\UserModel;

class AdminController extends BaseController
{
    public function dashboard()
    {
        if ($redirect = $this->ensureAuthenticated(['admin'])) {
            return $redirect;
        }

        $dashboard = new DashboardModel();
        $stats = $dashboard->getAdminStats();
        $recentDemandes = $dashboard->getRecentDemandes(5);
        $absents = $dashboard->getAbsentsToday(5);

        return view('admin/dashboard', [
            'currentUser' => $this->currentUser(),
            'stats' => $stats,
            'recentDemandes' => $recentDemandes,
            'absents' => $absents,
        ]);
    }

    public function employes()
    {
        if ($redirect = $this->ensureAuthenticated(['admin'])) {
            return $redirect;
        }

        $users = new UserModel();
        $departements = new DepartementModel();

        return view('admin/employes', [
            'currentUser' => $this->currentUser(),
            'employes' => $users->getAllWithDepartement(),
            'departements' => $departements->findAll(),
            'success' => $this->session->getFlashdata('success'),
            'errors' => $this->session->getFlashdata('errors'),
        ]);
    }

    public function storeEmploye()
    {
        if ($redirect = $this->ensureAuthenticated(['admin'])) {
            return $redirect;
        }

        $data = [
            'prenom' => trim((string) $this->request->getPost('prenom')),
            'nom' => trim((string) $this->request->getPost('nom')),
            'email' => trim((string) $this->request->getPost('email')),
            'password' => (string) $this->request->getPost('password'),
            'departement_id' => (int) $this->request->getPost('departement_id'),
            'role' => trim((string) $this->request->getPost('role')),
            'date_embauche' => (string) $this->request->getPost('date_embauche'),
        ];

        $errors = [];
        if ($data['prenom'] === '') {
            $errors['prenom'] = 'Le prenom est requis.';
        }
        if ($data['nom'] === '') {
            $errors['nom'] = 'Le nom est requis.';
        }
        if ($data['email'] === '') {
            $errors['email'] = 'L\'email est requis.';
        }
        if ($data['password'] === '') {
            $errors['password'] = 'Le mot de passe est requis.';
        }

        if ($errors) {
            $this->session->setFlashdata('errors', $errors);
            return redirect()->back()->withInput();
        }

        $users = new UserModel();
        $userId = $users->createUser($data);

        $types = new TypeCongeModel();
        $soldes = new SoldeCongeModel();
        $annee = (int) date('Y');

        foreach ($types->findAll() as $type) {
            $soldes->insert([
                'employe_id' => $userId,
                'type_conge_id' => (int) $type['id'],
                'annee' => $annee,
                'jours_attribues' => (float) $type['jours_annuels'],
                'jours_pris' => 0,
            ]);
        }
        $this->session->setFlashdata('success', 'Employe cree avec succes.');

        return redirect()->to('/admin/employes');
    }

    public function demandes()
    {
        if ($redirect = $this->ensureAuthenticated(['admin'])) {
            return $redirect;
        }

        $filters = [
            'statut' => $this->request->getGet('statut'),
            'departement_id' => $this->request->getGet('departement_id'),
        ];

        $demandes = new DemandeCongeModel();
        $departements = new DepartementModel();

        return view('admin/demandes', [
            'currentUser' => $this->currentUser(),
            'demandes' => $demandes->getDemandesForRh($filters),
            'departements' => $departements->findAll(),
            'filters' => $filters,
            'stats' => $demandes->getRhStats(),
            'success' => $this->session->getFlashdata('success'),
        ]);
    }

    public function departements()
    {
        if ($redirect = $this->ensureAuthenticated(['admin'])) {
            return $redirect;
        }

        $departements = new DepartementModel();

        return view('admin/departements', [
            'currentUser' => $this->currentUser(),
            'departements' => $departements->orderBy('nom', 'ASC')->findAll(),
            'success' => $this->session->getFlashdata('success'),
            'errors' => $this->session->getFlashdata('errors'),
        ]);
    }

    public function typesConge()
    {
        if ($redirect = $this->ensureAuthenticated(['admin'])) {
            return $redirect;
        }

        $types = new TypeCongeModel();

        return view('admin/types_conge', [
            'currentUser' => $this->currentUser(),
            'types' => $types->orderBy('libelle', 'ASC')->findAll(),
            'success' => $this->session->getFlashdata('success'),
            'errors' => $this->session->getFlashdata('errors'),
        ]);
    }

    public function soldes()
    {
        if ($redirect = $this->ensureAuthenticated(['admin'])) {
            return $redirect;
        }

        $soldes = new SoldeCongeModel();

        return view('admin/soldes', [
            'currentUser' => $this->currentUser(),
            'soldes' => $soldes->getSoldesAll((int) date('Y')),
        ]);
    }

    public function storeDepartement()
    {
        if ($redirect = $this->ensureAuthenticated(['admin'])) {
            return $redirect;
        }

        $data = [
            'nom' => trim((string) $this->request->getPost('nom')),
            'description' => trim((string) $this->request->getPost('description')),
        ];

        $errors = [];
        if ($data['nom'] === '') {
            $errors['nom'] = 'Le nom du departement est requis.';
        }

        if ($errors) {
            $this->session->setFlashdata('errors', $errors);
            return redirect()->back()->withInput();
        }

        $departements = new DepartementModel();
        $departements->insert($data);
        $this->session->setFlashdata('success', 'Departement cree avec succes.');

        return redirect()->to('/admin/departements');
    }

    public function storeTypeConge()
    {
        if ($redirect = $this->ensureAuthenticated(['admin'])) {
            return $redirect;
        }

        $data = [
            'libelle' => trim((string) $this->request->getPost('libelle')),
            'jours_annuels' => $this->request->getPost('jours_annuels'),
            'deductible' => $this->request->getPost('deductible') ? 1 : 0,
        ];

        $errors = [];
        if ($data['libelle'] === '') {
            $errors['libelle'] = 'Le libelle est requis.';
        }

        if ($data['jours_annuels'] === '' || $data['jours_annuels'] === null) {
            $errors['jours_annuels'] = 'Le nombre de jours est requis.';
        } elseif (!is_numeric($data['jours_annuels']) || (float) $data['jours_annuels'] < 0) {
            $errors['jours_annuels'] = 'Le nombre de jours doit etre un nombre positif.';
        }

        if ($errors) {
            $this->session->setFlashdata('errors', $errors);
            return redirect()->back()->withInput();
        }

        $types = new TypeCongeModel();
        $types->insert([
            'libelle' => $data['libelle'],
            'jours_annuels' => (float) $data['jours_annuels'],
            'deductible' => (int) $data['deductible'],
        ]);
        $this->session->setFlashdata('success', 'Type de conge cree avec succes.');

        return redirect()->to('/admin/types-conge');
    }
}
