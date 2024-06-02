<?php

declare(strict_types=1);

namespace App\Exception;

use LogicException;

class CurrencyPairNotFoundException extends LogicException
{
    public $message = 'Currency pair not found';
}
