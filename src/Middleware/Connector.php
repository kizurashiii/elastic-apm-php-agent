<?php

namespace PhilKra\Middleware;

use PhilKra\Agent;
use PhilKra\Events\EventBean;
use PhilKra\Stores\TransactionsStore;
use GuzzleHttp\Client;

/**
 *
 * Connector which Transmits the Data to the Endpoints
 *
 */
class Connector
{
    /**
     * Agent Config
     *
     * @var \PhilKra\Helper\Config
     */
    protected $config;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * @var array
     */
    protected $payload = [];

    /**
     * @param \PhilKra\Helper\Config $config
     */
    public function __construct(\PhilKra\Helper\Config $config) {
        $this->config = $config;
        $this->configureHttpClient();
    }

    /**
     * Is the Payload Queue populated?
     *
     * @return bool
     */
    public function isPayloadSet() {
        return (empty($this->payload) === false);
    }

    /**
     * Create and configure the HTTP client
     *
     * @return void
     */
    protected function configureHttpClient() {
        $httpClientDefaults = [
            'timeout' => $this->config->get('timeout'),
        ];

        $httpClientConfig = $this->config->get('httpClient')?:[];

        $this->client = new Client(array_merge($httpClientDefaults, $httpClientConfig));
    }

    /**
     * Put Events to the Payload Queue
     */
    public function putEvent(EventBean $event) {
        $this->payload[] = json_encode($event);
    }

    /**
     * Commit the Events to the APM server
     *
     * @return bool
     */
    public function commit() {
        $body = '';
        foreach($this->payload as $line) {
            $body .= $line . "\n";
        }

        $this->payload = [];
        $response = $this->client->post($this->getEndpoint(), [
            'headers' => $this->getRequestHeaders(),
            'body'    => $body,
        ]);
        return ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300);
    }

    /**
     * Get the Payload
     *
     *
     * @return Arrays
     */
    public function getPayload() {
        return $this->payload;
    }

    /**
     * Set the Payload
     *
     *
     */
    public function setPayload( $payload = array() ) {
        $this->payload = $payload;
    }

    /**
     * Get the Server Informations
     *
     * @link https://www.elastic.co/guide/en/apm/server/7.3/server-info.html
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getInfo() {
        return $this->client->get(
            $this->config->get('serverUrl'),
            ['headers' => $this->getRequestHeaders(),]
        );
    }

    /**
     * Get the Endpoint URI of the APM Server
     *
     * @param $endpoint
     *
     * @return string
     */
    protected function getEndpoint() {
        return sprintf('%s/intake/v2/events', $this->config->get('serverUrl'));
    }

    /**
     * Get the Headers for the POST Request
     *
     * @return array
     */
    protected function getRequestHeaders() {
        // Default Headers Set
        $headers = [
            'Content-Type'     => 'application/x-ndjson',
            'User-Agent'       => sprintf('elasticapm-php/%s', Agent::VERSION),
            'Accept'           => 'application/json',
        ];

        // Add Secret Token to Header
        if ($this->config->get('secretToken') !== null) {
            $headers['Authorization'] = sprintf('Bearer %s', $this->config->get('secretToken'));
        }

        return $headers;
    }

}
