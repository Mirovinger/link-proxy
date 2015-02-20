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
class GFNLinkProxy_Route_Prefix_Redirect implements XenForo_Route_Interface
{
    public function match($routePath, Zend_Controller_Request_Http $request, XenForo_Router $router)
    {
        return $router->getRouteMatch('GFNLinkProxy_ControllerPublic_Redirect');
    }
} 