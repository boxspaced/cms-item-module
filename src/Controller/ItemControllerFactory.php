<?php
namespace Item\Controller;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Item\Controller\ItemController;
use Item\Service\ItemService;
use Workflow\Service\WorkflowService;
use Account\Service\AccountService;
use Zend\Log\Logger;
use Core\Controller\AbstractControllerFactory;
use Block\Service\BlockService;

class ItemControllerFactory extends AbstractControllerFactory implements FactoryInterface
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
