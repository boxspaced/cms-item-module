<?php
namespace Boxspaced\CmsItemModule;

use Boxspaced\EntityManager\Entity\AbstractEntity;
use Boxspaced\EntityManager\Mapper\Conditions;
use Boxspaced\EntityManagerModule\Mapper\ConditionsFactory;
use Zend\Router\Http\Segment;
use Boxspaced\CmsCoreModule\Model\RepositoryFactory;
use Boxspaced\CmsSlugModule\Model\Route;
use Boxspaced\CmsAccountModule\Model\User;
use Boxspaced\CmsCoreModule\Model\ProvisionalLocation;
use Zend\ServiceManager\Factory\InvokableFactory;
use Boxspaced\CmsBlockModule\Model\Block;
use Zend\Permissions\Acl\Acl;

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
    'acl' => [
        'resources' => [
            [
                'id' => Controller\ItemController::class,
            ],
        ],
        'rules' => [
            [
                'type' => Acl::TYPE_ALLOW,
                'roles' => 'guest',
                'resources' => Controller\ItemController::class,
                'privileges' => 'view',
            ],
            [
                'type' => Acl::TYPE_ALLOW,
                'roles' => 'author',
                'resources' => Controller\ItemController::class,
                'privileges' => [
                    'create',
                    'edit',
                ],
            ],
            [
                'type' => Acl::TYPE_ALLOW,
                'roles' => 'publisher',
                'resources' => Controller\ItemController::class,
                'privileges' => [
                    'publish',
                    'delete',
                    'publish-update',
                ],
            ],
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
                            'version_of' => 'version_of_id',
                            'type' => 'type_id',
                            'author' => 'author_id',
                            'provisional_location' => 'provisional_location_id',
                            'route' => 'route_id',
                            'template' => 'template_id',
                            'teaser_template' => 'teaser_template_id',
                        ],
                    ],
                ],
                'entity' => [
                    'fields' => [
                        'id' => [
                            'type' => AbstractEntity::TYPE_INT,
                        ],
                        'colour_scheme' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'nav_text' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'meta_keywords' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'meta_description' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'title' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'published_to' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'live_from' => [
                            'type' => AbstractEntity::TYPE_DATETIME,
                        ],
                        'expires_end' => [
                            'type' => AbstractEntity::TYPE_DATETIME,
                        ],
                        'workflow_stage' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'status' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'authored_time' => [
                            'type' => AbstractEntity::TYPE_DATETIME,
                        ],
                        'last_modified_time' => [
                            'type' => AbstractEntity::TYPE_DATETIME,
                        ],
                        'published_time' => [
                            'type' => AbstractEntity::TYPE_DATETIME,
                        ],
                        'rollback_stop_point' => [
                            'type' => AbstractEntity::TYPE_DATETIME,
                        ],
                        'version_of' => [
                            'type' => Model\Item::class,
                        ],
                        'type' => [
                            'type' => Model\ItemType::class,
                        ],
                        'author' => [
                            'type' => User::class,
                        ],
                        'provisional_location' => [
                            'type' => ProvisionalLocation::class,
                        ],
                        'route' => [
                            'type' => Route::class,
                        ],
                        'template' => [
                            'type' => Model\ItemTemplate::class,
                        ],
                        'teaser_template' => [
                            'type' => Model\ItemTeaserTemplate::class,
                        ],
                    ],
                    'one_to_many' => [
                        'fields' => [
                            'type' => Model\ItemField::class,
                            'conditions' => [
                                'factory' => ConditionsFactory::class,
                                'options' => [
                                    'constraints' => [
                                        [
                                            'field' => 'parent_item.id',
                                            'operation' => 'eq',
                                            'value' => ':id',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'parts' => [
                            'type' => Model\ItemPart::class,
                            'conditions' => [
                                'factory' => ConditionsFactory::class,
                                'options' => [
                                    'constraints' => [
                                        [
                                            'field' => 'parent_item.id',
                                            'operation' => 'eq',
                                            'value' => ':id',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'notes' => [
                            'type' => Model\ItemNote::class,
                            'conditions' => [
                                'factory' => ConditionsFactory::class,
                                'options' => [
                                    'constraints' => [
                                        [
                                            'field' => 'parent_item.id',
                                            'operation' => 'eq',
                                            'value' => ':id',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'free_blocks' => [
                            'type' => Model\ItemFreeBlock::class,
                            'conditions' => [
                                'factory' => ConditionsFactory::class,
                                'options' => [
                                    'constraints' => [
                                        [
                                            'field' => 'parent_item.id',
                                            'operation' => 'eq',
                                            'value' => ':id',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'block_sequences' => [
                            'type' => Model\ItemBlockSequence::class,
                            'conditions' => [
                                'factory' => ConditionsFactory::class,
                                'options' => [
                                    'constraints' => [
                                        [
                                            'field' => 'parent_item.id',
                                            'operation' => 'eq',
                                            'value' => ':id',
                                        ],
                                    ],
                                ],
                            ],
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
                        'multiple_parts' => [
                            'type' => AbstractEntity::TYPE_BOOL,
                        ],
                    ],
                    'one_to_many' => [
                        'templates' => [
                            'type' => Model\ItemTemplate::class,
                            'conditions' => [
                                'factory' => ConditionsFactory::class,
                                'options' => [
                                    'constraints' => [
                                        [
                                            'field' => 'for_type.id',
                                            'operation' => 'eq',
                                            'value' => ':id',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'teaser_templates' => [
                            'type' => Model\ItemTeaserTemplate::class,
                            'conditions' => [
                                'factory' => ConditionsFactory::class,
                                'options' => [
                                    'constraints' => [
                                        [
                                            'field' => 'for_type.id',
                                            'operation' => 'eq',
                                            'value' => ':id',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            Model\ItemField::class => [
                'mapper' => [
                    'params' => [
                        'table' => 'item_field',
                        'columns' => [
                            'parent_item' => 'item_id',
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
                        'parent_item' => [
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
                            'parent_item' => 'item_id',
                        ],
                    ],
                ],
                'entity' => [
                    'fields' => [
                        'id' => [
                            'type' => AbstractEntity::TYPE_INT,
                        ],
                        'order_by' => [
                            'type' => AbstractEntity::TYPE_INT,
                        ],
                        'parent_item' => [
                            'type' => Model\Item::class,
                        ],
                    ],
                    'one_to_many' => [
                        'fields' => [
                            'type' => Model\ItemPartField::class,
                            'conditions' => [
                                'factory' => ConditionsFactory::class,
                                'options' => [
                                    'constraints' => [
                                        [
                                            'field' => 'parent_part.id',
                                            'operation' => 'eq',
                                            'value' => ':id',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            Model\ItemPartField::class => [
                'mapper' => [
                    'params' => [
                        'table' => 'item_part_field',
                        'columns' => [
                            'parent_part' => 'part_id',
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
                        'parent_part' => [
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
                            'parent_item' => 'item_id',
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
                        'created_time' => [
                            'type' => AbstractEntity::TYPE_DATETIME,
                        ],
                        'parent_item' => [
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
                            'for_type' => 'for_type_id',
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
                        'view_script' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'description' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'for_type' => [
                            'type' => Model\ItemType::class,
                        ],
                    ],
                    'one_to_many' => [
                        'blocks' => [
                            'type' => Model\ItemTemplateBlock::class,
                            'conditions' => [
                                'factory' => ConditionsFactory::class,
                                'options' => [
                                    'constraints' => [
                                        [
                                            'field' => 'parent_template.id',
                                            'operation' => 'eq',
                                            'value' => ':id',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            Model\ItemTeaserTemplate::class => [
                'mapper' => [
                    'params' => [
                        'table' => 'item_teaser_template',
                        'columns' => [
                            'for_type' => 'for_type_id',
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
                        'view_script' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'description' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'for_type' => [
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
                            'parent_template' => 'template_id',
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
                        'admin_label' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'sequence' => [
                            'type' => AbstractEntity::TYPE_BOOL,
                        ],
                        'parent_template' => [
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
                            'parent_item' => 'item_id',
                            'template_block' => 'template_block_id',
                            'block' => 'block_id',
                        ],
                    ],
                ],
                'entity' => [
                    'fields' => [
                        'id' => [
                            'type' => AbstractEntity::TYPE_INT,
                        ],
                        'parent_item' => [
                            'type' => Model\Item::class,
                        ],
                        'template_block' => [
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
                            'parent_item' => 'item_id',
                            'template_block' => 'template_block_id',
                        ],
                    ],
                ],
                'entity' => [
                    'fields' => [
                        'id' => [
                            'type' => AbstractEntity::TYPE_INT,
                        ],
                        'parent_item' => [
                            'type' => Model\Item::class,
                        ],
                        'template_block' => [
                            'type' => Model\ItemTemplateBlock::class,
                        ],
                    ],
                    'one_to_many' => [
                        'blocks' => [
                            'type' => Model\ItemBlockSequenceBlock::class,
                            'conditions' => [
                                'factory' => ConditionsFactory::class,
                                'options' => [
                                    'constraints' => [
                                        [
                                            'field' => 'parent_block_sequence.id',
                                            'operation' => 'eq',
                                            'value' => ':id',
                                        ],
                                    ],
                                    'ordering' => [
                                        [
                                            'field' => 'order_by',
                                            'direction' => Conditions::ORDER_ASC,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            Model\ItemBlockSequenceBlock::class => [
                'mapper' => [
                    'params' => [
                        'table' => 'item_block_sequence_block',
                        'columns' => [
                            'parent_block_sequence' => 'block_sequence_id',
                            'block' => 'block_id',
                        ],
                    ],
                ],
                'entity' => [
                    'fields' => [
                        'id' => [
                            'type' => AbstractEntity::TYPE_INT,
                        ],
                        'order_by' => [
                            'type' => AbstractEntity::TYPE_INT,
                        ],
                        'parent_block_sequence' => [
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
