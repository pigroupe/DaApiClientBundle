<?php

/**
 * This file is part of the Da Project.
 *
 * (c) Thomas Prelot <tprelot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Da\ApiClientBundle\Http\Transport;

use Da\ApiClientBundle\Logger\HttpLoggerInterface;
use Da\ApiClientBundle\Http\Response;

/**
 * CurlHttpTransport.
 *
 * @author Gabriel Bondaz <gabriel.bondaz@idci-consulting.fr>
 */
class CurlHttpTransport extends AbstractHttpTransport
{
    const USER_AGENT_NAME = "DaApiClient php/curl/REST-UA";

    protected $cUrl;

    /**
     * {@inheritdoc}
     */
    public function __construct(HttpLoggerInterface $logger = null)
    {
        parent::__construct($logger);

        $this->cUrl = curl_init();
        $this
            ->addCurlOption(CURLOPT_RETURNTRANSFER, true)
            ->addCurlOption(CURLOPT_HEADER, true)
            ->addCurlOption(CURLOPT_USERAGENT, $this->getUserAgent())
        ;
    }

    /**
     * Add cUrl option
     *
     * @param string $key
     * @param mixed $value
     *
     * @return CurlTransport
     */
    protected function addCurlOption($key, $value)
    {
        curl_setopt($this->cUrl, $key, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function buildRequest()
    {
        $buildRequestMethod = sprintf('build%sRequest', ucfirst(strtolower($this->getMethod())));
        $this
            ->addCurlOption(CURLOPT_URL, $this->getPath())
            ->buildHeaders()
            ->$buildRequestMethod()
        ;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function buildHeaders()
    {
        $headers = array();
        foreach ($this->getHeaders() as $name => $value) {
            $headers[] = sprintf('%s: %s', $name, $value);
        }
        $this->addCurlOption(CURLOPT_HTTPHEADER, $headers);

        return $this;
    }

    /**
     * Build get request
     *
     * @return CurlTransport
     */
    protected function buildGetRequest()
    {
        return $this;
    }

    /**
     * Build post request
     *
     * @return CurlTransport
     */
    protected function buildPostRequest()
    {
        $this
            ->addCurlOption(CURLOPT_POST, true)
            ->addCurlOption(CURLOPT_POSTFIELDS, $this->getQueryStrings())
        ;

        return $this;
    }

    /**
     * Build put request
     *
     * @return CurlTransport
     */
    protected function buildPutRequest()
    {
        $this
            ->addCurlOption(CURLOPT_POST, true)
            ->addCurlOption(CURLOPT_CUSTOMREQUEST, 'PUT')
            ->addCurlOption(CURLOPT_POSTFIELDS, http_build_query($this->getQueryStrings()))
            ->addHeader('X-HTTP-Method-Override', 'PUT')
        ;

        return $this;
    }

    /**
     * Build delete request
     *
     * @return CurlTransport
     */
    protected function buildDeleteRequest()
    {
        $this
            ->addCurlOption(CURLOPT_POST, true)
            ->addCurlOption(CURLOPT_CUSTOMREQUEST, 'DELETE')
            ->addCurlOption(CURLOPT_POSTFIELDS, http_build_query($this->getQueryStrings()))
            ->addHeader('X-HTTP-Method-Override', 'DELETE')
        ;

        return $this;
    }

    /**
     * Build link request
     *
     * @return CurlTransport
     */
    protected function buildLinkRequest()
    {
        $this
            ->addCurlOption(CURLOPT_CUSTOMREQUEST, 'LINK')
            ->addHeader('Link', implode(', ', $this->getLinks()))
            ->addHeader('X-HTTP-Method-Override', 'LINK')
        ;

        return $this;
    }

    /**
     * Build unlink request
     *
     * @return CurlTransport
     */
    protected function buildUnlinkRequest()
    {
        $this
            ->addCurlOption(CURLOPT_CUSTOMREQUEST, 'UNLINK')
            ->addHeader('Link', implode(', ', $this->getLinks()))
            ->addHeader('X-HTTP-Method-Override', 'UNLINK')
        ;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function executeRequest()
    {
        $response = curl_exec($this->cUrl);
        $url = curl_getinfo($this->cUrl, CURLINFO_EFFECTIVE_URL);
        $code = curl_getinfo($this->cUrl, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($this->cUrl, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);
        curl_close($this->cUrl);

        return Response::create($url, $body, $code, self::parseHeaders($header));
    }

    /**
     * Parse Headers
     *
     * @param string $raw
     * @return array
     */
    public static function parseHeaders($raw)
    {
        $headers = array(); // $headers = [];

        foreach (explode("\n", $raw) as $i => $h) {
            $h = explode(':', $h, 2);

            if (isset($h[1])) {
                if (!isset($headers[$h[0]])) {
                    $headers[$h[0]] = trim($h[1]);
                } elseif (is_array($headers[$h[0]])) {
                    $tmp = array_merge($headers[$h[0]], array(trim($h[1])));
                    $headers[$h[0]] = $tmp;
                } else {
                    $tmp = array_merge(array($headers[$h[0]]), array(trim($h[1])));
                    $headers[$h[0]] = $tmp;
                }
            }
        }

        return $headers;
    }
}