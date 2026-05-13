<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<section id="page-admin-demandes" style="margin-top:3rem">
<div class="app-wrap">

  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon" style="background:var(--ink);border:1px solid rgba(255,255,255,.15)"><i class="bi bi-shield-check" style="color:var(--leaf)"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Administration</span></div>
    </div>
    <div class="sidebar-section">Gestion</div>
    <ul class="sidebar-nav">
      <li><a href="<?= site_url('admin') ?>"><i class="bi bi-speedometer2"></i> Vue d'ensemble</a></li>
      <li>
        <a href="<?= site_url('admin/demandes') ?>" class="active">
          <i class="bi bi-inbox"></i> Toutes les demandes
          <?php if (!empty($stats['en_attente'])) : ?>
          <span class="nav-badge alert"><?= (int) $stats['en_attente'] ?></span>
          <?php endif; ?>
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
        <div class="topbar-title">Demandes a traiter</div>
        <div class="topbar-breadcrumb"><a href="<?= site_url('admin') ?>">Administration</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Demandes</div>
      </div>
      <div class="topbar-actions">
        <span style="font-size:.8rem;color:var(--muted);background:var(--warn-bg);border:1px solid var(--warn-br);border-radius:6px;padding:5px 10px;display:flex;align-items:center;gap:5px;color:var(--warn)">
          <i class="bi bi-hourglass-split"></i> <?= (int) ($stats['en_attente'] ?? 0) ?> en attente
        </span>
      </div>
    </div>

    <div class="content">

      <?php if (!empty($success)) : ?>
      <div class="flash flash-success">
        <i class="bi bi-check-circle-fill"></i>
        <?= esc($success) ?>
      </div>
      <?php endif; ?>

      <?php
        $adminBaseUrl = site_url('admin/demandes');
        $adminUrlAttente = $adminBaseUrl . '?' . http_build_query(['statut' => 'en attente']);
        $adminUrlApprouvee = $adminBaseUrl . '?' . http_build_query(['statut' => 'approuvee']);
        $adminUrlRefusee = $adminBaseUrl . '?' . http_build_query(['statut' => 'refusee']);
      ?>
      <div style="display:flex;gap:8px;margin-bottom:1.25rem;flex-wrap:wrap">
        <a href="<?= $adminBaseUrl ?>" style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid var(--forest);background:var(--forest);color:var(--white);cursor:pointer;text-decoration:none">Tous (<?= (int) ($stats['total'] ?? 0) ?>)</a>
        <a href="<?= $adminUrlAttente ?>" style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid var(--border);background:var(--white);color:var(--muted);cursor:pointer;text-decoration:none">En attente (<?= (int) ($stats['en_attente'] ?? 0) ?>)</a>
        <a href="<?= $adminUrlApprouvee ?>" style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid var(--border);background:var(--white);color:var(--muted);cursor:pointer;text-decoration:none">Approuvees (<?= (int) ($stats['approuvee'] ?? 0) ?>)</a>
        <a href="<?= $adminUrlRefusee ?>" style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid var(--border);background:var(--white);color:var(--muted);cursor:pointer;text-decoration:none">Refusees (<?= (int) ($stats['refusee'] ?? 0) ?>)</a>
        <form method="get" style="margin-left:auto">
          <?php if (!empty($filters['statut'])) : ?>
            <input type="hidden" name="statut" value="<?= esc($filters['statut']) ?>"/>
          <?php endif; ?>
          <select name="departement_id" class="f-select" style="font-size:.8rem;padding:6px 10px;width:auto" onchange="this.form.submit()">
            <option value="">Tous les departements</option>
            <?php foreach ($departements as $departement) : ?>
              <option value="<?= (int) $departement['id'] ?>" <?= ((string) $filters['departement_id'] === (string) $departement['id']) ? 'selected' : '' ?>>
                <?= esc($departement['nom']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </form>
      </div>

      <div class="data-card">
        <div class="data-card-head"><h3>Toutes les demandes</h3></div>
        <table class="tbl">
          <thead>
            <tr><th>Employe</th><th>Type</th><th>Periode</th><th>Duree</th><th>Solde dispo</th><th>Statut</th><th>Actions</th></tr>
          </thead>
          <tbody>
            <?php if (!empty($demandes)) : ?>
              <?php $statusMap = ['en attente' => 's-attente', 'approuvee' => 's-approuvee', 'refusee' => 's-refusee', 'annulee' => 's-annulee']; ?>
              <?php foreach ($demandes as $demande) : ?>
                <?php
                  $statusClass = $statusMap[$demande['statut']] ?? 's-attente';
                  $soldeDispo = null;
                  if ($demande['jours_attribues'] !== null) {
                    $soldeDispo = (float) $demande['jours_attribues'] - (float) $demande['jours_pris'];
                  }
                ?>
                <tr>
                  <td>
                    <div class="profile-row">
                      <div class="avatar av-green" style="width:32px;height:32px;font-size:.7rem"><?= strtoupper(substr($demande['prenom'], 0, 1) . substr($demande['nom'], 0, 1)) ?></div>
                      <div class="profile-info">
                        <div class="pname"><?= esc($demande['prenom'] . ' ' . $demande['nom']) ?></div>
                        <div class="pdept"><?= esc($demande['departement_nom'] ?? '—') ?></div>
                      </div>
                    </div>
                  </td>
                  <td><span class="type-badge"><?= esc($demande['type_libelle']) ?></span></td>
                  <td class="td-muted" style="font-size:.8rem"><?= esc($demande['date_debut']) ?> – <?= esc($demande['date_fin']) ?></td>
                  <td class="td-mono"><?= (int) $demande['nb_jours'] ?> j</td>
                  <td>
                    <?php if ($soldeDispo !== null) : ?>
                      <span style="font-family:'DM Mono',monospace;font-size:.82rem;color:var(--success);font-weight:500"><?= (int) $soldeDispo ?> j</span>
                      <span style="font-size:.72rem;color:var(--muted)"> dispo</span>
                    <?php else : ?>
                      <span style="font-family:'DM Mono',monospace;font-size:.82rem;color:var(--muted)">—</span>
                    <?php endif; ?>
                  </td>
                  <td><span class="statut <?= $statusClass ?>"><?= esc($demande['statut']) ?></span></td>
                  <td>
                    <?php if ($demande['statut'] === 'en attente') : ?>
                      <div class="action-btns">
                        <form method="post" action="<?= site_url('admin/demandes/' . $demande['id'] . '/approve') ?>">
                          <?= csrf_field() ?>
                          <input type="text" name="commentaire_rh" class="f-input" placeholder="Commentaire (optionnel)" style="font-size:.72rem;padding:4px 6px;width:160px" />
                          <button class="btn-sm btn-approve" type="submit"><i class="bi bi-check-lg"></i> Approuver</button>
                        </form>
                        <form method="post" action="<?= site_url('admin/demandes/' . $demande['id'] . '/refuse') ?>">
                          <?= csrf_field() ?>
                          <input type="text" name="commentaire_rh" class="f-input" placeholder="Commentaire (optionnel)" style="font-size:.72rem;padding:4px 6px;width:160px" />
                          <button class="btn-sm btn-refuse" type="submit"><i class="bi bi-x-lg"></i> Refuser</button>
                        </form>
                      </div>
                    <?php else : ?>
                      <span class="td-muted" style="font-size:.75rem">Traite</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else : ?>
              <tr><td colspan="7"><div class="empty"><i class="bi bi-inbox"></i><p>Aucune demande a afficher.</p></div></td></tr>
            <?php endif; ?>
          </tbody>
        </table>
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
