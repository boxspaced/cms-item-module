<?php
namespace Boxspaced\CmsItemModule\Service;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Zend\Log\Logger;
use Zend\Authentication\AuthenticationService;
use Boxspaced\EntityManager\EntityManager;
use Boxspaced\CmsItemModule\Model;
use Boxspaced\CmsAccountModule\Model\UserRepository;
use Boxspaced\CmsCoreModule\Model\ModuleRepository;
use Boxspaced\CmsBlockModule\Model\BlockRepository;
use Boxspaced\CmsMenuModule\Model\MenuRepository;
use Boxspaced\CmsSlugModule\Model\RouteRepository;
use Boxspaced\CmsVersioningModule\Model\VersioningService;
use Boxspaced\CmsWorkflowModule\Model\WorkflowService;
use Boxspaced\CmsCoreModule\Model\EntityFactory;

class ItemServiceFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new ItemService(
            $container->get('Cache\Long'),
            $container->get(Logger::class),
            $container->get('config'),
            $container->get(AuthenticationService::class),
            $container->get(EntityManager::class),
            $container->get(Model\ItemTypeRepository::class),
            $container->get(Model\ItemRepository::class),
            $container->get(Model\ItemTeaserTemplateRepository::class),
            $container->get(Model\ItemTemplateRepository::class),
            $container->get(UserRepository::class),
            $container->get(ModuleRepository::class),
            $container->get(BlockRepository::class),
            $container->get(MenuRepository::class),
            $container->get(RouteRepository::class),
            $container->get(VersioningService::class),
            $container->get(WorkflowService::class),
            $container->get(EntityFactory::class)
        );
    }

}
