<?php
namespace Boxspaced\CmsItemModule\Controller\Plugin;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Boxspaced\CmsAccountModule\Service\AccountService;

class ItemAdminWidgetFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new ItemAdminWidget(
            $container->get(AccountService::class),
            $container->get('config')
        );
    }

}
