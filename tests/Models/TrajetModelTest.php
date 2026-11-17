<?php

declare(strict_types=1);

namespace Tests\Models;

use App\Models\TrajetModel;
use PDOException;
use Tests\DatabaseTestCase;

/**
 * Tests des opérations d'écriture sur la table trajet.
 */
final class TrajetModelTest extends DatabaseTestCase
{
    private TrajetModel $model;

    private int $auteurId;

    private int $agenceA;

    private int $agenceB;

    /**
     * Prépare un auteur et deux agences pour chaque test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->model = new TrajetModel($this->pdo);
        $this->auteurId = $this->creerEmploye();
        $this->agenceA = $this->creerAgence('Agence Test Alpha');
        $this->agenceB = $this->creerAgence('Agence Test Beta');
    }

    /**
     * Données valides d'un trajet, modifiables par chaque test.
     *
     * @param array<string, int|string> $remplacements Valeurs à remplacer.
     *
     * @return array{id_agence_depart: int, id_agence_arrivee: int, date_heure_depart: string,
     *               date_heure_arrivee: string, nb_places_total: int, nb_places_disponibles: int,
     *               id_employe: int}
     */
    private function donneesTrajet(array $remplacements = []): array
    {
        /** @var array{id_agence_depart: int, id_agence_arrivee: int, date_heure_depart: string,
         *             date_heure_arrivee: string, nb_places_total: int, nb_places_disponibles: int,
         *             id_employe: int} $donnees */
        $donnees = array_merge([
            'id_agence_depart'      => $this->agenceA,
            'id_agence_arrivee'     => $this->agenceB,
            'date_heure_depart'     => date('Y-m-d H:i:s', strtotime('+2 days 08:00')),
            'date_heure_arrivee'    => date('Y-m-d H:i:s', strtotime('+2 days 12:00')),
            'nb_places_total'       => 4,
            'nb_places_disponibles' => 3,
            'id_employe'            => $this->auteurId,
        ], $remplacements);

        return $donnees;
    }

    public function testCreateEnregistreLeTrajetEtRetourneSonIdentifiant(): void
    {
        $id = $this->model->create($this->donneesTrajet());

        $trajet = $this->lireTrajet($id);
        $this->assertNotFalse($trajet);
        $this->assertSame($this->agenceA, (int) $trajet['id_agence_depart']);
        $this->assertSame($this->agenceB, (int) $trajet['id_agence_arrivee']);
        $this->assertSame(4, (int) $trajet['nb_places_total']);
        $this->assertSame(3, (int) $trajet['nb_places_disponibles']);
        $this->assertSame($this->auteurId, (int) $trajet['id_employe']);
    }

    public function testUpdateModifieLeTrajetSansChangerSonAuteur(): void
    {
        $id = $this->model->create($this->donneesTrajet());

        $nouvellesDonnees = $this->donneesTrajet([
            'id_agence_depart'      => $this->agenceB,
            'id_agence_arrivee'     => $this->agenceA,
            'nb_places_disponibles' => 1,
        ]);
        unset($nouvellesDonnees['id_employe']);
        $this->model->update($id, $nouvellesDonnees);

        $trajet = $this->lireTrajet($id);
        $this->assertNotFalse($trajet);
        $this->assertSame($this->agenceB, (int) $trajet['id_agence_depart']);
        $this->assertSame($this->agenceA, (int) $trajet['id_agence_arrivee']);
        $this->assertSame(1, (int) $trajet['nb_places_disponibles']);
        $this->assertSame($this->auteurId, (int) $trajet['id_employe']);
    }

    public function testDeleteSupprimeLeTrajet(): void
    {
        $id = $this->model->create($this->donneesTrajet());

        $this->assertTrue($this->model->delete($id));
        $this->assertFalse($this->lireTrajet($id));
    }

    public function testDeleteRetourneFalsePourUnTrajetInexistant(): void
    {
        $this->assertFalse($this->model->delete(999999));
    }

    public function testLaBaseRefuseUnTrajetAvecLaMemeAgenceAuDepartEtALArrivee(): void
    {
        $this->expectException(PDOException::class);

        $this->model->create($this->donneesTrajet(['id_agence_arrivee' => $this->agenceA]));
    }

    public function testLaBaseRefusePlusDePlacesDisponiblesQueDePlacesAuTotal(): void
    {
        $this->expectException(PDOException::class);

        $this->model->create($this->donneesTrajet(['nb_places_total' => 2, 'nb_places_disponibles' => 3]));
    }

    public function testFindDisponiblesExclutLesTrajetsPassesEtComplets(): void
    {
        $idOuvert = $this->model->create($this->donneesTrajet());
        $idComplet = $this->model->create($this->donneesTrajet(['nb_places_disponibles' => 0]));
        $idPasse = $this->model->create($this->donneesTrajet([
            'date_heure_depart'  => date('Y-m-d H:i:s', strtotime('-2 days 08:00')),
            'date_heure_arrivee' => date('Y-m-d H:i:s', strtotime('-2 days 12:00')),
        ]));

        $ids = array_map(static fn (array $t): int => (int) $t['id_trajet'], $this->model->findDisponibles());

        $this->assertContains($idOuvert, $ids);
        $this->assertNotContains($idComplet, $ids);
        $this->assertNotContains($idPasse, $ids);
    }
}
