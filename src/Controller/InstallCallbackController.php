<?php

namespace MattDunbar\ShopifyAppBundle\Controller;

use MattDunbar\ShopifyAppBundle\Service\ShopifyApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class InstallCallbackController extends AbstractController
{
    /**
     * @var ShopifyApi $shopifyApi
     */
    protected ShopifyApi $shopifyApi;
    /**
     * Constructor
     *
     * @param ShopifyApi $shopifyApi
     */
    public function __construct(
        ShopifyApi $shopifyApi,
    ) {
        $this->shopifyApi = $shopifyApi;
    }

    /**
     * Handle Install Callback
     *
     * @param  Request $request
     * @return RedirectResponse
     */
    #[Route('/shopify/install/callback', name: 'app_install_callback')]
    public function callback(Request $request): RedirectResponse
    {
        if ($request->getSession()->get('shopify_state') !== $request->query->get('state')) {
            return $this->redirectToRoute('app_install');
        }

        $shop = $this->shopifyApi->saveAccessToken(
            (string)$request->query->get('shop', ''),
            (string)$request->query->get('code', ''),
        );

        if ($shop === null) {
            return $this->redirectToRoute('app_install');
        }

        return $this->redirect($shop->getAppAdminUrl($this->shopifyApi->getAppUrlKey()));
    }
}
