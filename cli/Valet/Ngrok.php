<?php

namespace Valet;

use DomainException;
use Exception;
use Httpful\Request;

class Ngrok
{
    public $tunnelsEndpoint = 'http://127.0.0.1:4040/api/tunnels';
    public $cli;

    /**
     * Create a new Ngrok instance.
     *
     * @param CommandLine $cli
     *
     * @return void
     */
    public function __construct(CommandLine $cli)
    {
        $this->cli = $cli;
    }

    /**
     * Get the current tunnel URL from the Ngrok API.
     *
     * @throws Exception
     *
     * @return string
     */
    public function currentTunnelUrl()
    {
        return retry(20, function () {
            $body = Request::get($this->tunnelsEndpoint)->send()->body;

            // If there are active tunnels on the Ngrok instance we will spin through them and
            // find the one responding on HTTP. Each tunnel has an HTTP and a HTTPS address
            // but for local testing purposes we just desire the plain HTTP URL endpoint.
            if (isset($body->tunnels) && count($body->tunnels) > 0) {
                return $this->findHttpTunnelUrl($body->tunnels);
            } else {
                throw new DomainException('Tunnel not established.');
            }
        }, 250);
    }

    /**
     * Find the HTTP tunnel URL from the list of tunnels.
     *
     * @param array $tunnels
     *
     * @return string|null
     */
    public function findHttpTunnelUrl(array $tunnels)
    {
        foreach ($tunnels as $tunnel) {
            if ($tunnel->proto === 'http') {
                return $tunnel->public_url;
            }
        }

        return null;
    }

    /**
     * Find the HTTP tunnel URL from the list of tunnels.
     *
     * @param string $authToken
     *
     * @return void
     */
    public function setAuthToken(string $authToken)
    {
        $this->cli->run(__DIR__.'/../../bin/ngrok config add-authtoken '.$authToken);
        info('Ngrok authentication token set.');
    }
}
