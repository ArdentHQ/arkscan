<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia;

use App\DTO\Inertia\Peer as PeerDTO;
use App\Models\Peer;
use Inertia\Inertia;
use Inertia\Response;

final class NetworkMapController
{
    public function __invoke(): Response
    {
        $peers = Peer::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(fn (Peer $peer) => PeerDTO::fromModel($peer))
            ->toArray();

        return Inertia::renderWithMeta('NetworkMap/Index', 'network-map', [
            'peers' => $peers,
        ]);
    }
}
