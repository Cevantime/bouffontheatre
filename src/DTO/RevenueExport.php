<?php

namespace App\DTO;

class RevenueExport extends Export
{
    public function __construct(string $path, string $name, public string $companyRevenue, public string $rawCompanyRevenue, public string $notaxRevenue, public string $netRevenue)
    {
        parent::__construct($path, $name);
    }
}
