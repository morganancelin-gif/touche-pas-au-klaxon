<?php

declare(strict_types=1);

namespace Tests\Validators;

use App\Validators\TrajetValidator;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * Tests de la validation des données d'un trajet (avant écriture en base).
 *
 * La date de référence est fixe : les tests donnent le même résultat quel que soit le jour.
 */
final class TrajetValidatorTest extends TestCase
{
    /**
     * Agences considérées comme existantes.
     */
    private const AGENCES = [1, 2, 3];

    private TrajetValidator $validator;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->validator = new TrajetValidator();
    }

    /**
     * Valide une saisie correcte dans laquelle certains champs sont remplacés.
     *
     * @param array<string, string> $remplacements Champs à remplacer dans la saisie valide.
     *
     * @return array<string, int|string>|null Le résultat de TrajetValidator::validate().
     */
    private function valider(array $remplacements = []): ?array
    {
        $saisie = array_merge([
            'agence_depart'         => '1',
            'agence_arrivee'        => '2',
            'depart'                => '2026-06-10T08:00',
            'arrivee'               => '2026-06-10T12:30',
            'nb_places_total'       => '4',
            'nb_places_disponibles' => '3',
        ], $remplacements);

        return $this->validator->validate($saisie, self::AGENCES, new DateTimeImmutable('2026-06-01 12:00'));
    }

    public function testUneSaisieValideEstConvertieAuFormatDeLaBase(): void
    {
        $this->assertSame([
            'id_agence_depart'      => 1,
            'id_agence_arrivee'     => 2,
            'date_heure_depart'     => '2026-06-10 08:00:00',
            'date_heure_arrivee'    => '2026-06-10 12:30:00',
            'nb_places_total'       => 4,
            'nb_places_disponibles' => 3,
        ], $this->valider());
        $this->assertSame([], $this->validator->errors());
    }

    public function testLesAgencesDeDepartEtDArriveeDoiventEtreDifferentes(): void
    {
        $this->assertNull($this->valider(['agence_arrivee' => '1']));
        $this->assertArrayHasKey('agence_arrivee', $this->validator->errors());
    }

    public function testUneAgenceInconnueEstRefusee(): void
    {
        $this->assertNull($this->valider(['agence_depart' => '99']));
        $this->assertArrayHasKey('agence_depart', $this->validator->errors());
    }

    public function testLArriveeDoitEtrePosterieureAuDepart(): void
    {
        $this->assertNull($this->valider(['arrivee' => '2026-06-10T07:00']));
        $this->assertArrayHasKey('arrivee', $this->validator->errors());
    }

    public function testLeDepartDoitEtreDansLeFutur(): void
    {
        $this->assertNull($this->valider(['depart' => '2026-05-01T08:00', 'arrivee' => '2026-05-01T12:00']));
        $this->assertArrayHasKey('depart', $this->validator->errors());
    }

    public function testUneDateInexistanteEstRefusee(): void
    {
        $this->assertNull($this->valider(['depart' => '2027-02-30T08:00']));
        $this->assertArrayHasKey('depart', $this->validator->errors());
    }

    public function testLesPlacesDisponiblesNePeuventPasDepasserLeTotal(): void
    {
        $this->assertNull($this->valider(['nb_places_total' => '2', 'nb_places_disponibles' => '3']));
        $this->assertArrayHasKey('nb_places_disponibles', $this->validator->errors());
    }

    public function testLesNombresDePlacesHorsLimitesSontRefuses(): void
    {
        $this->assertNull($this->valider(['nb_places_total' => '0', 'nb_places_disponibles' => '-1']));
        $this->assertArrayHasKey('nb_places_total', $this->validator->errors());
        $this->assertArrayHasKey('nb_places_disponibles', $this->validator->errors());
    }

    public function testUnFormulaireVideProduitUneErreurParChamp(): void
    {
        $donnees = $this->validator->validate([], self::AGENCES, new DateTimeImmutable('2026-06-01 12:00'));

        $this->assertNull($donnees);
        $this->assertCount(6, $this->validator->errors());
    }
}
