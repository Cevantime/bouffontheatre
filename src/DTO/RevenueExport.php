<?php

namespace App\DTO;

class RevenueExport extends Export
{
    public function __construct(string $path, string $name, public string $companyRevenue, public float $rawCompanyRevenue)
    {
        parent::__construct($path, $name);
    }
}
