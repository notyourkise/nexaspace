<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use RouterOS\Client;
use RouterOS\Config;
use RouterOS\Query;
use Throwable;

class MikroTikService
{
    private readonly string $host;
    private readonly int    $port;
    private readonly string $user;
    private readonly string $pass;

    public function __construct(
        ?string $host = null,
        ?int    $port = null,
        ?string $user = null,
        ?string $pass = null,
    ) {
        // Fall back to global .env config when per-juragan values are absent.
        $this->host = $host ?? (string) config('services.mikrotik.host', '');
        $this->port = $port ?? (int)   config('services.mikrotik.port', 8728);
        $this->user = $user ?? (string) config('services.mikrotik.user', 'admin');
        $this->pass = $pass ?? (string) config('services.mikrotik.pass', '');
    }

    /**
     * Return a MikroTikService instance configured for the given juragan's router.
     * Falls back to global .env values when a juragan has no per-router config set.
     */
    public static function forJuragan(User $juragan): static
    {
        return new static(
            host: $juragan->mikrotik_host ?: null,
            port: $juragan->mikrotik_port ?: null,
            user: $juragan->mikrotik_user ?: null,
            pass: $juragan->mikrotik_pass ?: null,
        );
    }

    private function connect(): Client
    {
        $config = (new Config())
            ->set('host', $this->host)
            ->set('user', $this->user)
            ->set('pass', $this->pass)
            ->set('port', $this->port)
            ->set('timeout', 5);

        return new Client($config);
    }

    /**
     * Test whether the router is reachable with the configured credentials.
     * Result is cached for 5 minutes to avoid hitting the router on every page load.
     */
    public function isConnected(): bool
    {
        $cacheKey = 'mikrotik.is_connected.' . md5($this->host . ':' . $this->port);

        return Cache::remember($cacheKey, 300, function (): bool {
            try {
                $this->connect();
                return true;
            } catch (Throwable) {
                return false;
            }
        });
    }

    /**
     * Whether the router host is configured (non-empty).
     */
    public function isConfigured(): bool
    {
        return $this->host !== '';
    }

    /**
     * Connection details for display (host, port, user).
     *
     * @return array{host: string, port: int, user: string}
     */
    public function connectionInfo(): array
    {
        return ['host' => $this->host, 'port' => $this->port, 'user' => $this->user];
    }

    /**
     * Return all DHCP leases from the router.
     *
     * @return array<int, array<string, string>>
     */
    public function getLeases(): array
    {
        try {
            $client = $this->connect();
            $query  = new Query('/ip/dhcp-server/lease/print');

            return $client->query($query)->read();
        } catch (Throwable $e) {
            Log::error('[MikroTik] getLeases failed', ['host' => $this->host, 'error' => $e->getMessage()]);

            return [];
        }
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
                'host' => $this->host,
                'mac'  => $mac,
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
                Log::warning('[MikroTik] throttleDevice: lease not found', ['host' => $this->host, 'mac' => $mac]);
                return false;
            }

            $client = $this->connect();

            $query = (new Query('/ip/dhcp-server/lease/set'))
                ->equal('.id', $lease['.id'])
                ->equal('rate-limit', '256k/256k');

            $client->query($query)->read();

            Log::info('[MikroTik] Throttled device', ['host' => $this->host, 'mac' => $mac, 'lease_id' => $lease['.id']]);

            return true;
        } catch (Throwable $e) {
            Log::error('[MikroTik] throttleDevice failed', [
                'host'  => $this->host,
                'mac'   => $mac,
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
                Log::warning('[MikroTik] unthrottleDevice: lease not found', ['host' => $this->host, 'mac' => $mac]);
                return false;
            }

            $client = $this->connect();

            $query = (new Query('/ip/dhcp-server/lease/set'))
                ->equal('.id', $lease['.id'])
                ->equal('rate-limit', '');

            $client->query($query)->read();

            Log::info('[MikroTik] Unthrottled device', ['host' => $this->host, 'mac' => $mac, 'lease_id' => $lease['.id']]);

            return true;
        } catch (Throwable $e) {
            Log::error('[MikroTik] unthrottleDevice failed', [
                'host'  => $this->host,
                'mac'   => $mac,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
