<?php

declare(strict_types=1);

namespace Tests\Validators;

use App\Validators\AgenceValidator;
use PHPUnit\Framework\TestCase;

/**
 * Tests de la validation du nom d'une agence (avant écriture en base).
 */
final class AgenceValidatorTest extends TestCase
{
    private AgenceValidator $validator;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->validator = new AgenceValidator();
    }

    public function testUnNomValideEstAccepte(): void
    {
        $this->assertSame('Bordeaux', $this->validator->validate('Bordeaux'));
        $this->assertNull($this->validator->error());
    }

    public function testLeNomEstNormalise(): void
    {
        $this->assertSame('Saint-Étienne', $this->validator->validate('  saint-Étienne  '));
        $this->assertSame('Aix en Provence', $this->validator->validate('Aix   en  Provence'));
    }

    public function testLesApostrophesEtLesTiretsSontAcceptes(): void
    {
        $this->assertSame("L'Isle-d'Abeau", $this->validator->validate("L'Isle-d'Abeau"));
    }

    public function testUnNomVideEstRefuse(): void
    {
        $this->assertNull($this->validator->validate('   '));
        $this->assertNotNull($this->validator->error());
    }

    public function testLesChiffresEtLesBalisesSontRefuses(): void
    {
        $this->assertNull($this->validator->validate('Lyon 2'));
        $this->assertNull($this->validator->validate('<script>'));
    }

    public function testUnNomTropLongEstRefuse(): void
    {
        $this->assertNull($this->validator->validate(str_repeat('a', AgenceValidator::LONGUEUR_MAX + 1)));
    }
}
