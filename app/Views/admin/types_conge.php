<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<section id="page-admin-types-conge" style="margin-top:3rem">
<div class="app-wrap">

  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon" style="background:var(--ink);border:1px solid rgba(255,255,255,.15)"><i class="bi bi-shield-check" style="color:var(--leaf)"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Administration</span></div>
    </div>
    <div class="sidebar-section">Gestion</div>
    <ul class="sidebar-nav">
      <li><a href="<?= site_url('admin') ?>"><i class="bi bi-speedometer2"></i> Vue d'ensemble</a></li>
      <li><a href="<?= site_url('admin/demandes') ?>"><i class="bi bi-inbox"></i> Toutes les demandes</a></li>
      <li><a href="<?= site_url('admin/employes') ?>"><i class="bi bi-people"></i> Employes</a></li>
      <li><a href="<?= site_url('admin/departements') ?>"><i class="bi bi-building"></i> Departements</a></li>
      <li><a href="<?= site_url('admin/types-conge') ?>" class="active"><i class="bi bi-tags"></i> Types de conge</a></li>
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
        <div class="topbar-title">Types de conge</div>
        <div class="topbar-breadcrumb"><a href="<?= site_url('admin') ?>">Administration</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Types de conge</div>
      </div>
    </div>

    <div class="content">
      <div class="form-section">
        <h3><i class="bi bi-tags" style="color:var(--forest);margin-right:6px"></i>Ajouter un type de conge</h3>
        <?php if (!empty($success)) : ?>
          <div class="flash flash-success">
            <i class="bi bi-check-circle-fill"></i>
            <?= esc($success) ?>
          </div>
        <?php endif; ?>
        <form method="post" action="<?= site_url('admin/types-conge') ?>">
          <?= csrf_field() ?>
          <div class="form-grid-2" style="margin-bottom:1rem">
            <div class="f-group">
              <label class="f-label">Libelle</label>
              <input type="text" name="libelle" class="f-input" placeholder="Conge annuel" value="<?= esc(old('libelle')) ?>"/>
              <?php if (!empty($errors['libelle'])) : ?>
                <div class="f-error"><i class="bi bi-exclamation-circle"></i> <?= esc($errors['libelle']) ?></div>
              <?php endif; ?>
            </div>
            <div class="f-group">
              <label class="f-label">Jours annuels</label>
              <input type="number" step="0.5" min="0" name="jours_annuels" class="f-input" placeholder="30" value="<?= esc(old('jours_annuels')) ?>"/>
              <?php if (!empty($errors['jours_annuels'])) : ?>
                <div class="f-error"><i class="bi bi-exclamation-circle"></i> <?= esc($errors['jours_annuels']) ?></div>
              <?php endif; ?>
            </div>
            <div class="f-group">
              <label class="f-label">Deductible</label>
              <select name="deductible" class="f-select">
                <?php $deductibleValue = old('deductible'); ?>
                <option value="1" <?= ((string) $deductibleValue === '1') ? 'selected' : '' ?>>Oui</option>
                <option value="0" <?= ((string) $deductibleValue === '0') ? 'selected' : '' ?>>Non</option>
              </select>
            </div>
          </div>
          <div class="form-actions">
            <button class="btn-forest" type="submit"><i class="bi bi-plus"></i> Creer</button>
            <button class="btn-secondary" type="reset">Reinitialiser</button>
          </div>
        </form>
      </div>

      <div class="data-card">
        <div class="data-card-head"><h3>Liste des types de conge</h3></div>
        <table class="tbl">
          <thead>
            <tr><th>Libelle</th><th>Jours annuels</th><th>Deductible</th></tr>
          </thead>
          <tbody>
            <?php if (!empty($types)) : ?>
              <?php foreach ($types as $type) : ?>
                <tr>
                  <td class="td-name"><?= esc($type['libelle']) ?></td>
                  <td class="td-mono"><?= (float) $type['jours_annuels'] ?> j</td>
                  <td class="td-muted"><?= ((int) $type['deductible'] === 1) ? 'Oui' : 'Non' ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else : ?>
              <tr><td colspan="3"><div class="empty"><i class="bi bi-inbox"></i><p>Aucun type de conge trouve.</p></div></td></tr>
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
