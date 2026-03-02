<?php

namespace App\DTO;

class RevenueExport extends Export
{
    public function __construct(string $path, string $name, public string $companyRevenue, public string $rawCompanyRevenue)
    {
        parent::__construct($path, $name);
    }
}
