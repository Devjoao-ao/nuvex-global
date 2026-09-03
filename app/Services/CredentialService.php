<?php

namespace App\Services;

use App\Models\Service;
use App\Models\ServiceCredential;
use Illuminate\Support\Facades\Crypt;

class CredentialService
{
    public function addCredential(Service $service, array $data): ServiceCredential
    {
        return ServiceCredential::create([
            'service_id' => $service->id,
            'label' => $data['label'],
            'username' => $data['username'],
            'password' => Crypt::encryptString($data['password']),
            'type' => $data['type'] ?? 'password',
            'url' => $data['url'] ?? null,
            'notes' => $data['notes'] ?? null,
            'visible_to_user' => $data['visible_to_user'] ?? true,
        ]);
    }

    public function updateCredential(ServiceCredential $credential, array $data): ServiceCredential
    {
        $updates = array_filter([
            'label' => $data['label'] ?? null,
            'username' => $data['username'] ?? null,
            'password' => isset($data['password']) ? Crypt::encryptString($data['password']) : null,
            'type' => $data['type'] ?? null,
            'url' => $data['url'] ?? null,
            'notes' => $data['notes'] ?? null,
            'visible_to_user' => $data['visible_to_user'] ?? null,
        ], fn ($v) => $v !== null);

        $credential->update($updates);

        return $credential->fresh();
    }

    public function deleteCredential(ServiceCredential $credential): bool
    {
        return $credential->delete();
    }

    public function getVisibleCredentials(Service $service)
    {
        return ServiceCredential::where('service_id', $service->id)
            ->where('visible_to_user', true)
            ->get();
    }

    public function getCredentialDecrypted(ServiceCredential $credential): array
    {
        $credential->password = Crypt::decryptString($credential->password);
        return $credential->toArray();
    }
}
