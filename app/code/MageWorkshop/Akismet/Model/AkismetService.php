<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\Akismet\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Zend\Http\Client;
use Zend\Http\Request;

class AkismetService
{
    const VERSION = '1.11.1';

    /**
     * HTTP Client used to query all web services
     *
     * @var \Zend\Http\Client
     */
    protected static $_httpClient = null;

    /**
     * Akismet API key
     * @var string
     */
    protected $_apiKey;

    /**
     * Blog URL
     * @var string
     */
    protected $_blogUrl;

    /**
     * Charset used for encoding
     * @var string
     */
    protected $_charset = 'UTF-8';

    /**
     * TCP/IP port to use in requests
     * @var int
     */
    protected $_port = 80;

    /**
     * User Agent string to send in requests
     * @var string
     */
    protected $_userAgent;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;


    /**
     * AkismetService constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManagerInterface
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManagerInterface
    ) {
        $this->scopeConfig = $scopeConfig;
        $apiKey = $scopeConfig->getValue(AkismetModel::XML_PATH_MAGEWORKSHOP_AKISMET_GENERAL_API_KEY);
        $url    = $storeManagerInterface->getStore()->getBaseUrl();
        $this->setBlogUrl($url)
            ->setApiKey($apiKey)
            ->setUserAgent('Zend Framework/' . self::VERSION . ' | Akismet/1.11');
    }


    /**
     * Sets the HTTP client object to use for retrieving the feeds.  If none
     * is set, the default Zend_Http_Client will be used.
     *
     * @param \Zend\Http\Client $httpClient
     */
    public static function setHttpClient(Client $httpClient)
    {
        self::$_httpClient = $httpClient;
    }

    /**
     * Gets the HTTP client object.
     *
     * @return \Zend\Http\Client
     */
    public static function getHttpClient()
    {
        if (!self::$_httpClient instanceof Client) {
            self::$_httpClient = new Client();
        }
        return self::$_httpClient;
    }

    /**
     * Retrieve blog URL
     *
     * @return string
     */
    public function getBlogUrl()
    {
        return $this->_blogUrl;
    }

    /**
     * Set blog URL
     *
     * @param string $blogUrl
     * @return $this
     */
    public function setBlogUrl($blogUrl)
    {
        $this->_blogUrl = $blogUrl;
        return $this;
    }

    /**
     * Retrieve API key
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->_apiKey;
    }

    /**
     * Set API key
     *
     * @param string $apiKey
     * @return $this
     */
    public function setApiKey($apiKey)
    {
        $this->_apiKey = $apiKey;
        return $this;
    }

    /**
     * Retrieve charset
     *
     * @return string
     */
    public function getCharset()
    {
        return $this->_charset;
    }

    /**
     * Set charset
     *
     * @param string $charset
     * @return $this
     */
    public function setCharset($charset)
    {
        $this->_charset = $charset;
        return $this;
    }

    /**
     * Retrieve TCP/IP port
     *
     * @return int
     */
    public function getPort()
    {
        return $this->_port;
    }

    /**
     * Set TCP/IP port
     *
     * @param int $port
     * @return $this
     * @throws \Exception if non-integer value provided
     */
    public function setPort($port)
    {
        if (!is_int($port)) {
            throw new \Exception('Invalid port');
        }
        $this->_port = $port;
        return $this;
    }

    /**
     * Retrieve User Agent string
     *
     * @return string
     */
    public function getUserAgent()
    {
        return $this->_userAgent;
    }

    /**
     * Set User Agent
     *
     * Should be of form "Some user agent/version | Akismet/version"
     *
     * @param string $userAgent
     * @return $this
     * @throws \Exception with invalid user agent string
     */
    public function setUserAgent($userAgent)
    {
        if (!is_string($userAgent)
            || !preg_match(":^[^\n/]*/[^ ]* \| Akismet/[0-9\.]*$:i", $userAgent))
        {
            throw new \Exception('Invalid User Agent string; must be of format "Application name/version | Akismet/version"');
        }
        $this->_userAgent = $userAgent;
        return $this;
    }

    /**
     * Post a request
     *
     * @param string $host
     * @param string $path
     * @param array  $params
     * @return mixed
     */
    protected function _post($host, $path, array $params)
    {
        $uri    = 'http://' . $host . ':' . $this->getPort() . $path;
        $client = self::getHttpClient();
        $client->setUri($uri);
        $client->setOptions([
            'useragent'    => $this->getUserAgent(),
        ]);

        $client->setHeaders([
            'Host'         => $host,
            'Content-Type' => 'application/x-www-form-urlencoded; charset=' . $this->getCharset()
        ]);
        $client->setParameterPost($params);
        $client->setMethod(Request::METHOD_POST);
        return $client->send();
    }

    /**
     * Verify an API key
     *
     * @param string $key Optional; API key to verify
     * @param string $blog Optional; blog URL against which to verify key
     * @return boolean
     */
    public function verifyKey($key = null, $blog = null)
    {
        if (null === $key) {
            $key = $this->getApiKey();
        }

        if (null === $blog) {
            $blog = $this->getBlogUrl();
        }

        $response = $this->_post('rest.akismet.com', '/1.1/verify-key', [
            'key'  => $key,
            'blog' => $blog
        ]);

        return ('valid' == $response->getBody());
    }

    /**
     * Perform an API call
     *
     * @param string $path
     * @param array $params
     * @return \Zend\Http\Response
     * @throws \Exception if missing user_ip or user_agent fields
     */
    protected function _makeApiCall($path, $params)
    {
        if (empty($params['user_ip']) || empty($params['user_agent'])) {
            throw new \Exception('Missing required Akismet fields (user_ip and user_agent are required)');
        }

        if (!isset($params['blog'])) {
            $params['blog'] = $this->getBlogUrl();
        }

        return $this->_post($this->getApiKey() . '.rest.akismet.com', $path, $params);
    }

    /**
     * Check a comment for spam
     *
     * Checks a comment to see if it is spam. $params should be an associative
     * array with one or more of the following keys (unless noted, all keys are
     * optional):
     * - blog: URL of the blog. If not provided, uses value returned by {@link getBlogUrl()}
     * - user_ip (required): IP address of comment submitter
     * - user_agent (required): User Agent used by comment submitter
     * - referrer: contents of HTTP_REFERER header
     * - permalink: location of the entry to which the comment was submitted
     * - comment_type: typically, one of 'blank', 'comment', 'trackback', or 'pingback', but may be any value
     * - comment_author: name submitted with the content
     * - comment_author_email: email submitted with the content
     * - comment_author_url: URL submitted with the content
     * - comment_content: actual content
     *
     * Additionally, Akismet suggests returning the key/value pairs in the
     * $_SERVER array, and these may be included in the $params.
     *
     * This method implements the Akismet comment-check REST method.
     *
     * @param array $params
     * @return boolean
     * @throws \Exception with invalid API key
     */
    public function isSpam($params)
    {
        $response = $this->_makeApiCall('/1.1/comment-check', $params);
        $return = trim($response->getBody());
        if ('invalid' == $return) {
            #require_once 'Zend/Service/Exception.php';
            throw new \Exception('Invalid API key');
        }

        if ('true' == $return) {
            return true;
        }

        return false;
    }

    /**
     * Submit spam
     *
     * Takes the same arguments as {@link isSpam()}.
     *
     * Submits known spam content to Akismet to help train it.
     *
     * This method implements Akismet's submit-spam REST method.
     *
     * @param array $params
     * @return void
     * @throws \Exception with invalid API key
     */
    public function submitSpam($params)
    {
        $response = $this->_makeApiCall('/1.1/submit-spam', $params);
        $value    = trim($response->getBody());
        if ('invalid' == $value) {
            throw new \Exception('Invalid API key');
        }
    }
}
