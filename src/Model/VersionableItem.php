<?php
namespace Item\Model;

use DateTime;
use Versioning\Model\VersionableInterface;
use Account\Model\User;

class VersionableItem implements VersionableInterface
{

    /**
     * @var Item
     */
    protected $item;

    /**
     * @param Item $item
     */
    public function __construct(Item $item)
    {
        $this->item = $item;
    }

    /**
     * @return Item
     */
    public function getAdaptee()
    {
        return $this->item;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->item->getStatus();
    }

    /**
     * @param string $status
     * @return VersionableItem
     */
    public function setStatus($status)
    {
        $this->item->setStatus($status);
        return $this;
    }

    /**
     * @return VersionableItem
     */
    public function getVersionOf()
    {
        if (null === $this->item->getVersionOf()) {
            return null;
        }

        return new static($this->item->getVersionOf());
    }

    /**
     * @param VersionableItem $versionOf
     * @return VersionableItem
     */
    public function setVersionOf(VersionableInterface $versionOf = null)
    {
        if ($versionOf instanceof $this) {
            $versionOf = $versionOf->getAdaptee();
        }

        $this->item->setVersionOf($versionOf);
        return $this;
    }

    /**
     * @return array
     */
    public function getVersioningTransferValues()
    {
        return $this->item->getVersioningTransferValues();
    }

    /**
     * @param array $values
     * @return VersionableItem
     */
    public function setVersioningTransferValues(array $values)
    {
        $this->item->setVersioningTransferValues($values);
        return $this;
    }

    /**
     * @param User $author
     * @return VersionableItem
     */
    public function setAuthor(User $author = null)
    {
        $this->item->setAuthor($author);
        return $this;
    }

    /**
     * @param DateTime $authoredTime
     * @return VersionableItem
     */
    public function setAuthoredTime(DateTime $authoredTime = null)
    {
        $this->item->setAuthoredTime($authoredTime);
        return $this;
    }

    /**
     * @param DateTime $publishedTime
     * @return VersionableItem
     */
    public function setPublishedTime(DateTime $publishedTime = null)
    {
        $this->item->setPublishedTime($publishedTime);
        return $this;
    }

    /**
     * @param DateTime $lastModifiedTime
     * @return VersionableItem
     */
    public function setLastModifiedTime(DateTime $lastModifiedTime = null)
    {
        $this->item->setLastModifiedTime($lastModifiedTime);
        return $this;
    }

    /**
     * @param DateTime $rollbackStopPoint
     * @return VersionableItem
     */
    public function setRollbackStopPoint(DateTime $rollbackStopPoint = null)
    {
        $this->item->setRollbackStopPoint($rollbackStopPoint);
        return $this;
    }

}
