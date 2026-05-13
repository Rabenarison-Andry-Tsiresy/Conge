<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<section id="page-form-conge" style="margin-top:3rem">
<div class="app-wrap">

  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon"><i class="bi bi-briefcase"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Espace employé</span></div>
    </div>
    <ul class="sidebar-nav" style="margin-top:1rem">
      <li><a href="<?= site_url('employe') ?>"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
      <li><a href="<?= site_url('employe/demandes/create') ?>" class="active"><i class="bi bi-plus-circle"></i> Nouvelle demande</a></li>
      <li><a href="<?= site_url('employe/demandes') ?>"><i class="bi bi-calendar3"></i> Mes demandes</a></li>
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
        <div class="topbar-title">Nouvelle demande de conge</div>
        <div class="topbar-breadcrumb">
          <a href="<?= site_url('employe') ?>">Accueil</a>
          <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Nouvelle demande
        </div>
      </div>
    </div>

    <div class="content">

      <div style="display:grid;grid-template-columns:1fr 300px;gap:1.5rem;align-items:start" class="form-layout">

        <!-- Formulaire principal -->
        <div>
          <div class="form-section">
            <h3>Détails de la demande</h3>

            <form method="post" action="<?= site_url('employe/demandes') ?>">
              <?= csrf_field() ?>
              <?php
                $soldeMap = [];
                foreach ($soldes as $solde) {
                  $soldeMap[(int) $solde['type_conge_id']] = (float) $solde['jours_attribues'] - (float) $solde['jours_pris'];
                }
              ?>
              <div class="f-group" style="margin-bottom:1rem">
                <label class="f-label">Type de conge <span style="color:var(--danger)">*</span></label>
                <select name="type_conge_id" class="f-select">
                  <option value="">-- Choisir un type --</option>
                  <?php foreach ($types as $type) : ?>
                    <?php $restant = $soldeMap[(int) $type['id']] ?? null; ?>
                    <option value="<?= (int) $type['id'] ?>" <?= ($form['type_conge_id'] == $type['id']) ? 'selected' : '' ?>>
                      <?= esc($type['libelle']) ?><?= $restant !== null ? ' (' . (int) $restant . ' j restants)' : '' ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <?php if (!empty($errors['type_conge_id'])) : ?>
                  <div class="f-error"><i class="bi bi-exclamation-circle"></i> <?= esc($errors['type_conge_id']) ?></div>
                <?php endif; ?>
              </div>

            <div class="form-grid-2" style="margin-bottom:1rem">
              <div class="f-group">
                <label class="f-label">Date de debut <span style="color:var(--danger)">*</span></label>
                <input type="date" name="date_debut" class="f-input" value="<?= esc($form['date_debut']) ?>"/>
                <?php if (!empty($errors['date_debut'])) : ?>
                  <div class="f-error"><i class="bi bi-exclamation-circle"></i> <?= esc($errors['date_debut']) ?></div>
                <?php endif; ?>
              </div>
              <div class="f-group">
                <label class="f-label">Date de fin <span style="color:var(--danger)">*</span></label>
                <input type="date" name="date_fin" class="f-input" value="<?= esc($form['date_fin']) ?>"/>
                <?php if (!empty($errors['date_fin'])) : ?>
                  <div class="f-error"><i class="bi bi-exclamation-circle"></i> <?= esc($errors['date_fin']) ?></div>
                <?php endif; ?>
              </div>
            </div>

            <!-- Calcul automatique côté PHP (affiché après soumission ou en JS) -->
            <?php if (!empty($form['computed_days'])) : ?>
              <div class="f-computed">
                <div class="f-computed-num"><?= (int) $form['computed_days'] ?></div>
                <div class="f-computed-label">jours calendaires calcules<br><span style="font-size:.7rem;opacity:.7"><?= esc($form['computed_range']) ?></span></div>
              </div>
            <?php endif; ?>

            <div class="f-group" style="margin-bottom:1rem">
              <label class="f-label">Motif (optionnel)</label>
              <textarea name="motif" class="f-textarea" placeholder="Precisez le motif de votre demande si necessaire..."><?= esc($form['motif']) ?></textarea>
              <div class="f-hint">Le motif est visible par le responsable RH.</div>
            </div>

            <div class="form-actions">
              <button class="btn-forest" type="submit"><i class="bi bi-send"></i> Soumettre la demande</button>
              <a href="<?= site_url('employe') ?>" class="btn-secondary"><i class="bi bi-x"></i> Annuler</a>
            </div>
            </form>
          </div>
        </div>

        <!-- Panneau latéral : solde & règles -->
        <div style="display:flex;flex-direction:column;gap:1rem">
          <div class="data-card" style="margin:0">
            <div class="data-card-head"><h3><i class="bi bi-piggy-bank" style="color:var(--forest);margin-right:5px"></i>Vos soldes actuels</h3></div>
            <div style="padding:.75rem 1.1rem;display:flex;flex-direction:column;gap:.75rem">
              <?php if (!empty($soldes)) : ?>
                <?php foreach ($soldes as $solde) : ?>
                  <?php
                    $attribues = (float) $solde['jours_attribues'];
                    $pris = (float) $solde['jours_pris'];
                    $restants = $attribues - $pris;
                    $pct = $attribues > 0 ? max(0, min(100, ($restants / $attribues) * 100)) : 0;
                  ?>
                  <div>
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
                      <span style="font-size:.8rem;color:var(--ink)"><?= esc($solde['libelle']) ?></span>
                      <span style="font-family:'DM Mono',monospace;font-size:.8rem;color:var(--forest);font-weight:500"><?= (int) $restants ?> j</span>
                    </div>
                    <div class="solde-bar"><div class="solde-fill" style="width:<?= (int) $pct ?>%"></div></div>
                  </div>
                <?php endforeach; ?>
              <?php else : ?>
                <div class="empty"><i class="bi bi-inbox"></i><p>Aucun solde configure.</p></div>
              <?php endif; ?>
            </div>
          </div>
          <div class="flash flash-info" style="margin:0">
            <i class="bi bi-info-circle-fill"></i>
            <span style="font-size:.8rem">Le solde est déduit uniquement à l'approbation de votre responsable.</span>
          </div>
          <div style="background:var(--cream);border:1px solid var(--border);border-radius:8px;padding:.85rem 1rem">
            <div style="font-size:.78rem;font-weight:500;color:var(--ink);margin-bottom:.5rem"><i class="bi bi-clipboard-check" style="color:var(--forest);margin-right:5px"></i>Rappel des règles</div>
            <ul style="margin:0;padding-left:1rem;font-size:.75rem;color:var(--muted);line-height:1.7">
              <li>Préavis minimum : 48h avant la date de début</li>
              <li>Pas de chevauchement avec une demande en cours</li>
              <li>Solde insuffisant = demande refusée automatiquement</li>
            </ul>
          </div>
        </div>

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
