<?php

namespace App\DTO;

use Ramsey\Uuid\Uuid;

class GetProductDTO
{
    public function __construct(
        public string $value,
        public string $label,
    ) {}
}
