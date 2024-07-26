<?php

namespace App\Admin\Extension;

use App\Controller\Admin\MediaBrowserController;
use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'sonata.admin.extension', attributes: ['target' => 'sonata.media.admin.media'])]
final class MediaBrowserAdminExtension extends AbstractAdminExtension
{
    public function configure(AdminInterface $admin): void
    {
        $admin->setTemplate('outer_list_rows_browser', '@SonataFormatter/Ckeditor/list_outer_rows_browser.html.twig');
    }

    public function configurePersistentParameters(AdminInterface $admin, array $parameters): array
    {
        if ($admin->hasRequest()) {
            $request = $admin->getRequest();

            $parameters['CKEditor'] = $request->query->get('CKEditor');
            $parameters['CKEditorFuncNum'] = $request->query->get('CKEditorFuncNum');
        }

        return $parameters;
    }

    public function configureBatchActions(AdminInterface $admin, array $actions): array
    {
        if ('browser' === $admin->getListMode()) {
            return [];
        }

        return $actions;
    }

    public function configureRoutes(AdminInterface $admin, RouteCollectionInterface $collection): void
    {
        $collection->add('custom_browser', null, [
            '_controller' => MediaBrowserController::class . "::browserAction",
        ]);

        $collection->add('custom_upload', null, [
            '_controller' => MediaBrowserController::class . "::uploadAction",
        ]);
    }
}
