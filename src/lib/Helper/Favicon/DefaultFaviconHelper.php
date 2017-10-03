<?php
/**
 * Created by PhpStorm.
 * User: marius
 * Date: 10.09.17
 * Time: 02:16
 */

namespace OCA\Passwords\Helper\Favicon;

use OCA\Passwords\Services\HelperService;
use OCP\Files\SimpleFS\ISimpleFile;

/**
 * Class DefaultPageShotHelper
 *
 * @package OCA\Passwords\Helper\Favicon
 */
class DefaultFaviconHelper extends AbstractFaviconHelper {

    /**
     * @var string
     */
    protected $prefix = HelperService::FAVICON_DEFAULT;

    /**
     * @param string $domain
     *
     * @return ISimpleFile
     */
    public function getFavicon(string $domain): ISimpleFile {
        return $this->getDefaultFavicon();
    }

    /**
     * @param string $domain
     *
     * @return string
     */
    protected function getFaviconUrl(string $domain): string {
        return '';
    }
}