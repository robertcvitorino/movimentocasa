<?php

namespace App\Services\Integrations;

use Illuminate\Http\Client\Factory as HttpFactory;

class ViaCepService
{
    public function __construct(
        private readonly HttpFactory $http,
    ) {
    }

    public function lookup(string $zipCode): ?array
    {
        $normalizedZipCode = preg_replace('/\D+/', '', $zipCode);

        if (blank($normalizedZipCode) || strlen($normalizedZipCode) !== 8) {
            return null;
        }

        $response = $this->http->baseUrl('https://viacep.com.br/ws')
            ->acceptJson()
            ->get(sprintf('/%s/json/', $normalizedZipCode))
            ->throw()
            ->json();

        if (($response['erro'] ?? false) === true) {
            return null;
        }

        return [
            'zip_code' => $response['cep'] ?? null,
            'street' => $response['logradouro'] ?? null,
            'complement' => $response['complemento'] ?? null,
            'district' => $response['bairro'] ?? null,
            'city' => $response['localidade'] ?? null,
            'state' => $response['uf'] ?? null,
        ];
    }
}
