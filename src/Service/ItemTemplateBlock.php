<?php
namespace Boxspaced\CmsItemModule\Service;

use Boxspaced\CmsItemModule\Model\ItemTemplateBlock as ItemTemplateBlockEntity;

class ItemTemplateBlock
{

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $adminLabel;

    /**
     *
     * @var bool
     */
    public $sequence;

    /**
     * @param ItemTemplateBlockEntity $entity
     * @return ItemTemplateBlock
     */
    public static function createFromEntity(ItemTemplateBlockEntity $entity)
    {
        $templateBlock = new static();

        $templateBlock->name = $entity->getName();
        $templateBlock->adminLabel = $entity->getAdminLabel();
        $templateBlock->sequence = (bool) $entity->getSequence();

        return $templateBlock;
    }

}
