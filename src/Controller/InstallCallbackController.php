<?php

namespace MattDunbar\ShopifyAppBundle\Controller;

use MattDunbar\ShopifyAppBundle\Service\ShopifyApi;
use MattDunbar\ShopifyAppBundle\Service\ShopifyApi\Config;
use MattDunbar\ShopifyAppBundle\Service\ShopifyApi\OAuth;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class InstallCallbackController extends AbstractController
{
    /**
     * @var OAuth $oAuth
     */
    protected OAuth $oAuth;
    /**
     * @var Config $config
     */
    protected Config $config;

    /**
     * Constructor
     *
     * @param OAuth $oAuth
     * @param Config $config
     */
    public function __construct(
        OAuth $oAuth,
        Config $config
    ) {
        $this->oAuth = $oAuth;
        $this->config = $config;
    }

    /**
     * Handle Install Callback
     *
     * @param  Request $request
     * @return RedirectResponse
     */
    #[Route('/shopify/install/callback', name: 'shopify_app_install_callback')]
    public function callback(Request $request): RedirectResponse
    {
        if ($request->getSession()->get('shopify_state') !== $request->query->get('state')) {
            return $this->redirectToRoute('shopify_app_install');
        }

        $shop = $this->oAuth->finishInstall(
            (string)$request->query->get('shop', ''),
            (string)$request->query->get('code', ''),
        );

        if ($shop === null) {
            return $this->redirectToRoute('shopify_app_install');
        }

        return $this->redirect($shop->getAppAdminUrl($this->config->getAppUrlKey()));
    }
}
