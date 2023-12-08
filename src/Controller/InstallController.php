<?php

namespace MattDunbar\ShopifyAppBundle\Controller;

use MattDunbar\ShopifyAppBundle\Entity\Install;
use MattDunbar\ShopifyAppBundle\EntityFactory\InstallFactory;
use MattDunbar\ShopifyAppBundle\Form\InstallType;
use MattDunbar\ShopifyAppBundle\Service\ShopifyApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class InstallController extends AbstractController
{
    /**
     * @var InstallFactory $installFactory
     */
    protected InstallFactory $installFactory;
    /**
     * @var ShopifyApi $shopifyApi
     */
    protected ShopifyApi $shopifyApi;
    /**
     * @var FormFactoryInterface $formFactory
     */
    protected FormFactoryInterface $formFactory;

    /**
     * Constructor
     *
     * @param InstallFactory $installFactory
     * @param ShopifyApi $shopifyApi
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(
        InstallFactory $installFactory,
        ShopifyApi $shopifyApi,
        FormFactoryInterface $formFactory
    ) {
        $this->installFactory = $installFactory;
        $this->shopifyApi = $shopifyApi;
        $this->formFactory = $formFactory;
    }

    /**
     * Render install form
     *
     * @param  Request $request
     * @return Response
     */
    #[Route('/shopify/install', name: 'app_install')]
    public function index(Request $request): Response
    {
        $installForm = $this->formFactory->create(InstallType::class, $this->installFactory->create());
        $installForm->handleRequest($request);

        if ($installForm->isSubmitted() && $installForm->isValid()) {
            return $this->handleSubmit($request, $installForm);
        }

        return $this->render(
            '@shopifyapp/install.html.twig',
            [
            'form' => $installForm->createView(),
            ]
        );
    }

    /**
     * Handle form submission
     *
     * @param  Request       $request
     * @param  FormInterface $installForm
     * @return RedirectResponse
     */
    protected function handleSubmit(Request $request, FormInterface $installForm): Response
    {
        /**
 * @var Install $install
*/
        $install = $installForm->getData();
        $installDetails = $this->shopifyApi->getInstallDetails(
            $install->getShopifyDomain(),
            $this->generateUrl('app_install_callback', [], UrlGeneratorInterface::ABSOLUTE_URL)
        );
        $request->getSession()->set('shopify_state', $installDetails['state']);
        return $this->redirect($installDetails['url']);
    }
}
