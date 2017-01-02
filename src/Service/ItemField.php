<?php
namespace Item\Service;

use Item\Model\ItemField as ItemFieldEntity;

class ItemField
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
    public $value;

    /**
     * @param ItemFieldEntity $entity
     * @return ItemField
     */
    public static function createFromEntity(ItemFieldEntity $entity)
    {
        $field = new static();

        $field->name = $entity->getName();
        $field->value = $entity->getValue();

        return $field;
    }

}
