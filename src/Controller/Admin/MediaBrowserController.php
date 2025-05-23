<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\Admin;

use Symfony\Component\Security\Http\Attribute\IsGranted;
use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;
use Sonata\ClassificationBundle\Model\ContextManagerInterface;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Model\MediaManagerInterface;
use Sonata\MediaBundle\Provider\MediaProviderInterface;
use Sonata\MediaBundle\Provider\Pool;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[IsGranted('ROLE_ADMIN')]
#[Route("/admin/app/media")]
class MediaBrowserController extends CRUDController
{
    public static function getSubscribedServices(): array
    {
        return [
            'sonata.media.pool' => Pool::class,
            'sonata.media.manager.media' => MediaManagerInterface::class,
            'sonata.media.manager.category' => '?' . CategoryManagerInterface::class,
            'sonata.media.manager.context' => '?' . ContextManagerInterface::class,
        ] + parent::getSubscribedServices();
    }

    #[Route("/custom_browser", name: "admin_app_media_custom_browser")]
    public function browserAction(Request $request): Response
    {
        $this->checkIfMediaBundleIsLoaded();

        $this->admin->checkAccess('list');
        $this->admin->setListMode('browser');

        $filters = $request->query->all('filter');

        $pool = $this->container->get('sonata.media.pool');
        \assert($pool instanceof Pool);

        // set the default context
        if (\array_key_exists('context', $filters)) {
            $context = $filters['context']['value'];
        } else {
            $context = $this->admin->getPersistentParameter('context', $pool->getDefaultContext());
        }

        $datagrid = $this->admin->getDatagrid();
        $datagrid->setValue('context', null, $context);
        $datagrid->setValue('providerName', null, $this->admin->getPersistentParameter('provider'));

        $rootCategory = null;
        if (
            $this->container->has('sonata.media.manager.category')
            && $this->container->has('sonata.media.manager.context')
        ) {
            $categoryManager = $this->container->get('sonata.media.manager.category');
            \assert($categoryManager instanceof CategoryManagerInterface);
            $contextManager = $this->container->get('sonata.media.manager.context');
            \assert($contextManager instanceof ContextManagerInterface);

            $rootCategories = $categoryManager->getRootCategoriesForContext($contextManager->find($context));

            if ([] !== $rootCategories) {
                $rootCategory = current($rootCategories);
            }

            if (null !== $rootCategory && [] === $filters) {
                $datagrid->setValue('category', null, $rootCategory->getId());
            }

            if (null !== $request->query->get('category')) {
                $category = $categoryManager->findOneBy([
                    'id' => $request->query->getInt('category'),
                    'context' => $context,
                ]);

                if (null !== $category) {
                    $datagrid->setValue('category', null, $category->getId());
                } elseif (null !== $rootCategory) {
                    $datagrid->setValue('category', null, $rootCategory->getId());
                }
            }
        }

        $formats = [];
        foreach ($datagrid->getResults() as $media) {
            \assert($media instanceof MediaInterface);
            $formats[$media->getId() ?? ''] = $pool->getFormatNamesByContext(
                $media->getContext() ?? $context
            );
        }

        $formView = $datagrid->getForm()->createView();
        $this->setFormTheme($formView, $this->admin->getFilterTheme());

        return $this->renderWithExtraParams('@SonataFormatter/Ckeditor/browser.html.twig', [
            'action' => 'browser',
            'form' => $formView,
            'datagrid' => $datagrid,
            'root_category' => $rootCategory,
            'formats' => $formats,
            'csrf_token' => $this->getCsrfToken('sonata.batch'),
            'export_formats' => [],
        ]);
    }

    #[Route("/media_upload", name: "admin_app_media_custom_upload")]
    public function uploadAction(Request $request): Response
    {
        $this->checkIfMediaBundleIsLoaded();

        $this->admin->checkAccess('create');

        $mediaManager = $this->container->get('sonata.media.manager.media');
        \assert($mediaManager instanceof MediaManagerInterface);

        $provider = $request->get('provider');
        $file = $request->files->get('upload');

        if (null === $provider || null === $file) {
            throw $this->createNotFoundException();
        }

        $pool = $this->container->get('sonata.media.pool');
        \assert($pool instanceof Pool);
        $context = $request->get('context', $pool->getDefaultContext());

        $media = $mediaManager->create();
        $media->setContext($context);
        $media->setProviderName($provider);
        $media->setBinaryContent($file);

        $mediaManager->save($media);
        $this->admin->createObjectSecurity($media);

        $format = $pool->getProvider($provider)->getFormatName(
            $media,
            $request->get('format', MediaProviderInterface::FORMAT_REFERENCE)
        );

        return $this->render('@SonataFormatter/Ckeditor/upload.html.twig', [
            'action' => 'list',
            'object' => $media,
            'format' => $format,
        ]);
    }

    /**
     * Checks if SonataMediaBundle is loaded otherwise throws an exception.
     *
     * @throws \RuntimeException
     */
    private function checkIfMediaBundleIsLoaded(): void
    {
        $bundles = $this->getParameter('kernel.bundles');
        \assert(\is_array($bundles));

        if (!isset($bundles['SonataMediaBundle'])) {
            throw new \RuntimeException('You cannot use this feature because you have to use SonataMediaBundle');
        }
    }
}
