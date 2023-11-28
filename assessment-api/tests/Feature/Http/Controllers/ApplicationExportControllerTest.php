<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Assessment\API\Tests\Feature\Http\Controllers;

use Carbon\Carbon;
use Faker\Generator;
use Illuminate\Container\Container;
use MinVWS\DUSi\Assessment\API\Tests\TestCase;
use MinVWS\DUSi\Shared\Application\Models\Answer;
use MinVWS\DUSi\Shared\Application\Models\Application;
use MinVWS\DUSi\Shared\Application\Models\ApplicationStage;
use MinVWS\DUSi\Shared\Application\Models\ApplicationSurePayResult;
use MinVWS\DUSi\Shared\Application\Models\Identity;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\SubjectRole;
use MinVWS\DUSi\Shared\Subsidy\Models\Enums\VersionStatus;
use MinVWS\DUSi\Shared\Subsidy\Models\Field;
use MinVWS\DUSi\Shared\Subsidy\Models\Subsidy;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyStage;
use MinVWS\DUSi\Shared\Subsidy\Models\SubsidyVersion;
use MinVWS\DUSi\Shared\Test\MocksEncryption;
use MinVWS\DUSi\Shared\User\Enums\Role;
use MinVWS\DUSi\Shared\User\Enums\Role as RoleEnum;
use MinVWS\DUSi\Shared\User\Models\User;

/**
 * @group application-assessor
 * @group application-assessor-controller
 */
class ApplicationExportControllerTest extends TestCase
{
    use MocksEncryption;

    private Subsidy $subsidy;
    private SubsidyStage $subsidyStage1;
    private Application $application;

    /**
     * @psalm-suppress InvalidPropertyAssignmentValue
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->subsidy = Subsidy::factory()->create(['code' => 'PCZM']);
        $subsidyVersion = SubsidyVersion::factory()
            ->for($this->subsidy)
            ->create(['status' => VersionStatus::Published]);
        $subsidyStage1 = SubsidyStage::factory()->for($subsidyVersion)->create([
            'stage' => 1,
            'subject_role' => SubjectRole::Applicant,
            'assessor_user_role' => RoleEnum::Assessor,
        ]);
        $subsidyStage2 = SubsidyStage::factory()->for($subsidyVersion)->create([
            'stage' => 2,
            'subject_role' => SubjectRole::Assessor,
            'assessor_user_role' => RoleEnum::Assessor,
        ]);

        $identity = Identity::factory()->create();

        $faker = Container::getInstance()->make(Generator::class);

        Application::factory()
            ->for($identity)
            ->for($subsidyVersion)
            ->count(10)
            ->create(
                [
                    'updated_at' => Carbon::today(),
                    'created_at' => Carbon::today(),
                    'final_review_deadline' => Carbon::tomorrow(),
                    'status' => 'approved',

                ]
            )
            ->each(function (Application $application) use ($subsidyStage1, $subsidyStage2, $faker) {
                ApplicationSurePayResult::factory()
                    ->for($application)
                    ->create()
                ;

                $appStage1 = ApplicationStage::factory()->for($application)->for($subsidyStage1)
                    ->create(['is_current' => false, 'is_submitted' => true, 'submitted_at' => Carbon::now()]);
                ApplicationStage::factory()->for($application)->for($subsidyStage2)
                    ->create(['is_current' => true, 'sequence_number' => 2]);

                $applicationFields = [
                    'firstName' => 'firstName',
                    'infix' => null,
                    'lastName' => 'lastName',
                    'street' => 'streetName',
                    'houseNumber' => 'buildingNumber',
                    'houseNumberSuffix' => 'randomLetter',
                    'postalCode' => null,
                    'city' => 'city',
                    'phoneNumber' => 'e164PhoneNumber',
                    'email' => 'email',
                    'bankAccountHolder' => 'name',
                    'bankAccountNumber' => null,
                ];

                foreach ($applicationFields as $fieldCode => $fakerFunction) {
                    $field = Field::factory()
                        ->for($subsidyStage1)
                        ->create([
                            'code' => $fieldCode,
                            'title' => $fieldCode
                        ]);

                    if ($fakerFunction) {
                        $encryptedAnswer = $faker->{$fakerFunction};
                    } elseif ($fieldCode === 'postalCode') {
                        $encryptedAnswer = sprintf(
                            '1234%s%s',
                            strtoupper($faker->randomLetter()),
                            strtoupper($faker->randomLetter())
                        );
                    } elseif ($fieldCode === 'bankAccountNumber') {
                        $encryptedAnswer = 'NL62ABNA9999841479';
                    } else {
                        $encryptedAnswer = '';
                    }

                    Answer::factory()->create([
                        'application_stage_id' => $appStage1->id,
                        'field_id' => $field->id,
                        'encrypted_answer' => $encryptedAnswer,
                    ]);
                }
            })
        ;
    }

    public function testExportApplications(): void
    {
        $user = User::factory()->create();
        $user->attachRole(RoleEnum::DataExporter, $this->subsidy->id);

        $firstApplication = Application::orderBy('created_at')->first();
        $currentTime = Carbon::now();
        Carbon::setTestNow($currentTime);

        $response = $this
            ->be($user)
            ->json('GET', '/api/export/applications')
        ;

        $response->assertStatus(200);

        $responseFilename = sprintf('attachment; filename=export-%s.csv', $currentTime->format('Y-m-d-His'));
        $response->assertHeader('Content-Disposition', $responseFilename);

        $content = $response->streamedContent();
        $content = str_replace("\xEF\xBB\xBF",'', $content);
        $rows = explode("\n", $content);

        $headerRow = str_getcsv($rows[0]);
        $this->assertContains("Dossiernummer", $headerRow);
        $this->assertContains("Voornaam", $headerRow);
        $this->assertContains("Tussenvoegsel + Achternaam", $headerRow);
        $this->assertContains("Straatnaam", $headerRow);
        $this->assertContains("Huisnummer + HuisnummerToevoeging", $headerRow);
        $this->assertContains("Postcode", $headerRow);
        $this->assertContains("Woonplaats", $headerRow);
        $this->assertContains("NaamRekeninghouder", $headerRow);
        $this->assertContains("IBAN", $headerRow);
        $this->assertContains("SurePay resultaat IBAN-Bestaan", $headerRow);
        $this->assertContains("SurePay resultaat Naam-IBAN", $headerRow);
        $this->assertContains("SurePay resultaat AccountType (organisatie/persoon)", $headerRow);
        $this->assertContains("SurePay resultaat Actief/inactief", $headerRow);

        $firstRow = str_getcsv($rows[1]);
        assert($firstApplication instanceof Application);

        $this->assertEquals($firstApplication->reference, $firstRow[0]);

        $applicationSurePayResult = $firstApplication->applicationSurePayResult;
        $this->assertEquals($applicationSurePayResult->account_number_validation->value, $firstRow[9]);
    }

    public function testWithWrongRole(): void
    {
        $user = User::factory()->create();
        $user->attachRole(RoleEnum::ImplementationCoordinator, $this->subsidy->id);

        $response = $this
            ->be($user)
            ->json('GET', '/api/export/applications')
        ;

        $response->assertStatus(403);
    }

    public function testWithWrongSubsidy(): void
    {
        $user = User::factory()->create();
        $otherSubsidy = Subsidy::factory()->create(['code' => 'TEST']);
        $user->attachRole(RoleEnum::DataExporter, $otherSubsidy->id);

        $response = $this
            ->be($user)
            ->json('GET', '/api/export/applications')
        ;

        $response->assertStatus(403);
    }
}
