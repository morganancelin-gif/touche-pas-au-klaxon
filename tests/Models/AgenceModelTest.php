<?php

declare(strict_types=1);

namespace Tests\Models;

use App\Models\AgenceModel;
use App\Models\TrajetModel;
use PDOException;
use Tests\DatabaseTestCase;

/**
 * Tests des opérations d'écriture sur la table agence.
 */
final class AgenceModelTest extends DatabaseTestCase
{
    private AgenceModel $model;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->model = new AgenceModel($this->pdo);
    }

    public function testCreateEnregistreLAgence(): void
    {
        $id = $this->model->create('Agence Test Gamma');

        $agence = $this->model->findById($id);
        $this->assertNotNull($agence);
        $this->assertSame('Agence Test Gamma', $agence['nom']);
    }

    public function testLaBaseRefuseDeuxAgencesDeMemeNom(): void
    {
        $this->model->create('Agence Test Gamma');

        $this->expectException(PDOException::class);
        $this->model->create('Agence Test Gamma');
    }

    public function testUpdateRenommeLAgence(): void
    {
        $id = $this->model->create('Agence Test Gamma');

        $this->model->update($id, 'Agence Test Delta');

        $agence = $this->model->findById($id);
        $this->assertNotNull($agence);
        $this->assertSame('Agence Test Delta', $agence['nom']);
    }

    public function testDeleteSupprimeUneAgenceInutilisee(): void
    {
        $id = $this->model->create('Agence Test Gamma');

        $this->assertTrue($this->model->delete($id));
        $this->assertNull($this->model->findById($id));
    }

    public function testLaBaseRefuseDeSupprimerUneAgenceUtiliseeParUnTrajet(): void
    {
        $agenceDepart = $this->model->create('Agence Test Gamma');
        $agenceArrivee = $this->model->create('Agence Test Delta');
        (new TrajetModel($this->pdo))->create([
            'id_agence_depart'      => $agenceDepart,
            'id_agence_arrivee'     => $agenceArrivee,
            'date_heure_depart'     => date('Y-m-d H:i:s', strtotime('+2 days 08:00')),
            'date_heure_arrivee'    => date('Y-m-d H:i:s', strtotime('+2 days 12:00')),
            'nb_places_total'       => 4,
            'nb_places_disponibles' => 3,
            'id_employe'            => $this->creerEmploye(),
        ]);

        $this->assertSame(1, $this->model->countTrajets($agenceDepart));

        $this->expectException(PDOException::class);
        $this->model->delete($agenceDepart);
    }

    public function testNomExisteIgnoreLaCasseEtLesAccents(): void
    {
        $this->model->create('Agence Test Gamma');

        $this->assertTrue($this->model->nomExiste('agence test gamma'));
        $this->assertTrue($this->model->nomExiste('Agence Test Gàmma'));
        $this->assertFalse($this->model->nomExiste('Agence Test Inconnue'));
    }

    public function testNomExisteIgnoreLAgenceEnCoursDeModification(): void
    {
        $id = $this->model->create('Agence Test Gamma');

        $this->assertFalse($this->model->nomExiste('Agence Test Gamma', $id));
    }
}
