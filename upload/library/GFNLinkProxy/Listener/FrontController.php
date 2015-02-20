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
class GFNLinkProxy_Listener_FrontController
{
    public static function postView(XenForo_FrontController $fc, &$output)
    {
        if (!($fc->getDependencies() instanceof XenForo_Dependencies_Public))
        {
            return;
        }

        if (!is_string($output) || empty($output))
        {
            return;
        }

        try
        {
            if (preg_match('#<body[^>]*>#sU', $output))
            {
                self::_changeExternalLinks($output);
            }
            else
            {
                $json = json_decode($output, true);

                if ($json && isset($json['templateHtml']))
                {
                    self::_changeExternalLinks($json['templateHtml']);
                    $output = json_encode($json);
                }
            }
        }
        catch (Exception $e) { }
    }

    protected static function _changeExternalLinks(&$content)
    {
        if (empty($content))
        {
            return;
        }

        $dom = new Zend_Dom_Query();
        $dom->setDocumentHtml($content, 'utf-8');

        if ($dom->getDocumentErrors())
        {
            return;
        }

        $query = $dom->query('.externalLink');

        if (!$query->count())
        {
            return;
        }

        $links = array();

        /** @var DOMElement $a */
        foreach ($query as $a)
        {
            $link = $a->getAttribute('href');

            if (empty($link))
            {
                continue;
            }

            $links[] = $link;
            GFNLinkProxy_Api::preload($link);
        }

        foreach ($links as $link)
        {
            $content = preg_replace_callback(
                '#(<a[^>]+href=("|\')?)(' . preg_quote($link, '#') . ')(("|\')?[^>]+class=("|\')?[^"\']*externalLink[^"\']*("|\')?[^>]*>)#sU',
                array(__CLASS__, '_replace'), $content, 1
            );
        }
    }

    protected static function _replace($match)
    {
        if (empty($match[3]) || empty($match[4]))
        {
            return $match[0];
        }

        return $match[1] . GFNLinkProxy_Api::getProxyLink($match[3]) . $match[4];
    }
} 