<?php

namespace App\DTO;

class Export
{
    public function __construct(public string $path, public string $name)
    {
    }

}
