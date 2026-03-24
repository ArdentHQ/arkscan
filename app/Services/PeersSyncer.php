<?php

declare(strict_types=1);

namespace App\Services;

use App\Facades\Network;
use App\Models\Peer;
use Illuminate\Support\Facades\Http;
use Torann\GeoIP\Location;

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
                'country'   => $location['country'],
                'city'      => $location['city'],
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
     * @return array{lat: float|null, lon: float|null, country: string|null, city: string|null}
     */
    private function resolveLocation(string $ip): array
    {
        $default = ['lat' => null, 'lon' => null, 'country' => null, 'city' => null];

        try {
            /** @var Location $location */
            $location = geoip($ip);

            if ($location->default) {
                return $default;
            }

            return [
                'lat'     => $location->lat,
                'lon'     => $location->lon,
                'country' => $location->country ?? null,
                'city'    => $location->city ?? null,
            ];
        } catch (\Throwable) {
            return $default;
        }
    }
}
