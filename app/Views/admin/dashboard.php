<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<section id="page-dashboard-admin" style="margin-top:3rem">
<div class="app-wrap">

  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon" style="background:var(--ink);border:1px solid rgba(255,255,255,.15)"><i class="bi bi-shield-check" style="color:var(--leaf)"></i></div>
      <div class="sidebar-brand-name">TechMada RH
        <span>Administration</span>
      </div>
    </div>
    <div class="sidebar-section">Gestion</div>
    <ul class="sidebar-nav">
      <li><a href="<?= site_url('admin') ?>" class="active"><i class="bi bi-speedometer2"></i> Vue d'ensemble</a></li>
      <li>
        <a href="<?= site_url('admin/demandes') ?>">
          <i class="bi bi-inbox"></i> Toutes les demandes
          <span class="nav-badge alert"><?= (int) ($stats['demandes_attente'] ?? 0) ?></span>
        </a>
      </li>
      <li><a href="<?= site_url('admin/employes') ?>"><i class="bi bi-people"></i> Employes</a></li>
      <li><a href="<?= site_url('admin/departements') ?>"><i class="bi bi-building"></i> Departements</a></li>
      <li><a href="<?= site_url('admin/types-conge') ?>"><i class="bi bi-tags"></i> Types de conge</a></li>
      <li><a href="<?= site_url('admin/soldes') ?>"><i class="bi bi-sliders"></i> Soldes annuels</a></li>
      <li><a href="#page-profil-admin"><i class="bi bi-person"></i> Mon profil</a></li>
    </ul>
    <div class="sidebar-user">
      <div class="s-user-row">
        <div class="avatar" style="background:#5a2d82;width:32px;height:32px;font-size:.7rem">AD</div>
        <div><div class="user-name"><?= esc($currentUser['prenom'] . ' ' . $currentUser['nom']) ?></div><div class="user-role">Admin systeme</div></div>
        <a href="<?= site_url('logout') ?>" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem"><i class="bi bi-box-arrow-right"></i></a>
      </div>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Vue d'ensemble</div>
        <div class="topbar-breadcrumb">Administration</div>
      </div>
      <div class="topbar-actions">
        <a href="<?= site_url('admin/employes') ?>" class="btn-forest" style="padding:7px 14px;font-size:.82rem"><i class="bi bi-person-plus"></i> Ajouter un employe</a>
      </div>
    </div>

    <div class="content">

      <!-- Métriques admin -->
      <div class="metrics">
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-forest"><i class="bi bi-people"></i></div></div>
          <div class="metric-val"><?= (int) ($stats['employes_actifs'] ?? 0) ?></div>
          <div class="metric-label">Employes actifs</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-amber"><i class="bi bi-hourglass-split"></i></div></div>
          <div class="metric-val"><?= (int) ($stats['demandes_attente'] ?? 0) ?></div>
          <div class="metric-label">Demandes en attente</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-green"><i class="bi bi-calendar-check"></i></div></div>
          <div class="metric-val"><?= (int) ($stats['approuvees_mois'] ?? 0) ?></div>
          <div class="metric-label">Approuvees ce mois</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-blue"><i class="bi bi-building"></i></div></div>
          <div class="metric-val"><?= (int) ($stats['departements'] ?? 0) ?></div>
          <div class="metric-label">Departements</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-red"><i class="bi bi-person-slash"></i></div></div>
          <div class="metric-val"><?= (int) ($stats['absents'] ?? 0) ?></div>
          <div class="metric-label">Absents aujourd'hui</div>
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 320px;gap:1.5rem;align-items:start">

        <!-- Demandes récentes -->
        <div class="data-card" style="margin:0">
          <div class="data-card-head">
            <h3>Demandes recentes</h3>
            <a href="<?= site_url('admin/demandes') ?>" style="font-size:.8rem;color:var(--forest);text-decoration:none">Tout voir →</a>
          </div>
          <table class="tbl">
            <thead>
              <tr><th>Employé</th><th>Type</th><th>Durée</th><th>Statut</th></tr>
            </thead>
            <tbody>
              <?php if (!empty($recentDemandes)) : ?>
                <?php $statusMap = ['en attente' => 's-attente', 'approuvee' => 's-approuvee', 'refusee' => 's-refusee', 'annulee' => 's-annulee']; ?>
                <?php foreach ($recentDemandes as $demande) : ?>
                  <?php $statusClass = $statusMap[$demande['statut']] ?? 's-attente'; ?>
                  <tr>
                    <td><div style="display:flex;align-items:center;gap:7px"><div class="avatar av-green" style="width:28px;height:28px;font-size:.62rem"><?= strtoupper(substr($demande['prenom'], 0, 1) . substr($demande['nom'], 0, 1)) ?></div><span class="td-name" style="font-size:.84rem"><?= esc($demande['prenom'] . ' ' . $demande['nom']) ?></span></div></td>
                    <td><span class="type-badge"><?= esc($demande['type_libelle']) ?></span></td>
                    <td class="td-mono"><?= (int) $demande['nb_jours'] ?> j</td>
                    <td><span class="statut <?= $statusClass ?>"><?= esc($demande['statut']) ?></span></td>
                  </tr>
                <?php endforeach; ?>
              <?php else : ?>
                <tr><td colspan="4"><div class="empty"><i class="bi bi-inbox"></i><p>Aucune demande recente.</p></div></td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- Absents du jour + soldes critiques -->
        <div style="display:flex;flex-direction:column;gap:1rem">
          <div class="data-card" style="margin:0">
            <div class="data-card-head"><h3><i class="bi bi-person-slash" style="color:var(--muted);margin-right:5px"></i>Absents aujourd'hui</h3></div>
            <div style="padding:.75rem 1.1rem;display:flex;flex-direction:column;gap:.6rem">
              <?php if (!empty($absents)) : ?>
                <?php foreach ($absents as $absent) : ?>
                  <div style="display:flex;align-items:center;gap:8px">
                    <div class="avatar av-green" style="width:30px;height:30px;font-size:.65rem"><?= strtoupper(substr($absent['prenom'], 0, 1) . substr($absent['nom'], 0, 1)) ?></div>
                    <div><div style="font-size:.83rem;font-weight:500;color:var(--ink)"><?= esc($absent['prenom'] . ' ' . $absent['nom']) ?></div><div style="font-size:.72rem;color:var(--muted)"><?= esc($absent['type_libelle']) ?> · retour <?= esc($absent['date_fin']) ?></div></div>
                  </div>
                <?php endforeach; ?>
              <?php else : ?>
                <div class="empty"><i class="bi bi-inbox"></i><p>Aucun absent aujourd'hui.</p></div>
              <?php endif; ?>
            </div>
          </div>
          <div class="flash flash-warn" style="margin:0">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span style="font-size:.8rem">Surveillez les soldes critiques dans l'espace admin.</span>
          </div>
        </div>

      </div>

      <div id="page-profil-admin" class="data-card" style="margin-top:1.5rem">
        <div class="data-card-head"><h3>Mon profil</h3></div>
        <div style="padding:1rem 1.25rem;display:grid;grid-template-columns:1fr 1fr;gap:1rem">
          <div>
            <div class="td-muted" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.08em">Nom complet</div>
            <div class="td-name" style="font-size:.95rem"><?= esc($currentUser['prenom'] . ' ' . $currentUser['nom']) ?></div>
          </div>
          <div>
            <div class="td-muted" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.08em">Email</div>
            <div class="td-name" style="font-size:.95rem"><?= esc($currentUser['email'] ?? '—') ?></div>
          </div>
          <div>
            <div class="td-muted" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.08em">Role</div>
            <div class="td-name" style="font-size:.95rem"><?= esc($currentUser['role'] ?? 'admin') ?></div>
          </div>
          <div>
            <div class="td-muted" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.08em">Identifiant</div>
            <div class="td-mono" style="font-size:.95rem">#<?= (int) ($currentUser['id'] ?? 0) ?></div>
          </div>
        </div>
      </div>

    </div>
    <div class="footer-app"><i class="bi bi-c-circle"></i> 2025 <span>TechMada RH</span></div>
  </div>

</div>
</section>

<?= $this->endSection() ?>
