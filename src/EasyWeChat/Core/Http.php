<?php

/**
 * Http.php.
 *
 * Part of EasyWeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace EasyWeChat\Core;

use EasyWeChat\Core\Exceptions\HttpException;
use EasyWeChat\Core\Exceptions\InvalidArgumentException;
use GuzzleHttp\Client as HttpClient;

/**
 * Class Http.
 */
class Http
{
    const HTTP_RESPONSE_ERROR_NONE = 0;

    /**
     * Access token.
     *
     * @var string
     */
    protected $token;

    /**
     * Http client.
     *
     * @var HttpClient
     */
    protected $client;

    /**
     * Defualt exception.
     *
     * @var string
     */
    protected $exception = HttpException::class;

    /**
     * Constructor.
     *
     * @param \GuzzleHttp\Client                $client
     * @param \EasyWeChat\Core\AccessToken|null $token
     */
    public function __construct(HttpClient $client, AccessToken $token = null)
    {
        $this->token = $token;
        $this->client = $client;
    }

    /**
     * Set token.
     *
     * @param AccessToken $token
     */
    public function setToken(AccessToken $token)
    {
        $this->token = $token;
    }

    /**
     * Return token.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * GET request.
     *
     * @param string $url
     * @param array  $params
     *
     * @return array|bool
     *
     * @throws HttpException
     */
    public function get($url, array $params = [])
    {
        return $this->request($url, 'GET', ['query' => $params]);
    }

    /**
     * POST request.
     *
     * @param string $url
     * @param array  $params
     *
     * @return array|bool
     *
     * @throws HttpException
     */
    public function post($url, array $params = [])
    {
        return $this->request($url, 'POST', ['body' => $params]);
    }

    /**
     * JSON request.
     *
     * @param string $url
     * @param array  $params
     *
     * @return array|bool
     *
     * @throws HttpException
     */
    public function json($url, array $params = [])
    {
        return $this->request($url, 'POST', ['json' => $params]);
    }

    /**
     * Upload file.
     *
     * @param string $url
     * @param array  $files
     * @param array  $form
     *
     * @return array|bool
     *
     * @throws HttpException
     */
    public function upload($url, array $files = [], array $form = [])
    {
        $options = [
            'multipart' => array_map(function ($path, $name) {
                return [
                    'name' => $name,
                    'contents' => fopen($path, 'r'),
                ];
            }, $files),
            'form_params' => $form,
        ];

        return $this->request($url, 'POST', $options);
    }

    /**
     * Set exception to be throw when an error occurs.
     *
     * @param Exception $exception
     *
     * @return Http
     *
     * @throws InvalidArgumentException
     */
    public function setExpectedException($exception)
    {
        if (!is_subclass_of($exception, 'Exception')) {
            throw new InvalidArgumentException('Invalid Exception name.');
        }

        $this->exception = is_string($exception) ? $exception : get_class($exception);

        return $this;
    }

    /**
     * Return expected exception name.
     *
     * @return string
     */
    public function getExpectedException()
    {
        return $this->exception;
    }

    /**
     * Make a request.
     *
     * @param string $url
     * @param string $method
     * @param array  $params
     *
     * @return array|bool
     *
     * @throws HttpException
     */
    public function request($url, $method = 'GET', $params = [])
    {
        if ($this->token) {
            if (empty($params['query'])) {
                $params['query'] = [];
            }

            $params['query']['access_token'] = $this->token;
        }

        $method = strtoupper($method);

        $response = $this->client->request($method, $url, $params)->getBody();

        if (empty($response)) {
            throw new HttpException('Empty response.', -1);
        }

        $contents = json_decode($response, true);

        // while the response is an invalid JSON structure, returned the source data
        if (JSON_ERROR_NONE !== json_last_error()) {
            return $response;
        }

        if (isset($contents['errcode']) && 0 != $contents['errcode']) {
            if (empty($contents['errmsg'])) {
                $contents['errmsg'] = 'Unknown';
            }

            $this->thorwException($contents['errmsg'], $contents['errcode']);
        }

        if (isset($contents['errcode']) && $contents['errcode'] == self::HTTP_RESPONSE_ERROR_NONE) {
            return true;
        }

        return $contents;
    }

    /**
     * Throw Http Exception.
     *
     * @param string $msg
     * @param int    $code
     */
    protected function thorwException($msg, $code)
    {
        throw new $this->exception($msg, $code);
    }
}
