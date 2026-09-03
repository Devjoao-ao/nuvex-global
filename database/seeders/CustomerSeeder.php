<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Service;
use App\Models\Domain;
use App\Models\Hosting;
use App\Models\EmailService;
use App\Models\EmailAccount;
use App\Models\ServiceCredential;
use App\Models\Order;
use App\Models\Invoice;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            ['name' => 'Maria Santos', 'email' => 'maria@empresa.ao', 'phone' => '+244 923 456 789'],
            ['name' => 'Pedro Costa', 'email' => 'pedro@startup.ao', 'phone' => '+244 912 345 678'],
            ['name' => 'Ana Ferreira', 'email' => 'ana@loja.ao', 'phone' => '+244 934 567 890'],
            ['name' => 'Carlos Mendes', 'email' => 'carlos@tech.ao', 'phone' => '+244 945 678 901'],
            ['name' => 'Lucia Neto', 'email' => 'lucia@media.ao', 'phone' => '+244 956 789 012'],
            ['name' => 'Ricardo Alves', 'email' => 'ricardo@grupo.ao', 'phone' => '+244 967 890 123'],
            ['name' => 'Sofia Pereira', 'email' => 'sofia@creative.ao', 'phone' => '+244 978 901 234'],
            ['name' => 'Manuel Tavares', 'email' => 'manuel@construcao.ao', 'phone' => '+244 989 012 345'],
        ];

        foreach ($customers as $i => $data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'role' => 'customer',
                'active' => true,
                'password' => Hash::make('password'),
            ]);

            // Create order + invoice for the customer
            $order = Order::create([
                'number' => 'ORD-2026-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'user_id' => $user->id,
                'subtotal' => 100000,
                'total' => 100000,
                'status' => 'completed',
                'payment_method' => 'reference',
                'paid_at' => now()->subDays(30),
                'completed_at' => now()->subDays(30),
            ]);

            Invoice::create([
                'number' => 'INV-2026-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'order_id' => $order->id,
                'user_id' => $user->id,
                'amount' => 100000,
                'description' => 'Serviços NUVEX',
                'status' => 'paid',
                'issue_date' => now()->subDays(30)->toDateString(),
                'method' => 'Referência Multicaixa',
            ]);
        }
    }
}
