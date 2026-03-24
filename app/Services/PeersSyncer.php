<?php

declare(strict_types=1);

namespace App\Services;

use App\Facades\Network;
use App\Models\Peer;
use Illuminate\Support\Facades\Http;
use Torann\GeoIP\Facades\GeoIP;

final class PeersSyncer
{
    public function sync(): int
    {
        $peers = $this->fetchPeers();

        if ($peers === []) {
            return 0;
        }

        $existingIps = Peer::pluck('ip')->toArray();
        $apiIps      = array_column($peers, 'ip');

        Peer::whereNotIn('ip', $apiIps)->delete();

        $newPeers = array_filter($peers, fn (array $peer) => ! in_array($peer['ip'], $existingIps, true));

        foreach ($newPeers as $peerData) {
            $location = $this->resolveLocation($peerData['ip']);

            Peer::create([
                'ip'        => $peerData['ip'],
                'port'      => $peerData['port'],
                'latitude'  => $location['lat'],
                'longitude' => $location['lon'],
            ]);
        }

        return count($newPeers);
    }

    /**
     * @return array<int, array{ip: string, port: int}>
     */
    private function fetchPeers(): array
    {
        try {
            $response = Http::get(sprintf('%s/peers', Network::api()))->json();

            return $response['data'] ?? [];
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * @return array{lat: float|null, lon: float|null}
     */
    private function resolveLocation(string $ip): array
    {
        try {
            $location = GeoIP::getLocation($ip);

            if ($location->default) {
                return ['lat' => null, 'lon' => null];
            }

            return [
                'lat' => $location->lat,
                'lon' => $location->lon,
            ];
        } catch (\Throwable) {
            return ['lat' => null, 'lon' => null];
        }
    }
}
