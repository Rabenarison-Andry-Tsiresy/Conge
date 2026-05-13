<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CongeModel;
use App\Models\SoldeModel;
use App\Models\EmployeModel;
use App\Models\DepartementModel;

class ResponsableRHController extends BaseController
{
    // -------------------------------------------------------------------------
    // Demandes
    // -------------------------------------------------------------------------

    public function consulterDemandes()
    {
        $data['conges']       = (new CongeModel())->findEnAttente();
        $data['departements'] = (new DepartementModel())->findAllSorted();

        return view('rh/demandes', $data);
    }

    public function filtreDemande()
    {
        $statut        = $this->request->getGet('statut');
        $departementId = $this->request->getGet('departement_id');

        $data['conges']       = (new CongeModel())->findFiltrees($statut ?: null, $departementId ?: null);
        $data['departements'] = (new DepartementModel())->findAllSorted();

        return view('rh/demandes', $data);
    }

    public function approuverDemande(int $congeId)
    {
        $congeModel = new CongeModel();
        $conge      = $congeModel->find($congeId);

        if (!$conge || $conge['statut'] !== CongeModel::STATUT_EN_ATTENTE) {
            return redirect()->to('/rh/demandes')->with('error', 'Demande introuvable ou déjà traitée.');
        }

        $soldeModel = new SoldeModel();

        try {
            $soldeModel->deduire(
                $conge['employe_id'],
                $conge['type_conge_id'],
                date('Y', strtotime($conge['date_debut'])),
                $conge['nb_jours']
            );
        } catch (\RuntimeException $e) {
            return redirect()->to('/rh/demandes')->with('error', $e->getMessage());
        }

        $congeModel->approuver($congeId, session('employe_id'), $this->request->getPost('commentaire'));

        return redirect()->to('/rh/demandes')->with('success', 'Demande approuvée et solde mis à jour.');
    }

    public function refuserDemande(int $congeId)
    {
        $congeModel = new CongeModel();
        $conge      = $congeModel->find($congeId);

        if (!$conge || $conge['statut'] !== CongeModel::STATUT_EN_ATTENTE) {
            return redirect()->to('/rh/demandes')->with('error', 'Demande introuvable ou déjà traitée.');
        }

        $congeModel->refuser($congeId, session('employe_id'), $this->request->getPost('commentaire'));

        return redirect()->to('/rh/demandes')->with('success', 'Demande refusée.');
    }

    // -------------------------------------------------------------------------
    // Soldes
    // -------------------------------------------------------------------------

    public function getSoldeEmploye(int $employeId)
    {
        $annee = $this->request->getGet('annee') ?? date('Y');

        $data['employe'] = (new EmployeModel())->find($employeId);
        $data['soldes']  = (new SoldeModel())->findByEmploye($employeId, $annee);
        $data['annee']   = $annee;

        return view('rh/solde_employe', $data);
    }

    public function updateSoldeConge(int $employeId)
    {
        $typeCongeId     = $this->request->getPost('type_conge_id');
        $annee           = $this->request->getPost('annee');
        $joursAttribues  = $this->request->getPost('jours_attribues');

        $soldeModel = new SoldeModel();
        $solde      = $soldeModel->findSolde($employeId, $typeCongeId, $annee);

        if ($solde) {
            $soldeModel->update($solde['id'], ['jours_attribues' => $joursAttribues]);
        } else {
            $soldeModel->insert([
                'employe_id'     => $employeId,
                'type_conge_id'  => $typeCongeId,
                'annee'          => $annee,
                'jours_attribues' => $joursAttribues,
                'jours_pris'     => 0,
            ]);
        }

        return redirect()->to("/rh/employe/{$employeId}/solde")->with('success', 'Solde mis à jour.');
    }
}