<?php

namespace App\Services;

use App\Models\Plan;

class PlanService
{
    public function getActiveByType(string $type)
    {
        return Plan::where('type', $type)
            ->where('active', true)
            ->orderBy('price')
            ->get();
    }

    public function createPlan(array $data): Plan
    {
        return Plan::create([
            'name' => $data['name'],
            'slug' => $data['slug'] ?? \Illuminate\Support\Str::slug($data['name']),
            'type' => $data['type'],
            'price' => $data['price'],
            'currency' => $data['currency'] ?? 'AOA',
            'billing_cycle' => $data['billing_cycle'] ?? 'yearly',
            'features' => $data['features'] ?? [],
            'description' => $data['description'] ?? null,
            'active' => $data['active'] ?? true,
        ]);
    }

    public function updatePlan(Plan $plan, array $data): Plan
    {
        $plan->update(array_filter([
            'name' => $data['name'] ?? null,
            'slug' => isset($data['name']) ? \Illuminate\Support\Str::slug($data['name']) : ($data['slug'] ?? null),
            'type' => $data['type'] ?? null,
            'price' => $data['price'] ?? null,
            'currency' => $data['currency'] ?? null,
            'billing_cycle' => $data['billing_cycle'] ?? null,
            'features' => $data['features'] ?? null,
            'description' => $data['description'] ?? null,
            'active' => $data['active'] ?? null,
        ], fn ($v) => $v !== null));

        return $plan->fresh();
    }

    public function toggleActive(Plan $plan): Plan
    {
        $plan->update(['active' => !$plan->active]);

        return $plan->fresh();
    }

    public function getPrices()
    {
        return Plan::where('active', true)
            ->select('id', 'name', 'slug', 'type', 'price', 'currency', 'billing_cycle')
            ->get()
            ->groupBy('type');
    }
}
