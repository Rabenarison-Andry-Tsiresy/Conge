<?php

namespace App\Controllers;

use App\Models\DemandeCongeModel;
use App\Models\DepartementModel;
use App\Models\SoldeCongeModel;
use App\Models\TypeCongeModel;

class RhController extends BaseController
{
    public function index()
    {
        if ($redirect = $this->ensureAuthenticated(['rh', 'admin'])) {
            return $redirect;
        }

        $statut = $this->normalizeStatut($this->request->getGet('statut'));
        $filters = [
            'statut' => $statut,
            'departement_id' => $this->request->getGet('departement_id'),
        ];

        $demandes = new DemandeCongeModel();
        $departements = new DepartementModel();

        $list = $demandes->getDemandesForRh($filters);
        $stats = $demandes->getRhStats();

        return view('rh/index', [
            'currentUser' => $this->currentUser(),
            'demandes' => $list,
            'departements' => $departements->findAll(),
            'filters' => $filters,
            'stats' => $stats,
            'success' => $this->session->getFlashdata('success'),
        ]);
    }

    public function soldes()
    {
        if ($redirect = $this->ensureAuthenticated(['rh', 'admin'])) {
            return $redirect;
        }

        $soldes = new SoldeCongeModel();

        return view('rh/soldes', [
            'currentUser' => $this->currentUser(),
            'soldes' => $soldes->getSoldesResume((int) date('Y')),
        ]);
    }

    public function approve(int $id)
    {
        if ($redirect = $this->ensureAuthenticated(['rh', 'admin'])) {
            return $redirect;
        }

        $comment = trim((string) $this->request->getPost('commentaire_rh'));

        $demandes = new DemandeCongeModel();
        $demande = $demandes->find($id);

        if ($demande && $demande['statut'] === 'en attente') {
            $types = new TypeCongeModel();
            $type = $types->find((int) $demande['type_conge_id']);

            if ($type && (int) $type['deductible'] === 1) {
                $soldes = new SoldeCongeModel();
                $soldes->deductSolde((int) $demande['employe_id'], (int) $demande['type_conge_id'], (float) $demande['nb_jours'], (int) date('Y'));
            }

            $demandes->approveDemande($id, (int) $this->currentUser()['id'], $comment);
            $this->session->setFlashdata('success', 'Demande approuvee.');
        }

        return redirect()->back();
    }

    public function refuse(int $id)
    {
        if ($redirect = $this->ensureAuthenticated(['rh', 'admin'])) {
            return $redirect;
        }

        $comment = trim((string) $this->request->getPost('commentaire_rh'));

        $demandes = new DemandeCongeModel();
        $demande = $demandes->find($id);

        if ($demande && $demande['statut'] === 'en attente') {
            $demandes->refuseDemande($id, (int) $this->currentUser()['id'], $comment);
            $this->session->setFlashdata('success', 'Demande refusee.');
        }

        return redirect()->back();
    }

    private function normalizeStatut(?string $statut): ?string
    {
        if ($statut === null) {
            return null;
        }

        $value = strtolower(trim($statut));
        $value = str_replace(['é', 'è', 'ê'], 'e', $value);

        $allowed = ['en attente', 'approuvee', 'refusee', 'annulee'];
        return in_array($value, $allowed, true) ? $value : null;
    }
}
