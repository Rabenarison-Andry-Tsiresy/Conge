<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCongeTables extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INTEGER',
                'auto_increment' => true,
            ],
            'nom' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('conge_departements', true);

        $this->forge->addField([
            'id' => [
                'type' => 'INTEGER',
                'auto_increment' => true,
            ],
            'libelle' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'jours_annuels' => [
                'type' => 'INTEGER',
                'null' => false,
            ],
            'deductible' => [
                'type' => 'INTEGER',
                'constraint' => 1,
                'default' => 1,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('conge_types_conge', true);

        $this->forge->addField([
            'id' => [
                'type' => 'INTEGER',
                'auto_increment' => true,
            ],
            'nom' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'prenom' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'email' => [
                'type' => 'TEXT',
                'null' => false,
                'unique' => true,
            ],
            'password' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'role' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'departement_id' => [
                'type' => 'INTEGER',
                'null' => true,
            ],
            'date_embauche' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'actif' => [
                'type' => 'INTEGER',
                'constraint' => 1,
                'default' => 1,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('departement_id', 'conge_departements', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('conge_employes', true);

        $this->forge->addField([
            'id' => [
                'type' => 'INTEGER',
                'auto_increment' => true,
            ],
            'employe_id' => [
                'type' => 'INTEGER',
                'null' => false,
            ],
            'type_conge_id' => [
                'type' => 'INTEGER',
                'null' => false,
            ],
            'annee' => [
                'type' => 'INTEGER',
                'null' => false,
            ],
            'jours_attribues' => [
                'type' => 'REAL',
                'null' => false,
            ],
            'jours_pris' => [
                'type' => 'REAL',
                'default' => 0,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('employe_id', 'conge_employes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('type_conge_id', 'conge_types_conge', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('conge_soldes', true);

        $this->forge->addField([
            'id' => [
                'type' => 'INTEGER',
                'auto_increment' => true,
            ],
            'employe_id' => [
                'type' => 'INTEGER',
                'null' => false,
            ],
            'type_conge_id' => [
                'type' => 'INTEGER',
                'null' => false,
            ],
            'date_debut' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'date_fin' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'nb_jours' => [
                'type' => 'REAL',
                'null' => false,
            ],
            'motif' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'statut' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'commentaire_rh' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
                'default' => 'CURRENT_TIMESTAMP',
            ],
            'traite_par' => [
                'type' => 'INTEGER',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('employe_id', 'conge_employes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('type_conge_id', 'conge_types_conge', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('traite_par', 'conge_employes', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('conge_conges', true);

        $this->forge->addField([
            'id' => [
                'type' => 'INTEGER',
                'auto_increment' => true,
            ],
            'employe_id' => [
                'type' => 'INTEGER',
                'null' => false,
            ],
            'action' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'details' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
                'default' => 'CURRENT_TIMESTAMP',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('employe_id', 'conge_employes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('conge_historiques', true);

        $this->forge->addField([
            'id' => [
                'type' => 'INTEGER',
                'auto_increment' => true,
            ],
            'employe_id' => [
                'type' => 'INTEGER',
                'null' => false,
            ],
            'message' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'lu' => [
                'type' => 'INTEGER',
                'constraint' => 1,
                'default' => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
                'default' => 'CURRENT_TIMESTAMP',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('employe_id', 'conge_employes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('conge_notifications', true);

        $this->db->query(
            'CREATE VIEW reste_conges AS
            SELECT
                s.id,
                e.nom || " " || e.prenom AS employe,
                tc.libelle AS type_conge,
                s.annee,
                s.jours_attribues,
                s.jours_pris,
                (s.jours_attribues - s.jours_pris) AS reste
            FROM conge_soldes s
            JOIN conge_employes e ON s.employe_id = e.id
            JOIN conge_types_conge tc ON s.type_conge_id = tc.id'
        );
    }

    public function down()
    {
        $this->db->query('DROP VIEW IF EXISTS reste_conges');

        $this->forge->dropTable('conge_notifications', true);
        $this->forge->dropTable('conge_historiques', true);
        $this->forge->dropTable('conge_conges', true);
        $this->forge->dropTable('conge_soldes', true);
        $this->forge->dropTable('conge_employes', true);
        $this->forge->dropTable('conge_types_conge', true);
        $this->forge->dropTable('conge_departements', true);
    }
}
