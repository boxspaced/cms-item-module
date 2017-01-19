<?php
namespace Boxspaced\CmsItemModule\Service;

use DateTime;
use Zend\Cache\Storage\Adapter\AbstractAdapter as Cache;
use Zend\Log\Logger;
use Zend\Authentication\AuthenticationService;
use Boxspaced\EntityManager\EntityManager;
use Boxspaced\CmsItemModule\Model;
use Zend\Filter\StaticFilter;
use Zend\Filter\Word\DashToCamelCase;
use Zend_Search_Lucene as Search;
use Zend_Search_Lucene_Document as SearchDocument;
use Zend_Search_Lucene_Field as SearchField;
use utilphp\util as Util;
use Boxspaced\CmsItemModule\Exception;
use Boxspaced\CmsAccountModule\Model\UserRepository;
use Boxspaced\CmsSlugModule\Model\Route;
use Boxspaced\CmsCoreModule\Model\ModuleRepository;
use Boxspaced\CmsBlockModule\Model\BlockRepository;
use Boxspaced\CmsMenuModule\Model\MenuRepository;
use Boxspaced\CmsSlugModule\Model\RouteRepository;
use Boxspaced\CmsVersioningModule\Model\VersioningService;
use Boxspaced\CmsWorkflowModule\Model\WorkflowService;
use Boxspaced\CmsCoreModule\Model\EntityFactory;
use Boxspaced\CmsAccountModule\Model\User;
use Boxspaced\CmsVersioningModule\Model\VersionableInterface;
use Boxspaced\CmsMenuModule\Model\MenuItem;
use Boxspaced\CmsMenuModule\Service\MenuService;
use Boxspaced\CmsCoreModule\Model\ProvisionalLocation as ProvisionalLocationEntity;

class ItemService
{

    const MODULE_NAME = 'item';
    const PUBLISH_TO_MENU = 'Menu';
    const PUBLISH_TO_STANDALONE = 'Standalone';
    const CURRENT_PUBLISHING_OPTIONS_CACHE_ID = 'currentPublishingOptionsItem%d';
    const ITEM_CACHE_ID = 'item%d';
    const ITEM_META_CACHE_ID = 'itemMeta%d';
    const ITEM_TYPE_CACHE_ID = 'itemType%d';

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var AuthenticationService
     */
    protected $authService;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var Model\ItemTypeRepository
     */
    protected $itemTypeRepository;

    /**
     * @var Model\ItemRepository
     */
    protected $itemRepository;

    /**
     * @var Model\ItemTeaserTemplateRepository
     */
    protected $itemTeaserTemplateRepository;

    /**
     * @var Model\ItemTemplateRepository
     */
    protected $itemTemplateRepository;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var ModuleRepository
     */
    protected $moduleRepository;

    /**
     * @var BlockRepository
     */
    protected $blockRepository;

    /**
     * @var MenuRepository
     */
    protected $menuRepository;

    /**
     * @var RouteRepository
     */
    protected $routeRepository;

    /**
     * @var VersioningService
     */
    protected $versioningService;

    /**
     * @var WorkflowService
     */
    protected $workflowService;

    /**
     * @var EntityFactory
     */
    protected $entityFactory;

    /**
     * @param Cache $cache
     * @param Logger $logger
     * @param array $config
     * @param AuthenticationService $authService
     * @param EntityManager $entityManager
     * @param Model\ItemTypeRepository $itemTypeRepository
     * @param Model\ItemRepository $itemRepository
     * @param Model\ItemTeaserTemplateRepository $itemTeaserTemplateRepository
     * @param Model\ItemTemplateRepository $itemTemplateRepository
     * @param UserRepository $userRepository
     * @param ModuleRepository $moduleRepository
     * @param BlockRepository $blockRepository
     * @param MenuRepository $menuRepository
     * @param RouteRepository $routeRepository
     * @param VersioningService $versioningService
     * @param WorkflowService $workflowService
     * @param EntityFactory $entityFactory
     */
    public function __construct(
        Cache $cache,
        Logger $logger,
        array $config,
        AuthenticationService $authService,
        EntityManager $entityManager,
        Model\ItemTypeRepository $itemTypeRepository,
        Model\ItemRepository $itemRepository,
        Model\ItemTeaserTemplateRepository $itemTeaserTemplateRepository,
        Model\ItemTemplateRepository $itemTemplateRepository,
        UserRepository $userRepository,
        ModuleRepository $moduleRepository,
        BlockRepository $blockRepository,
        MenuRepository $menuRepository,
        RouteRepository $routeRepository,
        VersioningService $versioningService,
        WorkflowService $workflowService,
        EntityFactory $entityFactory
    )
    {
        $this->cache = $cache;
        $this->logger = $logger;
        $this->config = $config;
        $this->authService = $authService;
        $this->entityManager = $entityManager;
        $this->itemTypeRepository = $itemTypeRepository;
        $this->itemRepository = $itemRepository;
        $this->itemTeaserTemplateRepository = $itemTeaserTemplateRepository;
        $this->itemTemplateRepository = $itemTemplateRepository;
        $this->userRepository = $userRepository;
        $this->moduleRepository = $moduleRepository;
        $this->blockRepository = $blockRepository;
        $this->menuRepository = $menuRepository;
        $this->routeRepository = $routeRepository;
        $this->versioningService = $versioningService;
        $this->workflowService = $workflowService;
        $this->entityFactory = $entityFactory;

        if ($this->authService->hasIdentity()) {
            $identity = $authService->getIdentity();
            $this->user = $userRepository->getById($identity->id);
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isNameAvailable($name)
    {
        return (null === $this->routeRepository->getBySlug($name));
    }

    /**
     * @return void
     */
    public function reindex()
    {
        $path = $this->config['search']['index_path'];

        if (!$path) {
            throw new Exception\InvalidArgumentException('No path provided');
        }

        if (!Util::rmdir($path)) {
            throw new Exception\RuntimeException('Unable to remove current index');
        }

        $index = Search::create($path);

        foreach ($this->itemRepository->getAllLive() as $item) {

            $doc = new SearchDocument();

            $doc->addField(SearchField::Keyword('module', 'item', 'utf-8'));
            $doc->addField(SearchField::Keyword('contentId', $item->getId(), 'utf-8'));
            $doc->addField(SearchField::Keyword('slug', $item->getRoute()->getSlug(), 'utf-8'));

            $doc->addField(SearchField::Text('title', $item->getTitle(), 'utf-8'));
            $doc->addField(SearchField::UnStored('keywords', $item->getMetaKeywords(), 'utf-8'));
            $doc->addField(SearchField::UnStored('description', $item->getMetaDescription(), 'utf-8'));

            $content = '';

            foreach ($item->getFields() as $field) {
                $content .= strip_tags($field->getValue());
            }

            foreach ($item->getParts() as $part) {
                foreach ($part->getFields() as $field) {
                    $content .= strip_tags($field->getValue());
                }
            }

            $doc->addField(SearchField::UnStored('contents', $content), 'utf-8');

            $index->addDocument($doc);
        }

        $index->commit();
    }

    /**
     * @return ItemType[]
     */
    public function getTypes()
    {
        $types = [];

        foreach ($this->itemTypeRepository->getAll() as $type) {

            $types[] = ItemType::createFromEntity($type);
        }

        return $types;
    }

    /**
     * @return ItemType
     */
    public function getType($id)
    {
        $cacheId = sprintf(static::ITEM_TYPE_CACHE_ID, $id);
        $cached = $this->cache->getItem($cacheId);

        if (null !== $cached) {
            return $cached;
        }

        $type = $this->itemTypeRepository->getById($id);

        if (null === $type) {
            throw new Exception\UnexpectedValueException('Unable to find type with given ID');
        }

        $itemType = ItemType::createFromEntity($type);

        $this->cache->setItem($cacheId, $itemType);

        return $itemType;
    }

    /**
     * @param int $id
     * @return Item
     */
    public function getCacheControlledItem($id)
    {
        $cacheId = sprintf(static::ITEM_CACHE_ID, $id);
        $cached = $this->cache->getItem($cacheId);

        if (null !== $cached) {
            return $cached;
        }

        $item = $this->getItem($id);

        $this->cache->setItem($cacheId, $item);

        return $item;
    }

    /**
     * @param int $id
     * @return Item
     */
    public function getItem($id)
    {
        $item = $this->itemRepository->getById($id);

        if (null === $item) {
            throw new Exception\UnexpectedValueException('Unable to find an item with given ID');
        }

        return Item::createFromEntity($item);
    }

    /**
     * @param int $id
     * @return ItemMeta
     */
    public function getCacheControlledItemMeta($id)
    {
        $cacheId = sprintf(static::ITEM_META_CACHE_ID, $id);
        $cached = $this->cache->getItem($cacheId);

        if (null !== $cached) {
            return $cached;
        }

        $itemMeta = $this->getItemMeta($id);

        $this->cache->setItem($cacheId, $itemMeta);

        return $itemMeta;
    }

    /**
     * @param int $id
     * @return ItemMeta
     */
    public function getItemMeta($id)
    {
        $item = $this->itemRepository->getById($id);

        if (null === $item) {
            throw new Exception\UnexpectedValueException('Unable to find an item with given ID');
        }

        return ItemMeta::createFromEntity($item);
    }

    /**
     * @param int $id
     * @return PublishingOptions
     */
    public function getCurrentPublishingOptions($id)
    {
        $cacheId = sprintf(static::CURRENT_PUBLISHING_OPTIONS_CACHE_ID, $id);
        $cached = $this->cache->getItem($cacheId);

        if (null !== $cached) {
            return $cached;
        }

        $item = $this->itemRepository->getById($id);

        if (null === $item) {
            throw new Exception\UnexpectedValueException('Unable to find an item with given ID');
        }

        if ($item->getStatus() !== VersionableInterface::STATUS_PUBLISHED) {
            // @todo return null
            throw new Exception\UnexpectedValueException('Item is not published');
        }

        $publishingOptions = new PublishingOptions();
        $publishingOptions->name = $item->getRoute()->getSlug();
        $publishingOptions->colourScheme = $item->getColourScheme();
        $publishingOptions->teaserTemplateId = $item->getTeaserTemplate()->getId();
        $publishingOptions->templateId = $item->getTemplate()->getId();
        $publishingOptions->liveFrom = $item->getLiveFrom();
        $publishingOptions->expiresEnd = $item->getExpiresEnd();
        $publishingOptions->to = $item->getPublishedTo();

        if ($item->getPublishedTo() === static::PUBLISH_TO_MENU) {

            foreach ($this->getFlattenedMenu() as $flattenedMenuItem) {

                $menuItem = $flattenedMenuItem['item'];

                if ($this->getContentByMenuItem($menuItem) !== $item) {
                    continue;
                }

                if ($menuItem->getParentMenuItem()) {
                    $publishingOptions->beneathMenuItemId = $menuItem->getParentMenuItem()->getId();
                } else {
                    $publishingOptions->beneathMenuItemId = 0;
                }
            }

        }

        foreach ($item->getFreeBlocks() as $freeBlockEntity) {

            if ($freeBlockEntity->getBlock()->getStatus() !== VersionableInterface::STATUS_PUBLISHED) {
                continue;
            }

            $freeBlock = new FreeBlock();
            $freeBlock->name = $freeBlockEntity->getTemplateBlock()->getName();
            $freeBlock->id = $freeBlockEntity->getBlock()->getId();

            $publishingOptions->freeBlocks[] = $freeBlock;
        }

        foreach ($item->getBlockSequences() as $blockSequenceEntity) {

            $blockSequence = new BlockSequence();
            $blockSequence->name = $blockSequenceEntity->getTemplateBlock()->getName();

            foreach ($blockSequenceEntity->getBlocks() as $blockSequenceBlockEntity) {

                if ($blockSequenceBlockEntity->getBlock()->getStatus() !== VersionableInterface::STATUS_PUBLISHED) {
                    continue;
                }

                $blockSequenceBlock = new BlockSequenceBlock();
                $blockSequenceBlock->id = $blockSequenceBlockEntity->getBlock()->getId();
                $blockSequenceBlock->orderBy = $blockSequenceBlockEntity->getOrderBy();

                $blockSequence->blocks[] = $blockSequenceBlock;
            }

            $publishingOptions->blockSequences[] = $blockSequence;
        }

        $this->cache->setItem($cacheId, $publishingOptions);

        return $publishingOptions;
    }

    /**
     * @param MenuItem $menuItem
     * @return \Boxspaced\EntityManager\Entity\AbstractEntity
     */
    protected function getContentByMenuItem(MenuItem $menuItem)
    {
        $route = $menuItem->getRoute();

        if (is_numeric($route->getIdentifier())) {

            $module = $route->getModule();

            $entityName = rtrim($module->getName(), 's');
            $entityName = ucfirst(StaticFilter::execute($entityName, DashToCamelCase::class));
            $entityName = str_replace(
                '##',
                $entityName,
                'Boxspaced\\Cms##Module\\Model\\##'
            );

            return $this->entityManager->find($entityName, $route->getIdentifier());
        }

        return null;
    }

    /**
     * @param int $id
     * @return ProvisionalLocation
     */
    public function getProvisionalLocation($id)
    {
        $item = $this->itemRepository->getById($id);

        if (null === $item) {
            throw new Exception\UnexpectedValueException('Unable to find an item with given ID');
        }

        if ($item->getProvisionalLocation()) {
            return ProvisionalLocation::createFromEntity($item->getProvisionalLocation());
        }

        return null;
    }

    /**
     * @param int $id
     * @return AvailableLocationOptions
     */
    public function getAvailableLocationOptions($id)
    {
        $item = $this->itemRepository->getById($id);

        if (null === $item) {
            throw new Exception\UnexpectedValueException('Unable to find an item with given ID');
        }

        $availableLocationOptions = new AvailableLocationOptions();

        // Available locations
        $availableToOptions = array(
            static::PUBLISH_TO_MENU => 'Menu',
            static::PUBLISH_TO_STANDALONE => 'Standalone',
        );

        // Check if has child menu items and can therefore go standalone
        foreach ($this->getFlattenedMenu() as $flattenedMenuItem) {

            $menuItemEntity = $flattenedMenuItem['item'];

            if ($this->getContentByMenuItem($menuItemEntity) !== $item) {
                continue;
            }

            if (count($menuItemEntity->getItems())) {
                unset($availableToOptions[static::PUBLISH_TO_STANDALONE]);
            }

            break;
        }

        foreach ($availableToOptions as $value => $label) {

            $toOption = new AvailableLocationOption();
            $toOption->value = $value;
            $toOption->label = $label;

            $availableLocationOptions->toOptions[] = $toOption;
        }

        // Available menu positions
        foreach ($this->getFlattenedMenu() as $flattenedMenuItem) {

            $menuItemEntity = $flattenedMenuItem['item'];
            $level = $flattenedMenuItem['level'];

            if ($this->getContentByMenuItem($menuItemEntity) === $item) {
                continue;
            }

            if ($level >= $this->config['menu']['max_menu_levels']) {
                continue;
            }

            $menuItemOption = new AvailableLocationOption();
            $menuItemOption->value = $menuItemEntity->getId();

            if ($menuItemEntity->getExternal()) {
                $menuItemOption->label = $menuItemEntity->getExternal();
            } else {
                $menuItemOption->label = $menuItemEntity->getRoute()->getSlug();
            }

            $menuItemOption->level = $level;

            $availableLocationOptions->beneathMenuItemOptions[] = $menuItemOption;
        }

        return $availableLocationOptions;
    }

    /**
     * @return AvailableColourSchemeOption[]
     */
    public function getAvailableColourSchemeOptions()
    {
        $colours = array(
            'dark-blue',
            'light-blue',
            'lime-green',
            'red',
        );

        $options = [];

        foreach ($colours as $colour) {

            $option = new AvailableColourSchemeOption();
            $option->value = $colour;
            $option->label = $colour;

            $options[] = $option;
        }

        return $options;
    }

    /**
     * @param string $name
     * @param int $typeId
     * @param ProvisionalLocation $provisionalLocation
     * @return int
     */
    public function createDraft(
        $name,
        $typeId,
        ProvisionalLocation $provisionalLocation = null
    )
    {
        $type = $this->itemTypeRepository->getById($typeId);

        if ($type === null) {
            throw new Exception\UnexpectedValueException('Unable to find type provided');
        }

        if ($provisionalLocation) {

            $to = $provisionalLocation->to;
            $beneathMenuItemId = $provisionalLocation->beneathMenuItemId;

            if (!in_array($to, array(
                static::PUBLISH_TO_MENU,
                static::PUBLISH_TO_STANDALONE,
            ))) {
                throw new Exception\UnexpectedValueException('Provisional location: \'to\' is invalid');
            }

            $provisionalLocation = $this->entityFactory->createEntity(ProvisionalLocationEntity::class);
            $provisionalLocation->setTo($to);
            $provisionalLocation->setBeneathMenuItemId($beneathMenuItemId);
        }

        $draft = $this->entityFactory->createEntity(Model\Item::class);
        $draft->setType($type);

        if ($provisionalLocation) {
            $draft->setProvisionalLocation($provisionalLocation);
        }

        $versionableDraft = new Model\VersionableItem($draft);
        $workflowableDraft = new Model\WorkflowableItem($draft);

        $this->versioningService->createDraft($versionableDraft, $this->user);
        $this->workflowService->moveToAuthoring($workflowableDraft);

        $this->entityManager->flush();

        $module = $this->moduleRepository->getByName(static::MODULE_NAME);

        $route = $this->entityFactory->createEntity(Route::class);
        $route->setSlug($name);
        $route->setIdentifier($draft->getId());

        $draft->setRoute($route);
        $module->addRoute($route);

        $this->entityManager->flush();

        return $draft->getId();
    }

    /**
     * @param int $id Published item's ID
     * @return int
     */
    public function createRevision($id)
    {
        $revisionOf = $this->itemRepository->getById($id);

        if (null === $revisionOf) {
            throw new Exception\UnexpectedValueException('Unable to find an item with given ID');
        }

        if ($revisionOf->getStatus() !== VersionableInterface::STATUS_PUBLISHED) {
            throw new Exception\UnexpectedValueException('The item you are creating a revision of must be published');
        }

        $revision = $this->entityFactory->createEntity(Model\Item::class);
        $revision->setType($revisionOf->getType());

        $versionableRevision = new Model\VersionableItem($revision);
        $versionableRevisionOf = new Model\VersionableItem($revisionOf);
        $workflowableRevision = new Model\WorkflowableItem($revision);

        $this->versioningService->createRevision($versionableRevision, $versionableRevisionOf, $this->user);
        $this->workflowService->moveToAuthoring($workflowableRevision);

        $this->entityManager->flush();

        return $revision->getId();
    }

    /**
     * @param int $id Draft or revision ID
     * @param Item $item
     * @param string $noteText
     * @return void
     */
    public function edit($id, Item $item, $noteText = '')
    {
        $itemEntity = $this->itemRepository->getById($id);

        if (null === $itemEntity) {
            throw new Exception\UnexpectedValueException('Unable to find item');
        }

        if (!in_array($itemEntity->getStatus(), array(
            VersionableInterface::STATUS_DRAFT,
            VersionableInterface::STATUS_REVISION,
        ))) {
            throw new Exception\UnexpectedValueException('You can only edit a draft or revision');
        }

        $itemEntity->deleteAllFields();
        $itemEntity->deleteAllParts();

        $itemEntity->setNavText($item->navText);
        $itemEntity->setTitle($item->title);
        $itemEntity->setMetaKeywords($item->metaKeywords);
        $itemEntity->setMetaDescription($item->metaDescription);

        foreach ($item->fields as $field) {

            $fieldEntity = $this->entityFactory->createEntity(Model\ItemField::class);
            $fieldEntity->setName($field->name);
            $fieldEntity->setValue($field->value);

            $itemEntity->addField($fieldEntity);
        }

        foreach ($item->parts as $key => $part) {

            $partEntity = $this->entityFactory->createEntity(Model\ItemPart::class);
            $partEntity->setOrderBy($key);

            $itemEntity->addPart($partEntity);

            foreach ($part->fields as $field) {

                $fieldEntity = $this->entityFactory->createEntity(Model\ItemPartField::class);
                $fieldEntity->setName($field->name);
                $fieldEntity->setValue($field->value);

                $partEntity->addField($fieldEntity);
            }
        }

        if ($noteText) {

            $noteEntity = $this->entityFactory->createEntity(Model\ItemNote::class);
            $noteEntity->setText($noteText);
            $noteEntity->setUser($this->user);
            $noteEntity->setCreatedTime(new DateTime());

            $itemEntity->addNote($noteEntity);
        }

        $this->entityManager->flush();
    }

    /**
     * @param int $id
     * @param PublishingOptions $options
     * @return void
     */
    public function publish($id, PublishingOptions $options = null)
    {
        $item = $this->itemRepository->getById($id);

        if (null === $item) {
            throw new Exception\UnexpectedValueException('Unable to find item');
        }

        if (null === $options && in_array($item->getStatus(), array(
            VersionableInterface::STATUS_PUBLISHED,
            VersionableInterface::STATUS_DRAFT,
        ))) {
            throw new Exception\UnexpectedValueException('Item status requires publishing options');
        }

        $versionableItem = new Model\VersionableItem($item);
        $workflowableItem = new Model\WorkflowableItem($item);

        switch ($item->getStatus()) {

            case VersionableInterface::STATUS_PUBLISHED:
            case VersionableInterface::STATUS_DRAFT:

                $item->getRoute()->setSlug($options->name);
                $item->setColourScheme($options->colourScheme);
                $item->setLiveFrom($options->liveFrom);
                $item->setExpiresEnd($options->expiresEnd);
                $item->setPublishedTo($options->to);

                $teaserTemplate = $this->itemTeaserTemplateRepository->getById($options->teaserTemplateId);
                $item->setTeaserTemplate($teaserTemplate);

                $template = $this->itemTemplateRepository->getById($options->templateId);
                $item->setTemplate($template);

                if (static::PUBLISH_TO_MENU === $options->to) {
                    $this->publishToMenu($item, $options);
                } else {
                    $this->publishToStandalone($item);
                }

                $this->applyBlockPublishingOptions($item, $options);

                if ($item->getStatus() === VersionableInterface::STATUS_DRAFT) {

                    if ($item->getProvisionalLocation()) {

                        $this->entityManager->delete($item->getProvisionalLocation());
                        $item->setProvisionalLocation(null);
                    }

                    $this->versioningService->publishDraft($versionableItem);
                    $this->workflowService->removeFromWorkflow($workflowableItem);
                }
                break;

            case VersionableInterface::STATUS_REVISION:

                $this->versioningService->publishRevision($versionableItem);
                $this->workflowService->removeFromWorkflow($workflowableItem);
                break;

            case VersionableInterface::STATUS_ROLLBACK:

                $this->versioningService->restoreRollback($versionableItem);
                break;

            case VersionableInterface::STATUS_DELETED:

                $this->versioningService->restoreDeleted($versionableItem);
                break;

            default:
                // No default
        }

        $this->entityManager->flush();

        // Clear caches
        $this->cache->removeItem(MenuService::MENU_CACHE_ID);
        $this->cache->removeItem(sprintf(static::CURRENT_PUBLISHING_OPTIONS_CACHE_ID, $id));
        $versionOf = $item->getVersionOf();
        if ($versionOf) {
            $this->cache->removeItem(sprintf(static::ITEM_CACHE_ID, $versionOf->getId()));
            $this->cache->removeItem(sprintf(static::ITEM_META_CACHE_ID, $versionOf->getId()));
        }
    }

    /**
     * @param Model\Item $item
     * @param PublishingOptions $options
     * @return ItemService
     */
    protected function publishToMenu(Model\Item $item, PublishingOptions $options)
    {
        if ($item->getStatus() === VersionableInterface::STATUS_PUBLISHED) {

            $currentOptions = $this->getCurrentPublishingOptions($item->getId());

            if (
                static::PUBLISH_TO_MENU === $currentOptions->to
                && $currentOptions->beneathMenuItemId == $options->beneathMenuItemId
            ) {
                // Already in correct position of menu
                return $this;
            }

            foreach ($this->getFlattenedMenu() as $flattenedMenuItem) {

                if ($this->getContentByMenuItem($flattenedMenuItem['item']) !== $item) {
                    continue;
                }

                // Already in menu but moving...
                $menuItem = $flattenedMenuItem['item'];

                if (!$options->beneathMenuItemId) {

                    // Move to top level
                    $menuItem->setParentMenuItem(null);
                    return $this;
                }

                // Move beneath an existing item
                $beneathMenuItem = null;
                foreach ($this->getFlattenedMenu() as $flattenedMenuItem) {

                    if ($flattenedMenuItem['item']->getId() == $options->beneathMenuItemId) {

                        $beneathMenuItem = $flattenedMenuItem['item'];
                        break;
                    }
                }

                if (null === $beneathMenuItem) {
                    throw new Exception\UnexpectedValueException('Unable to find menu item to put beneath');
                }

                $beneathMenuItem->addItem($menuItem);

                return $this;
            }
        }

        if ($options->beneathMenuItemId) {

            // Publish beneath an existing item
            $beneathMenuItem = null;
            foreach ($this->getFlattenedMenu() as $flattenedMenuItem) {

                $menuItem = $flattenedMenuItem['item'];

                if ($menuItem->getId() == $options->beneathMenuItemId) {

                    $beneathMenuItem = $menuItem;
                    break;
                }
            }

            if (null === $beneathMenuItem) {
                throw new Exception\UnexpectedValueException('Unable to find menu item to put beneath');
            }

            // Calculate order by
            $existingSubItems = $beneathMenuItem->getItems();

            if (count($existingSubItems)) {
                $orderBy = $existingSubItems->last()->getOrderBy() + 1;
            } else {
                $orderBy = 0;
            }

            $newMenuItem = $this->entityFactory->createEntity(MenuItem::class);
            $newMenuItem->setOrderBy($orderBy);
            $newMenuItem->setRoute($item->getRoute());

            $beneathMenuItem->addItem($newMenuItem);

            return $this;
        }

        // Publish at top level
        $menu = $this->menuRepository->getByName('main');

        // Calculate order by
        $existingTopLevel = $menu->getItems()->filter(
            function(MenuItem $menuItem) {
                return null === $menuItem->getParentMenuItem();
            }
        );
        if (count($existingTopLevel)) {
            $orderBy = $existingTopLevel->last()->getOrderBy() + 1;
        } else {
            $orderBy = 0;
        }

        $newMenuItem = $this->entityFactory->createEntity(MenuItem::class);
        $newMenuItem->setOrderBy($orderBy);
        $newMenuItem->setRoute($item->getRoute());

        $menu->addItem($newMenuItem);

        return $this;
    }

    /**
     * @param Model\Item $item
     * @return ItemService
     */
    protected function publishToStandalone(Model\Item $item)
    {
        foreach ($this->getFlattenedMenu() as $flattenedMenuItem) {

            $menuItem = $flattenedMenuItem['item'];

            if ($this->getContentByMenuItem($menuItem) !== $item) {
                continue;
            }

            // Cant make an item standalone if it has child items
            if (count($menuItem->getItems()) > 0) {
                throw new Exception\UnexpectedValueException('Item has child menu items');
            }

            if ($menuItem->getParentMenuItem()) {
                $menuItem->getParentMenuItem()->deleteItem($menuItem);
            } else {
                $menu = $this->menuRepository->getByName('main');
                $menu->deleteItem($menuItem);
            }
        }

        return $this;
    }

    /**
     * @param Model\Item $item
     * @param PublishingOptions $options
     * @return ItemService
     */
    protected function applyBlockPublishingOptions(Model\Item $item, PublishingOptions $options)
    {
        // Remove all blocks
        $item->deleteAllFreeBlocks();
        $item->deleteAllBlockSequences();

        $template = $this->itemTemplateRepository->getById($options->templateId);

        // Free blocks
        foreach ($options->freeBlocks as $freeBlock) {

            if (null === $freeBlock->id || null === $freeBlock->name) {
                continue;
            }

            $templateBlocks = $template->getBlocks()->filter(function($templateBlock) use ($freeBlock) {
                return $templateBlock->getName() === $freeBlock->name;
            });
            $templateBlock = $templateBlocks->first();

            $block = $this->blockRepository->getById($freeBlock->id);

            if (null === $templateBlock || null === $block) {
                $this->logger->warn('Ignoring block');
                continue;
            }

            $itemFreeBlock = $this->entityFactory->createEntity(Model\ItemFreeBlock::class);
            $itemFreeBlock->setTemplateBlock($templateBlock);
            $itemFreeBlock->setBlock($block);

            $item->addFreeBlock($itemFreeBlock);
        }

        // Block sequences
        foreach ($options->blockSequences as $blockSequence) {

            if (!$blockSequence->blocks) {
                continue;
            }

            $templateBlocks = $template->getBlocks()->filter(function($templateBlock) use ($blockSequence) {
                return $templateBlock->getName() === $blockSequence->name;
            });
            $templateBlock = $templateBlocks->first();

            if (null === $templateBlock) {
                $this->logger->warn('Ignoring block sequence');
                continue;
            }

            $itemBlockSequence = $this->entityFactory->createEntity(Model\ItemBlockSequence::class);
            $itemBlockSequence->setTemplateBlock($templateBlock);

            // Blocks
            foreach ($blockSequence->blocks as $blockSequenceBlock) {

                $block = $this->blockRepository->getById($blockSequenceBlock->id);

                if (null === $block) {
                    $this->logger->warn('Ignoring block sequence block');
                    continue;
                }

                $itemBlockSequenceBlock = $this->entityFactory->createEntity(Model\ItemBlockSequenceBlock::class);
                $itemBlockSequenceBlock->setOrderBy($blockSequenceBlock->orderBy);
                $itemBlockSequenceBlock->setBlock($block);

                $itemBlockSequence->addBlock($itemBlockSequenceBlock);
            }

            $item->addBlockSequence($itemBlockSequence);
        }

        return $this;
    }

    /**
     * @param int $id Published item's ID
     * @return void
     */
    public function delete($id)
    {
        $item = $this->itemRepository->getById($id);

        if (null === $item) {
            throw new Exception\UnexpectedValueException('Unable to find item');
        }

        if ($item->getStatus() !== VersionableInterface::STATUS_PUBLISHED) {
            throw new Exception\UnexpectedValueException('Item must be published');
        }

        // Check no child items
        foreach ($this->getFlattenedMenu() as $flattenedMenuItem) {

            $menuItem = $flattenedMenuItem['item'];

            if ($this->getContentByMenuItem($menuItem) !== $item) {
                continue;
            }

            if (count($menuItem->getItems()) > 0) {
                throw new Exception\UnexpectedValueException('Item has child menu items');
            }
        }

        $item->setTemplate(null);
        $item->setTeaserTemplate(null);
        $item->setLiveFrom(null);
        $item->setExpiresEnd(null);
        $item->setPublishedTo(null);

        if ($item->getRoute()) {
            $this->entityManager->delete($item->getRoute());
            $item->setRoute(null);
        }

        $versionableItem = new Model\VersionableItem($item);
        $this->versioningService->deletePublished($versionableItem);

        // Remove from menu
        foreach ($this->getFlattenedMenu() as $flattenedMenuItem) {

            $menuItem = $flattenedMenuItem['item'];

            if ($this->getContentByMenuItem($menuItem) !== $item) {
                continue;
            }

            if ($menuItem->getParentMenuItem()) {
                $menuItem->getParentMenuItem()->deleteItem($menuItem);
            } else {
                $menu = $this->menuRepository->getByName('main');
                $menu->deleteItem($menuItem);
            }
        }

        $versionsOf = $this->itemRepository->getAllVersionOf($item->getId());

        foreach ($versionsOf as $versionOf) {
            $this->itemRepository->delete($versionOf);
        }

        $this->entityManager->flush();

        // Clear cache
        $this->cache->removeItem(MenuService::MENU_CACHE_ID);
        $this->cache->removeItem(sprintf(static::CURRENT_PUBLISHING_OPTIONS_CACHE_ID, $id));
        $this->cache->removeItem(sprintf(static::ITEM_CACHE_ID, $id));
        $this->cache->removeItem(sprintf(static::ITEM_META_CACHE_ID, $id));
    }

    /**
     * @return array
     */
    protected function getFlattenedMenu()
    {
        $flattened = [];

        $menu = $this->menuRepository->getByName('main');

        // @todo this needs optimizing as menu items already come flattened from Menu::getItems
        $this->flattenMenuRecursive($menu->getItems()->filter(
            function(MenuItem $menuItem) {
                return null === $menuItem->getParentMenuItem();
            }
        ), $flattened);

        return $flattened;
    }

    /**
     * @param type $items
     * @param type $flattened
     * @param type $level
     * @return void
     */
    protected function flattenMenuRecursive(\Boxspaced\EntityManager\Collection\Collection $items, array &$flattened, $level = 1)
    {
        foreach ($items as $item) {

            $flattened[] = array(
                'item' => $item,
                'level' => $level,
            );

            if (count($item->getItems())) {
                $this->flattenMenuRecursive($item->getItems(), $flattened, $level+1);
            }
        }
    }

}
