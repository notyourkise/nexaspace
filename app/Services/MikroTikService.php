<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use RouterOS\Client;
use RouterOS\Config;
use RouterOS\Query;
use Throwable;

class MikroTikService
{
    private function connect(): Client
    {
        $config = (new Config())
            ->set('host', config('services.mikrotik.host'))
            ->set('user', config('services.mikrotik.user'))
            ->set('pass', config('services.mikrotik.pass'))
            ->set('port', (int) config('services.mikrotik.port', 8728))
            ->set('timeout', 5);

        return new Client($config);
    }

    /**
     * Find a DHCP lease entry by MAC address.
     * Returns the lease array (including '.id') or null if not found.
     *
     * @return array<string, string>|null
     */
    public function findLeaseByMac(string $mac): ?array
    {
        try {
            $client = $this->connect();

            $query = (new Query('/ip/dhcp-server/lease/print'))
                ->where('mac-address', strtoupper($mac));

            $leases = $client->query($query)->read();

            return $leases[0] ?? null;
        } catch (Throwable $e) {
            Log::error('[MikroTik] findLeaseByMac failed', [
                'mac' => $mac,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Set rate-limit=256k/256k on the DHCP lease for the given MAC.
     * Silently logs and returns false if the router is unreachable or lease not found.
     */
    public function throttleDevice(string $mac): bool
    {
        try {
            $lease = $this->findLeaseByMac($mac);

            if ($lease === null) {
                Log::warning('[MikroTik] throttleDevice: lease not found', ['mac' => $mac]);
                return false;
            }

            $client = $this->connect();

            $query = (new Query('/ip/dhcp-server/lease/set'))
                ->equal('.id', $lease['.id'])
                ->equal('rate-limit', '256k/256k');

            $client->query($query)->read();

            Log::info('[MikroTik] Throttled device', ['mac' => $mac, 'lease_id' => $lease['.id']]);

            return true;
        } catch (Throwable $e) {
            Log::error('[MikroTik] throttleDevice failed', [
                'mac' => $mac,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Clear the rate-limit on the DHCP lease for the given MAC (restore full speed).
     * Silently logs and returns false if the router is unreachable or lease not found.
     */
    public function unthrottleDevice(string $mac): bool
    {
        try {
            $lease = $this->findLeaseByMac($mac);

            if ($lease === null) {
                Log::warning('[MikroTik] unthrottleDevice: lease not found', ['mac' => $mac]);
                return false;
            }

            $client = $this->connect();

            $query = (new Query('/ip/dhcp-server/lease/set'))
                ->equal('.id', $lease['.id'])
                ->equal('rate-limit', '');

            $client->query($query)->read();

            Log::info('[MikroTik] Unthrottled device', ['mac' => $mac, 'lease_id' => $lease['.id']]);

            return true;
        } catch (Throwable $e) {
            Log::error('[MikroTik] unthrottleDevice failed', [
                'mac' => $mac,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
