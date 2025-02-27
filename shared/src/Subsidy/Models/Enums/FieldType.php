<?php

declare(strict_types=1);

namespace MinVWS\DUSi\Shared\Subsidy\Models\Enums;

enum FieldType: string
{
    case Text = 'text';
    case TextNumeric = 'text:numeric';
    case TextFloat = 'text:float';
    case TextEmail = 'text:email';
    case TextTel = 'text:tel';
    case TextUrl = 'text:url';
    case Checkbox = 'checkbox';
    case Multiselect = 'multiselect';
    case Select = 'select';
    case TextArea = 'textarea';
    case Upload = 'upload';
    case Date = 'date';
    case CustomPostalCode = 'custom:postalcode';
    case CustomCountry = 'custom:country';
    case CustomBankAccount = 'custom:bankaccount';
}
