<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Application\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MinVWS\DUSi\Shared\Application\Database\Factories\ApplicationSurePayResultFactory;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\Enums\AccountNumberValidation;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\Enums\AccountStatus;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\Enums\AccountType;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\Enums\NameMatchResult;
use MinVWS\DUSi\Shared\Application\Repositories\SurePay\DTO\Enums\PaymentPreValidation;

/**
 * @property string $application_id
 * @property AccountNumberValidation $account_number_validation
 * @property NameMatchResult|null $name_match_result
 * @property PaymentPreValidation|null $payment_pre_validation
 * @property AccountStatus|null $status
 * @property AccountType|null $account_type
 * @property bool|null $joint_account
 * @property int|null $number_of_account_holders
 * @property string|null $country_code
 * @property string|null $encrypted_name_suggestion
 * @property DateTimeInterface $created_at
 * @property DateTimeInterface $updated_at
 */
class ApplicationSurePayResult extends Model
{
    use HasFactory;
    use HasUuids;

    protected $table = 'application_surepay_results';
    protected $primaryKey = 'application_id';
    protected $connection = Connection::APPLICATION;
    protected $fillable = ['application_id'];
    protected $casts = [
        'name_match_result' => NameMatchResult::class,
        'account_number_validation' => AccountNumberValidation::class,
        'payment_pre_validation' => PaymentPreValidation::class,
        'status' => AccountStatus::class,
        'account_type' => AccountType::class,
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_id', 'id');
    }

    protected static function newFactory(): ApplicationSurePayResultFactory
    {
        return ApplicationSurePayResultFactory::new();
    }
}
