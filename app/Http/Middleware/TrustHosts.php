<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustHosts as Middleware;

class TrustHosts extends Middleware
{
    /**
     * Get the host patterns that should be trusted.
     *
     * @return array<int, string|null>
     */
    public function hosts(): array
    {
        return [
            // 'localhost',
            // '127.0.0.1',
            'dominio.it',
            $this->allSubdomainsOfApplicationUrl(),
        ];
    }
}
