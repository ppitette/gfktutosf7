<?php

namespace App\Dto;

class CategoryWithCountDto
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly int $count,
    ) {
    }
}
