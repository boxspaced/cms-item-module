<?php
namespace Item\Model;

use DateTime;
use Boxspaced\EntityManager\Entity\AbstractEntity;
use Account\Model\User;

class ItemNote extends AbstractEntity
{

    /**
     * @return int
     */
    public function getId()
    {
        return $this->get('id');
    }

    /**
     * @param int $id
     * @return ItemNote
     */
    public function setId($id)
    {
        $this->set('id', $id);
        return $this;
    }

    /**
     * @return Item
     */
    public function getParentItem()
    {
        return $this->get('parentItem');
    }

    /**
     * @param Item $parentItem
     * @return ItemNote
     */
    public function setParentItem(Item $parentItem)
    {
        $this->set('parentItem', $parentItem);
		return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->get('text');
    }

    /**
     * @param string $text
     * @return ItemNote
     */
    public function setText($text)
    {
        $this->set('text', $text);
		return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->get('user');
    }

    /**
     * @param User $user
     * @return ItemNote
     */
    public function setUser(User $user)
    {
        $this->set('user', $user);
		return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedTime()
    {
        return $this->get('createdTime');
    }

    /**
     * @param DateTime $createdTime
     * @return ItemNote
     */
    public function setCreatedTime(DateTime $createdTime = null)
    {
        $this->set('createdTime', $createdTime);
		return $this;
    }

}
