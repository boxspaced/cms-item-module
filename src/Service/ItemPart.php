<?php
namespace Item\Service;

use Item\Model\ItemPart as ItemPartEntity;

class ItemPart
{

    /**
     *
     * @var int
     */
    public $orderBy;

    /**
     *
     * @var ItemField[]
     */
    public $fields = [];

    /**
     * @param ItemPartEntity $entity
     * @return ItemPart
     */
    public static function createFromEntity(ItemPartEntity $entity)
    {
        $part = new static();

        $part->orderBy = $entity->getOrderBy();

        foreach ($entity->getFields() as $field) {
            $part->fields[] = ItemPartField::createFromEntity($field);
        }

        return $part;
    }

}
