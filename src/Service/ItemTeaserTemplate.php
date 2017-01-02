<?php
namespace Item\Service;

use Item\Model\ItemTeaserTemplate as ItemTeaserTemplateEntity;

class ItemTeaserTemplate
{

    /**
     *
     * @var int
     */
    public $id;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $viewScript;

    /**
     *
     * @var string
     */
    public $description;

    /**
     * @param ItemTeaserTemplateEntity $entity
     * @return ItemTeaserTemplate
     */
    public static function createFromEntity(ItemTeaserTemplateEntity $entity)
    {
        $template = new static();

        $template->id = (int) $entity->getId();
        $template->name = $entity->getName();
        $template->viewScript = $entity->getViewScript();
        $template->description = $entity->getDescription();

        return $template;
    }

}
