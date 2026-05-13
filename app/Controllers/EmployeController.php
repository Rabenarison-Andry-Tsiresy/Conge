<?php

namespace App\Controllers;

use App\Models\DemandeCongeModel;
use App\Models\SoldeCongeModel;
use App\Models\TypeCongeModel;

class EmployeController extends BaseController
{
    public function dashboard()
    {
        if ($redirect = $this->ensureAuthenticated(['employe'])) {
            return $redirect;
        }

        $user = $this->currentUser();
        $demandes = new DemandeCongeModel();
        $soldes = new SoldeCongeModel();

        $stats = $demandes->getStatsByEmploye((int) $user['id']);
        $soldesList = $soldes->getSoldesByEmploye((int) $user['id'], (int) date('Y'));
        $recentDemandes = $demandes->getDemandesByEmploye((int) $user['id'], null, 5);

        return view('employe/dashboard', [
            'currentUser' => $user,
            'stats' => $stats,
            'soldes' => $soldesList,
            'demandes' => $recentDemandes,
            'success' => $this->session->getFlashdata('success'),
        ]);
    }

    public function index()
    {
        if ($redirect = $this->ensureAuthenticated(['employe'])) {
            return $redirect;
        }

        $user = $this->currentUser();
        $statut = $this->request->getGet('statut');

        $demandes = new DemandeCongeModel();
        $list = $demandes->getDemandesByEmploye((int) $user['id'], $statut ?: null, 50);

        return view('employe/index', [
            'currentUser' => $user,
            'demandes' => $list,
            'statut' => $statut,
        ]);
    }

    public function create()
    {
        if ($redirect = $this->ensureAuthenticated(['employe'])) {
            return $redirect;
        }

        $user = $this->currentUser();
        $types = new TypeCongeModel();
        $soldes = new SoldeCongeModel();

        $typesList = $types->findAll();
        $soldesList = $soldes->getSoldesByEmploye((int) $user['id'], (int) date('Y'));

        return view('employe/create', [
            'currentUser' => $user,
            'types' => $typesList,
            'soldes' => $soldesList,
            'errors' => $this->session->getFlashdata('errors'),
            'form' => [
                'type_conge_id' => old('type_conge_id'),
                'date_debut' => old('date_debut'),
                'date_fin' => old('date_fin'),
                'motif' => old('motif'),
                'computed_days' => $this->session->getFlashdata('computed_days'),
                'computed_range' => $this->session->getFlashdata('computed_range'),
            ],
        ]);
    }

    public function store()
    {
        if ($redirect = $this->ensureAuthenticated(['employe'])) {
            return $redirect;
        }

        $user = $this->currentUser();
        $typeId = (int) $this->request->getPost('type_conge_id');
        $dateDebut = (string) $this->request->getPost('date_debut');
        $dateFin = (string) $this->request->getPost('date_fin');
        $motif = trim((string) $this->request->getPost('motif'));

        $errors = [];
        if (!$typeId) {
            $errors['type_conge_id'] = 'Le type de conge est requis.';
        }
        if ($dateDebut === '') {
            $errors['date_debut'] = 'La date de debut est requise.';
        }
        if ($dateFin === '') {
            $errors['date_fin'] = 'La date de fin est requise.';
        }

        $days = $this->calculateDays($dateDebut, $dateFin);
        if ($days === null) {
            $errors['date_fin'] = 'La date de fin doit etre apres la date de debut.';
        }

        if ($errors) {
            $this->session->setFlashdata('errors', $errors);
            return redirect()->back()->withInput();
        }

        $types = new TypeCongeModel();
        $type = $types->find($typeId);
        if (!$type) {
            $this->session->setFlashdata('errors', ['type_conge_id' => 'Type de conge invalide.']);
            return redirect()->back()->withInput();
        }

        if ((int) $type['deductible'] === 1) {
            $soldes = new SoldeCongeModel();
            $solde = $soldes->getSoldeForType((int) $user['id'], $typeId, (int) date('Y'));

            if (!$solde || ((float) $solde['jours_attribues'] - (float) $solde['jours_pris']) < $days) {
                $this->session->setFlashdata('errors', ['type_conge_id' => 'Solde insuffisant pour ce type de conge.']);
                $this->session->setFlashdata('computed_days', $days);
                $this->session->setFlashdata('computed_range', $this->formatRange($dateDebut, $dateFin));
                return redirect()->back()->withInput();
            }
        }

        $demandes = new DemandeCongeModel();
        $demandes->createDemande([
            'employe_id' => (int) $user['id'],
            'type_conge_id' => $typeId,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'nb_jours' => $days,
            'motif' => $motif,
        ]);

        $this->session->setFlashdata('success', 'Votre demande a ete soumise et est en attente de validation.');
        return redirect()->to('/employe');
    }

    public function cancel(int $id)
    {
        if ($redirect = $this->ensureAuthenticated(['employe'])) {
            return $redirect;
        }

        $user = $this->currentUser();
        $demandes = new DemandeCongeModel();
        $demandes->cancelDemande($id, (int) $user['id']);

        return redirect()->back();
    }

    private function calculateDays(string $dateDebut, string $dateFin): ?int
    {
        try {
            $start = new \DateTime($dateDebut);
            $end = new \DateTime($dateFin);
        } catch (\Throwable $e) {
            return null;
        }

        if ($end < $start) {
            return null;
        }

        return (int) $start->diff($end)->days + 1;
    }

    private function formatRange(string $dateDebut, string $dateFin): string
    {
        try {
            $start = new \DateTime($dateDebut);
            $end = new \DateTime($dateFin);
        } catch (\Throwable $e) {
            return '';
        }

        return $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y');
    }
}
