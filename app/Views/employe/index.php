<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<section id="page-mes-conges" style="margin-top:3rem">
<div class="app-wrap">

  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon"><i class="bi bi-briefcase"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Espace employé</span></div>
    </div>
    <ul class="sidebar-nav" style="margin-top:1rem">
      <li><a href="<?= site_url('employe') ?>"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
      <li><a href="<?= site_url('employe/demandes/create') ?>"><i class="bi bi-plus-circle"></i> Nouvelle demande</a></li>
      <li><a href="<?= site_url('employe/demandes') ?>" class="active"><i class="bi bi-calendar3"></i> Mes demandes</a></li>
      <li><a href="#page-profil-employe"><i class="bi bi-person"></i> Mon profil</a></li>
    </ul>
    <div class="sidebar-user">
      <div class="s-user-row">
        <div class="avatar av-green">SR</div>
        <div><div class="user-name"><?= esc($currentUser['prenom'] . ' ' . $currentUser['nom']) ?></div><div class="user-role">Employe</div></div>
        <a href="<?= site_url('logout') ?>" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem" title="Deconnexion"><i class="bi bi-box-arrow-right"></i></a>
      </div>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Mes demandes de conge</div>
        <div class="topbar-breadcrumb"><a href="<?= site_url('employe') ?>">Accueil</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Mes demandes</div>
      </div>
      <div class="topbar-actions">
        <a href="<?= site_url('employe/demandes/create') ?>" class="btn-forest" style="padding:7px 14px;font-size:.82rem"><i class="bi bi-plus-lg"></i> Nouvelle demande</a>
      </div>
    </div>

    <div class="content">
      <div class="data-card">
        <div class="data-card-head">
          <h3>Toutes mes demandes</h3>
          <form method="get" style="display:flex;gap:6px">
            <select name="statut" class="f-select" style="font-size:.8rem;padding:6px 10px;width:auto" onchange="this.form.submit()">
              <option value="">Tous les statuts</option>
              <option value="en attente" <?= ($statut === 'en attente') ? 'selected' : '' ?>>En attente</option>
              <option value="approuvee" <?= ($statut === 'approuvee') ? 'selected' : '' ?>>Approuvee</option>
              <option value="refusee" <?= ($statut === 'refusee') ? 'selected' : '' ?>>Refusee</option>
              <option value="annulee" <?= ($statut === 'annulee') ? 'selected' : '' ?>>Annulee</option>
            </select>
          </form>
        </div>
        <table class="tbl">
          <thead>
            <tr><th>Type</th><th>Début</th><th>Fin</th><th>Durée</th><th>Statut</th><th>Commentaire RH</th><th>Action</th></tr>
          </thead>
          <tbody>
            <?php if (!empty($demandes)) : ?>
              <?php $statusMap = ['en attente' => 's-attente', 'approuvee' => 's-approuvee', 'refusee' => 's-refusee', 'annulee' => 's-annulee']; ?>
              <?php foreach ($demandes as $demande) : ?>
                <?php $statusClass = $statusMap[$demande['statut']] ?? 's-attente'; ?>
                <tr>
                  <td><span class="type-badge"><?= esc($demande['type_libelle']) ?></span></td>
                  <td class="td-muted"><?= esc($demande['date_debut']) ?></td>
                  <td class="td-muted"><?= esc($demande['date_fin']) ?></td>
                  <td class="td-mono"><?= (int) $demande['nb_jours'] ?> j</td>
                  <td><span class="statut <?= $statusClass ?>"><?= esc($demande['statut']) ?></span></td>
                  <td class="td-muted" style="font-size:.78rem"><?= $demande['commentaire_rh'] ? esc($demande['commentaire_rh']) : '—' ?></td>
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
              <tr><td colspan="7"><div class="empty"><i class="bi bi-inbox"></i><p>Aucune demande trouvee.</p></div></td></tr>
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
    <div class="footer-app"><i class="bi bi-c-circle"></i> 2025 <span>TechMada RH</span></div>
  </div>

</div>
</section>

<?= $this->endSection() ?>
