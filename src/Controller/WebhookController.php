<?php

namespace MattDunbar\ShopifyAppBundle\Controller;

use MattDunbar\ShopifyAppBundle\Service\ShopifyApi\Webhook;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WebhookController extends AbstractController
{
    /**
     * @var Webhook $webhook
     */
    protected Webhook $webhook;

    /**
     * Constructor
     *
     * @param Webhook $webhook
     */
    public function __construct(
        Webhook $webhook
    ) {
        $this->webhook = $webhook;
    }

    #[Route('/shopify/webhook/bulkoperation', name: 'shopify_app_webhook_bulkoperation')]
    public function webhook(Request $request): JsonResponse
    {
        if (!$this->webhook->verify()) {
            return $this->json(['error' => 'Invalid webhook signature'], Response::HTTP_FORBIDDEN);
        }

        // @TODO Handle webhook: Need to test, request contents are not documented
        return $this->json(['success' => true]);
    }
}
