<?php /*{$___fileHash}*/

/**
 * @package    {$___addOnTitle}
 * @version    {$___fileVersion}
 * @since      {$___fileCreateVersion}
 * @author     GoodForNothing Labs
 * @copyright  Copyright © 2012-{$___currentYear} GoodForNothing Labs <http://gfnlabs.com/>
 * @license    {$___licenseLink}
 * @link       {$___addOnLink}
 */
class GFNLinkProxy_Api
{
    public static function preload($link)
    {

    }

    public static function getProxyLink($link)
    {
        if (self::_protocolWhiteListed($link) || self::_domainWhiteListed($link))
        {
            return $link;
        }

        return XenForo_Link::buildPublicLink('full:redirect', null, array(
            'to' => base64_encode($link)
        ));
    }

    protected static function _domainWhiteListed($link)
    {
        static $whiteList = null;

        if ($whiteList === null)
        {
            $whiteList = GFNLinkProxy_Options::getInstance()->get('whiteListDomain');
            $whiteList = preg_split('/\r?\n/', $whiteList, PREG_SPLIT_NO_EMPTY);

            foreach ($whiteList as $k => &$v)
            {
                $v = trim($v);

                if (empty($v))
                {
                    unset($whiteList[$k]);
                }
            }

            $whiteList[] = parse_url(XenForo_Application::getOptions()->get('boardUrl'), PHP_URL_HOST);
        }

        $domain = parse_url($link, PHP_URL_HOST);
        if (!$domain)
        {
            return false;
        }

        foreach ($whiteList as $check)
        {
            if (strpos($domain, $check) !== false)
            {
                return true;
            }
        }

        return false;
    }

    protected static function _protocolWhiteListed($link)
    {
        static $whiteList = null;

        if ($whiteList === null)
        {
            $whiteList = GFNLinkProxy_Options::getInstance()->get('whiteListProtocol');
            $whiteList = preg_split('/\r?\n/', $whiteList, PREG_SPLIT_NO_EMPTY);

            foreach ($whiteList as $k => &$v)
            {
                $v = trim($v);

                if (empty($v))
                {
                    unset($whiteList[$k]);
                }
            }
        }

        foreach ($whiteList as $check)
        {
            if (strpos($link, $check) === 0)
            {
                return true;
            }
        }

        return false;
    }
} 