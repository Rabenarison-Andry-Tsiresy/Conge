<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<section id="page-admin-employes" style="margin-top:3rem">
<div class="app-wrap">

  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon" style="background:var(--ink);border:1px solid rgba(255,255,255,.15)"><i class="bi bi-shield-check" style="color:var(--leaf)"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Administration</span></div>
    </div>
    <ul class="sidebar-nav" style="margin-top:1rem">
      <li><a href="<?= site_url('admin') ?>"><i class="bi bi-speedometer2"></i> Vue d'ensemble</a></li>
      <li><a href="<?= site_url('admin/demandes') ?>"><i class="bi bi-inbox"></i> Toutes les demandes</a></li>
      <li><a href="<?= site_url('admin/employes') ?>" class="active"><i class="bi bi-people"></i> Employes</a></li>
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
        <div class="topbar-title">Gestion des employes</div>
        <div class="topbar-breadcrumb"><a href="<?= site_url('admin') ?>">Admin</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Employes</div>
      </div>
      <div class="topbar-actions">
        <a href="#" class="btn-forest" style="padding:7px 14px;font-size:.82rem"><i class="bi bi-person-plus"></i> Ajouter</a>
      </div>
    </div>

    <div class="content">

      <!-- Formulaire ajout -->
      <div class="form-section">
        <h3><i class="bi bi-person-plus" style="color:var(--forest);margin-right:6px"></i>Ajouter un employé</h3>
        <?php if (!empty($success)) : ?>
          <div class="flash flash-success">
            <i class="bi bi-check-circle-fill"></i>
            <?= esc($success) ?>
          </div>
        <?php endif; ?>
        <form method="post" action="<?= site_url('admin/employes') ?>">
          <?= csrf_field() ?>
          <div class="form-grid-2" style="margin-bottom:1rem">
            <div class="f-group">
              <label class="f-label">Prenom</label>
              <input type="text" name="prenom" class="f-input" placeholder="Jean" value="<?= esc(old('prenom')) ?>"/>
              <?php if (!empty($errors['prenom'])) : ?>
                <div class="f-error"><i class="bi bi-exclamation-circle"></i> <?= esc($errors['prenom']) ?></div>
              <?php endif; ?>
            </div>
            <div class="f-group">
              <label class="f-label">Nom</label>
              <input type="text" name="nom" class="f-input" placeholder="Rakoto" value="<?= esc(old('nom')) ?>"/>
              <?php if (!empty($errors['nom'])) : ?>
                <div class="f-error"><i class="bi bi-exclamation-circle"></i> <?= esc($errors['nom']) ?></div>
              <?php endif; ?>
            </div>
            <div class="f-group">
              <label class="f-label">Email</label>
              <input type="email" name="email" class="f-input" placeholder="jean.rakoto@techmada.mg" value="<?= esc(old('email')) ?>"/>
              <?php if (!empty($errors['email'])) : ?>
                <div class="f-error"><i class="bi bi-exclamation-circle"></i> <?= esc($errors['email']) ?></div>
              <?php endif; ?>
            </div>
            <div class="f-group">
              <label class="f-label">Mot de passe initial</label>
              <input type="password" name="password" class="f-input" placeholder="A communiquer a l'employe"/>
              <?php if (!empty($errors['password'])) : ?>
                <div class="f-error"><i class="bi bi-exclamation-circle"></i> <?= esc($errors['password']) ?></div>
              <?php endif; ?>
            </div>
            <div class="f-group">
              <label class="f-label">Departement</label>
              <select name="departement_id" class="f-select">
                <?php foreach ($departements as $departement) : ?>
                  <option value="<?= (int) $departement['id'] ?>"><?= esc($departement['nom']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="f-group">
              <label class="f-label">Role</label>
              <select name="role" class="f-select">
                <option value="employe">Employe</option>
                <option value="rh">Responsable RH</option>
                <option value="admin">Administrateur</option>
              </select>
            </div>
            <div class="f-group">
              <label class="f-label">Date d'embauche</label>
              <input type="date" name="date_embauche" class="f-input" value="<?= esc(old('date_embauche')) ?>"/>
            </div>
          </div>
        <div class="flash flash-info" style="margin-bottom:1rem">
          <i class="bi bi-info-circle-fill"></i>
          <span style="font-size:.82rem">Les soldes de congés seront initialisés automatiquement selon les types de congé configurés.</span>
        </div>
        <div class="form-actions">
          <button class="btn-forest" type="submit"><i class="bi bi-plus"></i> Creer l'employe</button>
          <button class="btn-secondary" type="reset">Reinitialiser</button>
        </div>
        </form>
      </div>

      <!-- Liste employés -->
      <div class="data-card">
        <div class="data-card-head">
          <h3>Tous les employés</h3>
          <div style="display:flex;gap:6px">
            <input type="text" class="f-input" placeholder="Rechercher..." style="width:200px;padding:6px 10px;font-size:.8rem"/>
            <select class="f-select" style="font-size:.8rem;padding:6px 10px;width:auto">
              <option>Tous les depts</option>
            </select>
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
        <table class="tbl">
          <thead>
            <tr><th>Employé</th><th>Département</th><th>Rôle</th><th>Embauche</th><th>Statut</th><th>Solde annuel</th><th>Actions</th></tr>
          </thead>
          <tbody>
            <?php if (!empty($employes)) : ?>
              <?php foreach ($employes as $employe) : ?>
                <tr<?= ((int) $employe['actif'] === 1) ? '' : ' style="opacity:.5"' ?>>
                  <td>
                    <div class="profile-row">
                      <div class="avatar av-green" style="width:32px;height:32px;font-size:.68rem"><?= strtoupper(substr($employe['prenom'], 0, 1) . substr($employe['nom'], 0, 1)) ?></div>
                      <div class="profile-info"><div class="pname"><?= esc($employe['prenom'] . ' ' . $employe['nom']) ?></div><div class="pdept"><?= esc($employe['email']) ?></div></div>
                    </div>
                  </td>
                  <td class="td-muted"><?= esc($employe['departement_nom'] ?? '—') ?></td>
                  <td><span class="type-badge" style="background:#f1efe8;color:#444441"><?= esc($employe['role']) ?></span></td>
                  <td class="td-muted td-mono" style="font-size:.78rem"><?= esc($employe['date_embauche'] ?? '—') ?></td>
                  <td><span class="statut <?= ((int) $employe['actif'] === 1) ? 's-approuvee' : 's-annulee' ?>" style="font-size:.68rem"><?= ((int) $employe['actif'] === 1) ? 'actif' : 'inactif' ?></span></td>
                  <td><span style="font-family:'DM Mono',monospace;font-size:.82rem;color:var(--muted)">— / — j</span></td>
                  <td>
                    <div class="action-btns">
                      <button class="btn-sm btn-edit" type="button"><i class="bi bi-pencil"></i> Editer</button>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else : ?>
              <tr><td colspan="7"><div class="empty"><i class="bi bi-inbox"></i><p>Aucun employe trouve.</p></div></td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    </div>
    <div class="footer-app"><i class="bi bi-c-circle"></i> 2025 <span>TechMada RH</span></div>
  </div>

</div>
</section>

<?= $this->endSection() ?>
