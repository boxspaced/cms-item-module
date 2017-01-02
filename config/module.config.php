<?php
namespace Item;

use Boxspaced\EntityManager\Entity\AbstractEntity;
use Boxspaced\EntityManager\Mapper\Conditions\Conditions;
use Zend\Router\Http\Segment;
use Core\Model\RepositoryFactory;
use Slug\Model\Route;
use Account\Model\User;
use Core\Model\ProvisionalLocation;
use Zend\ServiceManager\Factory\InvokableFactory;
use Block\Model\Block;

return [
    'item' => [
        'enable_meta_fields' => false,
    ],
    'router' => [
        'routes' => [
            // LIFO
            'item' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/item[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ItemController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            // LIFO
        ],
    ],
    'service_manager' => [
        'factories' => [
            Service\ItemService::class => Service\ItemServiceFactory::class,
            Model\ItemRepository::class => RepositoryFactory::class,
            Model\ItemTeaserTemplateRepository::class => RepositoryFactory::class,
            Model\ItemTemplateRepository::class => RepositoryFactory::class,
            Model\ItemTypeRepository::class => RepositoryFactory::class,
        ]
    ],
    'controllers' => [
        'aliases' => [
            'item' => Controller\ItemController::class,
        ],
        'factories' => [
            Controller\ItemController::class => Controller\ItemControllerFactory::class,
        ],
    ],
    'controller_plugins' => [
        'aliases' => [
            'itemBlocks' => Controller\Plugin\ItemBlocks::class,
            'itemAdminWidget' => Controller\Plugin\ItemAdminWidget::class,
        ],
        'factories' => [
            Controller\Plugin\ItemBlocks::class => InvokableFactory::class,
            Controller\Plugin\ItemAdminWidget::class => Controller\Plugin\ItemAdminWidgetFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'entity_manager' => [
        'types' => [
            Model\Item::class => [
                'mapper' => [
                    'params' => [
                        'table' => 'item',
                        'columns' => [
                            'versionOf' => 'version_of_id',
                            'type' => 'type_id',
                            'author' => 'author_id',
                            'provisionalLocation' => 'provisional_location_id',
                            'route' => 'route_id',
                            'template' => 'template_id',
                            'teaserTemplate' => 'teaser_template_id',
                        ],
                    ],
                ],
                'entity' => [
                    'fields' => [
                        'id' => [
                            'type' => AbstractEntity::TYPE_INT,
                        ],
                        'colourScheme' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'navText' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'metaKeywords' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'metaDescription' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'title' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'publishedTo' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'liveFrom' => [
                            'type' => AbstractEntity::TYPE_DATETIME,
                        ],
                        'expiresEnd' => [
                            'type' => AbstractEntity::TYPE_DATETIME,
                        ],
                        'workflowStage' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'status' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'authoredTime' => [
                            'type' => AbstractEntity::TYPE_DATETIME,
                        ],
                        'lastModifiedTime' => [
                            'type' => AbstractEntity::TYPE_DATETIME,
                        ],
                        'publishedTime' => [
                            'type' => AbstractEntity::TYPE_DATETIME,
                        ],
                        'rollbackStopPoint' => [
                            'type' => AbstractEntity::TYPE_DATETIME,
                        ],
                        'versionOf' => [
                            'type' => Model\Item::class,
                        ],
                        'type' => [
                            'type' => Model\ItemType::class,
                        ],
                        'author' => [
                            'type' => User::class,
                        ],
                        'provisionalLocation' => [
                            'type' => ProvisionalLocation::class,
                        ],
                        'route' => [
                            'type' => Route::class,
                        ],
                        'template' => [
                            'type' => Model\ItemTemplate::class,
                        ],
                        'teaserTemplate' => [
                            'type' => Model\ItemTeaserTemplate::class,
                        ],
                    ],
                    'children' => [
                        'fields' => [
                            'type' => Model\ItemField::class,
                            'conditions' => function ($id) {
                                return (new Conditions())
                                        ->field('parentItem.id')->eq($id);
                            },
                        ],
                        'parts' => [
                            'type' => Model\ItemPart::class,
                            'conditions' => function ($id) {
                                return (new Conditions())
                                        ->field('parentItem.id')->eq($id);
                            },
                        ],
                        'notes' => [
                            'type' => Model\ItemNote::class,
                            'conditions' => function ($id) {
                                return (new Conditions())
                                        ->field('parentItem.id')->eq($id);
                            },
                        ],
                        'freeBlocks' => [
                            'type' => Model\ItemFreeBlock::class,
                            'conditions' => function ($id) {
                                return (new Conditions())
                                        ->field('parentItem.id')->eq($id);
                            },
                        ],
                        'blockSequences' => [
                            'type' => Model\ItemBlockSequence::class,
                            'conditions' => function ($id) {
                                return (new Conditions())
                                        ->field('parentItem.id')->eq($id);
                            },
                        ],
                    ],
                ],
            ],
            Model\ItemType::class => [
                'mapper' => [
                    'params' => [
                        'table' => 'item_type',
                    ],
                ],
                'entity' => [
                    'fields' => [
                        'id' => [
                            'type' => AbstractEntity::TYPE_INT,
                        ],
                        'name' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'icon' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'description' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'multipleParts' => [
                            'type' => AbstractEntity::TYPE_BOOL,
                        ],
                    ],
                    'children' => [
                        'templates' => [
                            'type' => Model\ItemTemplate::class,
                            'conditions' => function ($id) {
                                return (new Conditions())
                                        ->field('forType.id')->eq($id);
                            },
                        ],
                        'teaserTemplates' => [
                            'type' => Model\ItemTeaserTemplate::class,
                            'conditions' => function ($id) {
                                return (new Conditions())
                                        ->field('forType.id')->eq($id);
                            },
                        ],
                    ],
                ],
            ],
            Model\ItemField::class => [
                'mapper' => [
                    'params' => [
                        'table' => 'item_field',
                        'columns' => [
                            'parentItem' => 'item_id',
                        ],
                    ],
                ],
                'entity' => [
                    'fields' => [
                        'id' => [
                            'type' => AbstractEntity::TYPE_INT,
                        ],
                        'name' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'value' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'parentItem' => [
                            'type' => Model\Item::class,
                        ],
                    ],
                ],
            ],
            Model\ItemPart::class => [
                'mapper' => [
                    'params' => [
                        'table' => 'item_part',
                        'columns' => [
                            'parentItem' => 'item_id',
                        ],
                    ],
                ],
                'entity' => [
                    'fields' => [
                        'id' => [
                            'type' => AbstractEntity::TYPE_INT,
                        ],
                        'orderBy' => [
                            'type' => AbstractEntity::TYPE_INT,
                        ],
                        'parentItem' => [
                            'type' => Model\Item::class,
                        ],
                    ],
                    'children' => [
                        'fields' => [
                            'type' => Model\ItemPartField::class,
                            'conditions' => function ($id) {
                                return (new Conditions())
                                        ->field('parentPart.id')->eq($id);
                            },
                        ],
                    ],
                ],
            ],
            Model\ItemPartField::class => [
                'mapper' => [
                    'params' => [
                        'table' => 'item_part_field',
                        'columns' => [
                            'parentPart' => 'part_id',
                        ],
                    ],
                ],
                'entity' => [
                    'fields' => [
                        'id' => [
                            'type' => AbstractEntity::TYPE_INT,
                        ],
                        'name' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'value' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'parentPart' => [
                            'type' => Model\ItemPart::class,
                        ],
                    ],
                ],
            ],
            Model\ItemNote::class => [
                'mapper' => [
                    'params' => [
                        'table' => 'item_note',
                        'columns' => [
                            'parentItem' => 'item_id',
                            'user' => 'user_id',
                        ],
                    ],
                ],
                'entity' => [
                    'fields' => [
                        'id' => [
                            'type' => AbstractEntity::TYPE_INT,
                        ],
                        'text' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'createdTime' => [
                            'type' => AbstractEntity::TYPE_DATETIME,
                        ],
                        'parentItem' => [
                            'type' => Model\Item::class,
                        ],
                        'user' => [
                            'type' => User::class,
                        ],
                    ],
                ],
            ],
            Model\ItemTemplate::class => [
                'mapper' => [
                    'params' => [
                        'table' => 'item_template',
                        'columns' => [
                            'forType' => 'for_type_id',
                        ],
                    ],
                ],
                'entity' => [
                    'fields' => [
                        'id' => [
                            'type' => AbstractEntity::TYPE_INT,
                        ],
                        'name' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'viewScript' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'description' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'forType' => [
                            'type' => Model\ItemType::class,
                        ],
                    ],
                    'children' => [
                        'blocks' => [
                            'type' => Model\ItemTemplateBlock::class,
                            'conditions' => function ($id) {
                                return (new Conditions())
                                        ->field('parentTemplate.id')->eq($id);
                            },
                        ],
                    ],
                ],
            ],
            Model\ItemTeaserTemplate::class => [
                'mapper' => [
                    'params' => [
                        'table' => 'item_teaser_template',
                        'columns' => [
                            'forType' => 'for_type_id',
                        ],
                    ],
                ],
                'entity' => [
                    'fields' => [
                        'id' => [
                            'type' => AbstractEntity::TYPE_INT,
                        ],
                        'name' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'viewScript' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'description' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'forType' => [
                            'type' => Model\ItemType::class,
                        ],
                    ],
                ],
            ],
            Model\ItemTemplateBlock::class => [
                'mapper' => [
                    'params' => [
                        'table' => 'item_template_block',
                        'columns' => [
                            'parentTemplate' => 'template_id',
                        ],
                    ],
                ],
                'entity' => [
                    'fields' => [
                        'id' => [
                            'type' => AbstractEntity::TYPE_INT,
                        ],
                        'name' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'adminLabel' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'sequence' => [
                            'type' => AbstractEntity::TYPE_BOOL,
                        ],
                        'parentTemplate' => [
                            'type' => Model\ItemTemplate::class,
                        ],
                    ],
                ],
            ],
            Model\ItemFreeBlock::class => [
                'mapper' => [
                    'params' => [
                        'table' => 'item_free_block',
                        'columns' => [
                            'parentItem' => 'item_id',
                            'templateBlock' => 'template_block_id',
                            'block' => 'block_id',
                        ],
                    ],
                ],
                'entity' => [
                    'fields' => [
                        'id' => [
                            'type' => AbstractEntity::TYPE_INT,
                        ],
                        'parentItem' => [
                            'type' => Model\Item::class,
                        ],
                        'templateBlock' => [
                            'type' => Model\ItemTemplateBlock::class,
                        ],
                        'block' => [
                            'type' => Block::class,
                        ],
                    ],
                ],
            ],
            Model\ItemBlockSequence::class => [
                'mapper' => [
                    'params' => [
                        'table' => 'item_block_sequence',
                        'columns' => [
                            'parentItem' => 'item_id',
                            'templateBlock' => 'template_block_id',
                        ],
                    ],
                ],
                'entity' => [
                    'fields' => [
                        'id' => [
                            'type' => AbstractEntity::TYPE_INT,
                        ],
                        'parentItem' => [
                            'type' => Model\Item::class,
                        ],
                        'templateBlock' => [
                            'type' => Model\ItemTemplateBlock::class,
                        ],
                    ],
                    'children' => [
                        'blocks' => [
                            'type' => Model\ItemBlockSequenceBlock::class,
                            'conditions' => function ($id) {
                                return (new Conditions())
                                        ->field('parentBlockSequence.id')->eq($id)
                                        ->order('orderBy', Conditions::ORDER_ASC);
                            },
                        ],
                    ],
                ],
            ],
            Model\ItemBlockSequenceBlock::class => [
                'mapper' => [
                    'params' => [
                        'table' => 'item_block_sequence_block',
                        'columns' => [
                            'parentBlockSequence' => 'block_sequence_id',
                            'block' => 'block_id',
                        ],
                    ],
                ],
                'entity' => [
                    'fields' => [
                        'id' => [
                            'type' => AbstractEntity::TYPE_INT,
                        ],
                        'orderBy' => [
                            'type' => AbstractEntity::TYPE_INT,
                        ],
                        'parentBlockSequence' => [
                            'type' => Model\ItemBlockSequence::class,
                        ],
                        'block' => [
                            'type' => Block::class,
                        ],
                    ],
                ],
            ],
        ],
    ],
];
