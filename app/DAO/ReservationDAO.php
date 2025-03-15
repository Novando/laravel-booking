<?php

namespace App\DAO;

use Carbon\Carbon;

class ReservationDAO
{
    public function __construct(
        public Carbon $date,
        public int $count,
    ) {}
}
