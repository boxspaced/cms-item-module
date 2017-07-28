<?php
namespace Boxspaced\CmsItemModule\Controller;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Boxspaced\CmsItemModule\Controller\ItemController;
use Boxspaced\CmsItemModule\Service\ItemService;
use Boxspaced\CmsWorkflowModule\Service\WorkflowService;
use Boxspaced\CmsAccountModule\Service\AccountService;
use Zend\Log\Logger;
use Boxspaced\CmsBlockModule\Service\BlockService;

class ItemControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new ItemController(
            $container->get(ItemService::class),
            $container->get(BlockService::class),
            $container->get(WorkflowService::class),
            $container->get(AccountService::class),
            $container->get(Logger::class),
            $container->get('config')
        );
    }

}
