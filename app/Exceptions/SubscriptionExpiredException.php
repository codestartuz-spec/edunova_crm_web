<?php

namespace App\Exceptions;

use RuntimeException;

/** v22: obunasi tugagan filialda yozish urinishi (faqat-ko'rish rejimi). */
class SubscriptionExpiredException extends RuntimeException
{
    public function __construct(string $message = "Filial obunasi tugagan: faqat ko'rish mumkin. Obunani uzaytiring.")
    {
        parent::__construct($message);
    }
}
