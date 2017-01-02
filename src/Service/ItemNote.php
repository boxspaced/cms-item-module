<?php
namespace Item\Service;

use Item\Model\ItemNote as ItemNoteEntity;

class ItemNote
{

    /**
     *
     * @var string
     */
    public $username;

    /**
     *
     * @var string
     */
    public $time;

    /**
     *
     * @var string
     */
    public $text;

    /**
     * @param ItemNoteEntity $entity
     * @return ItemNote
     */
    public static function createFromEntity(ItemNoteEntity $entity)
    {
        $note = new static();

        $note->username = $entity->getUser()->getUsername();
        $note->text = $entity->getText();
        $note->time = $entity->getCreatedTime();

        return $note;
    }

}
