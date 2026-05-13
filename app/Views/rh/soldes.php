<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<section id="page-rh-soldes" style="margin-top:3rem">
<div class="app-wrap">

  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon"><i class="bi bi-person-check"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Espace responsable</span></div>
    </div>
    <div class="sidebar-section">Menu</div>
    <ul class="sidebar-nav">
      <li><a href="<?= site_url('rh') ?>"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
      <li><a href="<?= site_url('rh') ?>"><i class="bi bi-inbox"></i> Demandes a traiter</a></li>
      <li><a href="<?= site_url('rh') ?>"><i class="bi bi-archive"></i> Historique</a></li>
      <li><a href="<?= site_url('rh/soldes') ?>" class="active"><i class="bi bi-people"></i> Soldes employes</a></li>
      <li><a href="#page-profil-rh"><i class="bi bi-person"></i> Mon profil</a></li>
    </ul>
    <div class="sidebar-user">
      <div class="s-user-row">
        <div class="avatar av-blue">RH</div>
        <div><div class="user-name"><?= esc($currentUser['prenom'] . ' ' . $currentUser['nom']) ?></div><div class="user-role">Responsable RH</div></div>
        <a href="<?= site_url('logout') ?>" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem"><i class="bi bi-box-arrow-right"></i></a>
      </div>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Soldes des employes</div>
        <div class="topbar-breadcrumb"><a href="<?= site_url('rh') ?>">Accueil</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Soldes</div>
      </div>
    </div>

    <div class="content">
      <div class="data-card">
        <div class="data-card-head"><h3>Jours restants par employe</h3></div>
        <table class="tbl">
          <thead>
            <tr><th>Employe</th><th>Departement</th><th>Jours restants</th></tr>
          </thead>
          <tbody>
            <?php if (!empty($soldes)) : ?>
              <?php foreach ($soldes as $solde) : ?>
                <tr>
                  <td class="td-name"><?= esc($solde['prenom'] . ' ' . $solde['nom']) ?></td>
                  <td class="td-muted"><?= esc($solde['departement_nom'] ?? '—') ?></td>
                  <td class="td-mono"><?= (int) $solde['jours_restants'] ?> j</td>
                </tr>
              <?php endforeach; ?>
            <?php else : ?>
              <tr><td colspan="3"><div class="empty"><i class="bi bi-inbox"></i><p>Aucun solde disponible.</p></div></td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <div id="page-profil-rh" class="data-card" style="margin-top:1.5rem">
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
            <div class="td-name" style="font-size:.95rem"><?= esc($currentUser['role'] ?? 'rh') ?></div>
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
