<?php
/**
 * This file is part of the Passwords App
 * created by Marius David Wieschollek
 * and licensed under the AGPL.
 */

namespace OCA\Passwords\Helper\Http;

use OCP\IConfig;
use OCP\IRequest;

/**
 * Class SetupReportHelper
 *
 * @package OCA\Passwords\Helper\Http
 */
class SetupReportHelper {

    /**
     * @var IConfig
     */
    protected $config;

    /**
     * @var IRequest
     */
    protected $request;

    /**
     * @var bool
     */
    protected $isProxy;

    /**
     * @var string
     */
    protected $remoteAddress;

    /**
     * SetupReportHelper constructor.
     *
     * @param IRequest $request
     * @param IConfig  $config
     */
    public function __construct(IRequest $request, IConfig $config) {
        $this->request = $request;
        $this->config  = $config;
    }

    /**
     * @return array[]
     */
    public function getHttpsSetupReport() {
        return [
            $this->getHttpsStatus(),
            $this->getProxyStatus(),
            $this->getProxySettingsStatus(),
            $this->getPhpStatus(),
        ];
    }

    /**
     * @return array
     */
    protected function getHttpsStatus() {
        return [
            'label' => 'HTTPS detection',
            'items' => [
                [
                    'label'    => 'Nextcloud reported protocol',
                    'expected' => 'https',
                    'actual'   => $this->request->getServerProtocol()
                ],
                [
                    'label'    => 'Client reported protocol',
                    'expected' => 'https',
                    'actual'   => $this->request->getParam('https', 'true') === 'true' ? 'https':'http'
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    protected function getProxyStatus() {
        $isProxy = $this->isProxy();

        return [
            'label' => 'Proxy detection',
            'items' => [
                [
                    'label'    => 'Proxy detected',
                    'expected' => $isProxy ? '':'Yes if Proxy',
                    'actual'   => $isProxy ? 'yes':'no',
                ],
                [
                    'label'    => 'Detected remote address',
                    'expected' => $this->request->getRemoteAddress(),
                    'actual'   => $this->getRemoteAddress()
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    protected function getProxySettingsStatus() {
        $isProxy       = $this->isProxy();
        $remoteAddress = $this->getRemoteAddress();

        $overwritecondaddr = 'not set';
        $trustedProxies    = '[]';
        if($isProxy) {
            if($remoteAddress !== $_SERVER['REMOTE_ADDR']) {
                $overwritecondaddr = '^'.str_replace('.', '\.', $_SERVER['REMOTE_ADDR']).'$';
                $trustedProxies    = "[\"{$_SERVER['REMOTE_ADDR']}\"]";
            } else {
                $overwritecondaddr = '^the\.proxy\.ip$';
                $trustedProxies    = '["the.proxy.ip"]';
            }
        }

        return [
            'label' => 'Nextcloud Proxy Settings',
            'items' => [
                [
                    'label'    => 'overwriteprotocol',
                    'expected' => $isProxy ? 'https':'not set',
                    'actual'   => $this->config->getSystemValue('overwriteprotocol', 'not set'),
                ],
                [
                    'label'    => 'overwritecondaddr',
                    'expected' => $overwritecondaddr,
                    'actual'   => $this->config->getSystemValue('overwritecondaddr', 'not set'),
                ],
                [
                    'label'    => 'trusted_proxies',
                    'expected' => $trustedProxies,
                    'actual'   => json_encode($this->config->getSystemValue('trusted_proxies', [])),
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    protected function getPhpStatus() {
        $forwardedProto = 'not set';
        $forwardedFor   = 'not set';

        if($this->isProxy()) {
            $forwardedProto = 'https';
            $forwardedFor   = $this->getRemoteAddress();
        }

        $items = [
            [
                'label'    => '$_SERVER[\'HTTPS\']',
                'expected' => 'on',
                'actual'   => isset($_SERVER['HTTPS']) ? $_SERVER['HTTPS']:'not set',
            ],
            [
                'label'    => '$_SERVER[\'REQUEST_SCHEME\']',
                'expected' => 'https',
                'actual'   => isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME']:'not set'
            ],
            [
                'label'    => '$_SERVER[\'REMOTE_ADDR\']',
                'expected' => '',
                'actual'   => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR']:'not set'
            ],
            [
                'label'    => '$_SERVER[\'HTTP_X_FORWARDED_PROTO\']',
                'expected' => $forwardedProto,
                'actual'   => isset($_SERVER['HTTP_X_FORWARDED_PROTO']) ? $_SERVER['HTTP_X_FORWARDED_PROTO']:'not set'
            ]
        ];

        $headers = $this->config->getSystemValue('forwarded_for_headers', ['HTTP_X_FORWARDED_FOR']);
        foreach($headers as $header) {
            $items[]
                = [
                'label'    => "\$_SERVER['{$header}']",
                'expected' => $forwardedFor,
                'actual'   => isset($_SERVER[ $header ]) ? $_SERVER[ $header ]:'not set'
            ];
        }

        return [
            'label' => 'PHP Variables',
            'items' => $items
        ];
    }

    /**
     * @return bool
     */
    protected function isProxy() {
        if($this->isProxy !== null) return $this->isProxy;

        $headers       = $this->config->getSystemValue('forwarded_for_headers', ['HTTP_X_FORWARDED_FOR']);
        $this->isProxy = isset($_SERVER['HTTP_X_FORWARDED_FOR']) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) || isset($_SERVER['HTTP_X_FORWARDED_PORT']);

        foreach($headers as $header) {
            if(isset($_SERVER[ $header ])) {
                $this->isProxy = true;
            }
        }

        return $this->isProxy;
    }

    /**
     * @return string
     */
    protected function getRemoteAddress() {
        if($this->remoteAddress !== null) return $this->remoteAddress;

        $this->remoteAddress = $_SERVER['REMOTE_ADDR'];
        $headers             = $this->config->getSystemValue('forwarded_for_headers', ['HTTP_X_FORWARDED_FOR']);

        foreach($headers as $header) {
            if(isset($_SERVER[ $header ])) {
                $this->remoteAddress = $_SERVER[ $header ];
            }
        }

        return $this->remoteAddress;
    }
}