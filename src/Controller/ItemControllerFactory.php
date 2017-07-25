<?php
namespace Boxspaced\CmsItemModule\Controller;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Boxspaced\CmsItemModule\Controller\ItemController;
use Boxspaced\CmsItemModule\Service\ItemService;
use Boxspaced\CmsWorkflowModule\Service\WorkflowService;
use Boxspaced\CmsAccountModule\Service\AccountService;
use Zend\Log\Logger;
use Boxspaced\CmsCoreModule\Controller\AbstractControllerFactory;
use Boxspaced\CmsBlockModule\Service\BlockService;

class ItemControllerFactory extends AbstractControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $controller = new ItemController(
            $container->get(ItemService::class),
            $container->get(BlockService::class),
            $container->get(WorkflowService::class),
            $container->get(AccountService::class),
            $container->get(Logger::class),
            $container->get('config')
        );

        return $this->adminNavigationWidget($controller);
    }

}
