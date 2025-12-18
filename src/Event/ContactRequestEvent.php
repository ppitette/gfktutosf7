<?php

namespace App\Event;

use App\Dto\contactDto;

class ContactRequestEvent
{
    public function __construct(
        public readonly contactDto $data,
    ) {
    }
}
