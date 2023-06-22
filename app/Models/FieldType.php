<?php
declare(strict_types=1);

namespace App\Models;

enum FieldType: string
{
    case Text = 'text';
    case TextNumeric = 'text:numeric';
    case TextEmail = 'text:email';
    Case TextTel ='text:tel';
    case TextUrl = 'text:url';
    case Checkbox = 'checkbox';
    case Select = 'select';
    case TextArea = 'textarea';
    case Upload = 'upload';
    case CustomPostalCode = 'custom:postalcode';
    case CustomCountry = 'custom:country';
    case CustomBankAccount = 'custom:bankaccount';
}
