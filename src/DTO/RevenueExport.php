<?php

namespace App\DTO;

class RevenueExport extends Export
{
    public function __construct(string $path, string $name, public string $companyRevenue)
    {
        parent::__construct($path, $name);
    }
}
