<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        // Domain plans
        Plan::create(['name' => 'Domínio .ao', 'type' => 'domain', 'price' => 25000, 'duration_months' => 12, 'features' => ['tld' => '.ao'], 'active' => true, 'sort_order' => 1]);
        Plan::create(['name' => 'Domínio .com', 'type' => 'domain', 'price' => 15000, 'duration_months' => 12, 'features' => ['tld' => '.com'], 'active' => true, 'sort_order' => 2]);
        Plan::create(['name' => 'Domínio .org', 'type' => 'domain', 'price' => 15000, 'duration_months' => 12, 'features' => ['tld' => '.org'], 'active' => true, 'sort_order' => 3]);

        // Hosting plans
        Plan::create(['name' => 'BASIC', 'type' => 'hosting', 'price' => 45600, 'duration_months' => 12, 'features' => ['storage' => '10 GB SSD', 'bandwidth' => '10 GB', 'emails' => 5, 'databases' => 2], 'active' => true, 'sort_order' => 1]);
        Plan::create(['name' => 'PRO', 'type' => 'hosting', 'price' => 54000, 'duration_months' => 12, 'features' => ['storage' => '50 GB SSD', 'bandwidth' => '20 GB', 'emails' => 30, 'databases' => 5], 'active' => true, 'sort_order' => 2]);
        Plan::create(['name' => 'BUSINESS', 'type' => 'hosting', 'price' => 190992, 'duration_months' => 12, 'features' => ['storage' => '85 GB SSD', 'bandwidth' => 'Ilimitado', 'emails' => 75, 'databases' => 100], 'active' => true, 'sort_order' => 3]);

        // Email plans
        Plan::create(['name' => 'START', 'type' => 'email', 'price' => 8000, 'duration_months' => 12, 'features' => ['storage' => '12 GB', 'accounts' => 1], 'active' => true, 'sort_order' => 1]);
        Plan::create(['name' => 'PRO', 'type' => 'email', 'price' => 18000, 'duration_months' => 12, 'features' => ['storage' => '40 GB', 'accounts' => 5], 'active' => true, 'sort_order' => 2]);
        Plan::create(['name' => 'ENTERPRISE', 'type' => 'email', 'price' => 35000, 'duration_months' => 12, 'features' => ['storage' => '60 GB', 'accounts' => 10], 'active' => true, 'sort_order' => 3]);
    }
}
