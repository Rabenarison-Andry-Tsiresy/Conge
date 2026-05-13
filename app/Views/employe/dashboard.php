<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<section id="page-dashboard-employe" style="margin-top:3rem">
<div class="app-wrap">

  <!-- SIDEBAR EMPLOYÉ -->
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon"><i class="bi bi-briefcase"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Espace employé</span></div>
    </div>
    <div class="sidebar-section">Menu</div>
    <ul class="sidebar-nav">
      <li><a href="<?= site_url('employe') ?>" class="active"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
      <li><a href="<?= site_url('employe/demandes/create') ?>"><i class="bi bi-plus-circle"></i> Nouvelle demande</a></li>
      <li>
        <a href="<?= site_url('employe/demandes') ?>">
          <i class="bi bi-calendar3"></i> Mes demandes
          <?php if (!empty($stats['en_attente'])) : ?>
          <span class="nav-badge alert"><?= (int) $stats['en_attente'] ?></span>
          <?php endif; ?>
        </a>
      </li>
      <li><a href="#page-profil-employe"><i class="bi bi-person"></i> Mon profil</a></li>
    </ul>
    <div class="sidebar-user">
      <div class="s-user-row">
        <div class="avatar av-green">SR</div>
        <div>
          <div class="user-name"><?= esc($currentUser['prenom'] . ' ' . $currentUser['nom']) ?></div>
          <div class="user-role">Employe</div>
        </div>
        <a href="<?= site_url('logout') ?>" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem" title="Deconnexion"><i class="bi bi-box-arrow-right"></i></a>
      </div>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Tableau de bord</div>
        <div class="topbar-breadcrumb">Accueil</div>
      </div>
      <div class="topbar-actions">
        <a href="<?= site_url('employe/demandes/create') ?>" class="btn-forest" style="padding:7px 14px;font-size:.82rem">
          <i class="bi bi-plus-lg"></i> Nouvelle demande
        </a>
      </div>
    </div>

    <div class="content">

      <!-- Flash succès -->
      <?php if (!empty($success)) : ?>
      <div class="flash flash-success">
        <i class="bi bi-check-circle-fill"></i>
        <?= esc($success) ?>
      </div>
      <?php endif; ?>

      <!-- Métriques -->
      <div class="metrics">
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-amber"><i class="bi bi-hourglass-split"></i></div></div>
          <div class="metric-val"><?= (int) ($stats['en_attente'] ?? 0) ?></div>
          <div class="metric-label">En attente</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-green"><i class="bi bi-check-circle"></i></div></div>
          <div class="metric-val"><?= (int) ($stats['approuvee'] ?? 0) ?></div>
          <div class="metric-label">Approuvées</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-forest"><i class="bi bi-calendar-check"></i></div></div>
          <?php
            $totalAttribues = 0;
            $totalRestants = 0;
            foreach ($soldes as $solde) {
              $totalAttribues += (float) $solde['jours_attribues'];
              $totalRestants += (float) $solde['jours_attribues'] - (float) $solde['jours_pris'];
            }
          ?>
          <div class="metric-val"><?= (int) $totalRestants ?></div>
          <div class="metric-label">Jours restants</div>
          <div class="metric-sub">sur <?= (int) $totalAttribues ?> cette annee</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-red"><i class="bi bi-x-circle"></i></div></div>
          <div class="metric-val"><?= (int) ($stats['refusee'] ?? 0) ?></div>
          <div class="metric-label">Refusée</div>
        </div>
      </div>

      <!-- Soldes de congés -->
      <div class="data-card">
        <div class="data-card-head"><h3>Mes soldes de conges — <?= date('Y') ?></h3></div>
        <div style="padding:1rem 1.25rem;display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem">
          <?php if (!empty($soldes)) : ?>
            <?php foreach ($soldes as $solde) : ?>
              <?php
                $attribues = (float) $solde['jours_attribues'];
                $pris = (float) $solde['jours_pris'];
                $restants = $attribues - $pris;
                $pct = $attribues > 0 ? max(0, min(100, ($restants / $attribues) * 100)) : 0;
              ?>
              <div class="solde-card" style="margin:0">
                <div class="solde-header">
                  <span class="solde-type"><?= esc($solde['libelle']) ?></span>
                  <span class="solde-nums"><strong><?= (int) $restants ?></strong> / <?= (int) $attribues ?> j</span>
                </div>
                <div class="solde-bar"><div class="solde-fill" style="width:<?= (int) $pct ?>%"></div></div>
                <div class="solde-label"><?= (int) $restants ?> jours restants · <?= (int) $pris ?> pris</div>
              </div>
            <?php endforeach; ?>
          <?php else : ?>
            <div class="empty"><i class="bi bi-inbox"></i><p>Aucun solde configure pour cette annee.</p></div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Dernières demandes -->
      <div class="data-card">
        <div class="data-card-head">
          <h3>Mes dernières demandes</h3>
          <a href="<?= site_url('employe/demandes') ?>" style="font-size:.8rem;color:var(--forest);text-decoration:none">Voir tout →</a>
        </div>
        <table class="tbl">
          <thead>
            <tr><th>Type</th><th>Du</th><th>Au</th><th>Durée</th><th>Statut</th><th>Action</th></tr>
          </thead>
          <tbody>
            <?php if (!empty($demandes)) : ?>
              <?php $statusMap = ['en attente' => 's-attente', 'approuvee' => 's-approuvee', 'refusee' => 's-refusee', 'annulee' => 's-annulee']; ?>
              <?php foreach ($demandes as $demande) : ?>
                <tr>
                  <td><span class="type-badge"><?= esc($demande['type_libelle']) ?></span></td>
                  <td class="td-muted"><?= esc($demande['date_debut']) ?></td>
                  <td class="td-muted"><?= esc($demande['date_fin']) ?></td>
                  <td class="td-mono"><?= (int) $demande['nb_jours'] ?> j</td>
                  <?php $statusClass = $statusMap[$demande['statut']] ?? 's-attente'; ?>
                  <td><span class="statut <?= $statusClass ?>"><?= esc($demande['statut']) ?></span></td>
                  <td>
                    <?php if ($demande['statut'] === 'en attente') : ?>
                      <form method="post" action="<?= site_url('employe/demandes/' . $demande['id'] . '/cancel') ?>">
                        <?= csrf_field() ?>
                        <button class="btn-sm btn-cancel" type="submit"><i class="bi bi-x"></i> Annuler</button>
                      </form>
                    <?php else : ?>
                      <span class="td-muted" style="font-size:.75rem">—</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else : ?>
              <tr><td colspan="6"><div class="empty"><i class="bi bi-inbox"></i><p>Aucune demande recente.</p></div></td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <div id="page-profil-employe" class="data-card" style="margin-top:1.5rem">
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
            <div class="td-name" style="font-size:.95rem"><?= esc($currentUser['role'] ?? 'employe') ?></div>
          </div>
          <div>
            <div class="td-muted" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.08em">Identifiant</div>
            <div class="td-mono" style="font-size:.95rem">#<?= (int) ($currentUser['id'] ?? 0) ?></div>
          </div>
        </div>
      </div>

    </div>
    <div class="footer-app"><i class="bi bi-c-circle"></i> 2025 <span>TechMada RH</span> — Projet CodeIgniter 4</div>
  </div>

</div>
</section>

<?= $this->endSection() ?>
