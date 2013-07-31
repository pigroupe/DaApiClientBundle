<?php

/**
 * This file is part of the Da Project.
 *
 * (c) Thomas Prelot <tprelot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Da\ApiClientBundle\HttpClient;

/**
 * RestApiClientImplementorInterface is the interface that an RestApiClientImplementor
 * should implement to be used as an implementor by the RestApiClientBridge.
 *
 * @author Thomas Prelot
 */
interface RestApiClientInterface
{
    /**
     * Get
     *
     * @param string $path        The relative path to the webservice.
     * @param string $queryString The specific queryString to the webservice.
     */
    function get($path, $queryString = null);

    /**
     * Post
     *
     * @param string $path        The relative path to the webservice.
     * @param string $queryString The specific queryString to the webservice.
     */
    function post($path, $queryString = null);

    /**
     * Put
     *
     * @param string $path        The relative path to the webservice.
     * @param string $queryString The specific queryString to the webservice.
     */
    function put($path, $queryString = null);

    /**
     * Delete
     *
     * @param string $path        The relative path to the webservice.
     * @param string $queryString The specific queryString to the webservice.
     */
    function delete($path, $queryString = null);
}