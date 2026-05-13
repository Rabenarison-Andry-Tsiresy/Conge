<?php

namespace App\Models;

class DashboardModel
{
    public function getAdminStats(): array
    {
        $db = \Config\Database::connect();
        $today = date('Y-m-d');
        $monthStart = date('Y-m-01');
        $monthEnd = date('Y-m-t');

        $employes = (int) $db->table('conge_employes')->where('actif', 1)->countAllResults();
        $demandesAttente = (int) $db->table('conge_conges')->where('statut', 'en attente')->countAllResults();
        $approuveesMois = (int) $db->table('conge_conges')
            ->where('statut', 'approuvee')
            ->where('date_debut >=', $monthStart)
            ->where('date_debut <=', $monthEnd)
            ->countAllResults();
        $departements = (int) $db->table('conge_departements')->countAllResults();
        $absents = (int) $db->table('conge_conges')
            ->where('statut', 'approuvee')
            ->where('date_debut <=', $today)
            ->where('date_fin >=', $today)
            ->countAllResults();

        return [
            'employes_actifs' => $employes,
            'demandes_attente' => $demandesAttente,
            'approuvees_mois' => $approuveesMois,
            'departements' => $departements,
            'absents' => $absents,
        ];
    }

    public function getRecentDemandes(int $limit = 5): array
    {
        $db = \Config\Database::connect();

        return $db->table('conge_conges')
            ->select('conge_conges.*, conge_employes.nom, conge_employes.prenom, conge_types_conge.libelle AS type_libelle')
            ->join('conge_employes', 'conge_employes.id = conge_conges.employe_id')
            ->join('conge_types_conge', 'conge_types_conge.id = conge_conges.type_conge_id')
            ->orderBy('conge_conges.created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    public function getAbsentsToday(int $limit = 5): array
    {
        $db = \Config\Database::connect();
        $today = date('Y-m-d');

        return $db->table('conge_conges')
            ->select('conge_conges.*, conge_employes.nom, conge_employes.prenom, conge_types_conge.libelle AS type_libelle')
            ->join('conge_employes', 'conge_employes.id = conge_conges.employe_id')
            ->join('conge_types_conge', 'conge_types_conge.id = conge_conges.type_conge_id')
            ->where('conge_conges.statut', 'approuvee')
            ->where('conge_conges.date_debut <=', $today)
            ->where('conge_conges.date_fin >=', $today)
            ->orderBy('conge_conges.date_fin', 'ASC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }
}
