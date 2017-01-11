<?php
namespace Boxspaced\CmsItemModule\Service;

use Boxspaced\CmsItemModule\Model\ItemPartField as ItemPartFieldEntity;

class ItemPartField
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
     * @param ItemPartFieldEntity $entity
     * @return ItemPartField
     */
    public static function createFromEntity(ItemPartFieldEntity $entity)
    {
        $field = new static();

        $field->name = $entity->getName();
        $field->value = $entity->getValue();

        return $field;
    }

}
