<?php
namespace Item\Model;

use DateTime;
use Boxspaced\EntityManager\Entity\AbstractEntity;
use Boxspaced\EntityManager\Collection\Collection;
use Account\Model\User;
use Slug\Model\Route;
use Core\Model\ProvisionalLocation;

class Item extends AbstractEntity
{

    /**
     * @var array
     */
    protected $versioningTransferFields = array(
        'navText',
        'metaKeywords',
        'metaDescription',
        'title',
        'fields',
        'parts',
    );

    /**
     * @return int
     */
    public function getId()
    {
        return $this->get('id');
    }

    /**
     * @param int $id
     * @return Item
     */
    public function setId($id)
    {
        $this->set('id', $id);
        return $this;
    }

    /**
     * @return Item
     */
    public function getVersionOf()
    {
        return $this->get('versionOf');
    }

    /**
     * @param Item $versionOf
     * @return Item
     */
    public function setVersionOf(Item $versionOf)
    {
        $this->set('versionOf', $versionOf);
		return $this;
    }

    /**
     * @return ItemType
     */
    public function getType()
    {
        return $this->get('type');
    }

    /**
     * @param ItemType $type
     * @return Item
     */
    public function setType(ItemType $type)
    {
        $this->set('type', $type);
		return $this;
    }

    /**
     * @return Route
     */
    public function getRoute()
    {
        return $this->get('route');
    }

    /**
     * @param Route $route
     * @return Item
     */
    public function setRoute(Route $route = null)
    {
        $this->set('route', $route);
		return $this;
    }

    /**
     * @return ItemTemplate
     */
    public function getTemplate()
    {
        return $this->get('template');
    }

    /**
     * @param ItemTemplate $template
     * @return Item
     */
    public function setTemplate(ItemTemplate $template = null)
    {
        $this->set('template', $template);
		return $this;
    }

    /**
     * @return ItemTeaserTemplate
     */
    public function getTeaserTemplate()
    {
        return $this->get('teaserTemplate');
    }

    /**
     * @param ItemTeaserTemplate $teaserTemplate
     * @return Item
     */
    public function setTeaserTemplate(ItemTeaserTemplate $teaserTemplate = null)
    {
        $this->set('teaserTemplate', $teaserTemplate);
		return $this;
    }

    /**
     * @return string
     */
    public function getColourScheme()
    {
        return $this->get('colourScheme');
    }

    /**
     * @param string $colourScheme
     * @return Item
     */
    public function setColourScheme($colourScheme)
    {
        $this->set('colourScheme', $colourScheme);
		return $this;
    }

    /**
     * @return string
     */
    public function getNavText()
    {
        return $this->get('navText');
    }

    /**
     * @param string $navText
     * @return Item
     */
    public function setNavText($navText)
    {
        $this->set('navText', $navText);
		return $this;
    }

    /**
     * @return string
     */
    public function getMetaKeywords()
    {
        return $this->get('metaKeywords');
    }

    /**
     * @param string $metaKeywords
     * @return Item
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->set('metaKeywords', $metaKeywords);
		return $this;
    }

    /**
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->get('metaDescription');
    }

    /**
     * @param string $metaDescription
     * @return Item
     */
    public function setMetaDescription($metaDescription)
    {
        $this->set('metaDescription', $metaDescription);
		return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->get('title');
    }

    /**
     * @param string $title
     * @return Item
     */
    public function setTitle($title)
    {
        $this->set('title', $title);
		return $this;
    }

    /**
     * @return ProvisionalLocation
     */
    public function getProvisionalLocation()
    {
        return $this->get('provisionalLocation');
    }

    /**
     * @param ProvisionalLocation $provisionalLocation
     * @return Item
     */
    public function setProvisionalLocation(ProvisionalLocation $provisionalLocation = null)
    {
        $this->set('provisionalLocation', $provisionalLocation);
		return $this;
    }

    /**
     * @return string
     */
    public function getPublishedTo()
    {
        return $this->get('publishedTo');
    }

    /**
     * @param string $publishedTo
     * @return Item
     */
    public function setPublishedTo($publishedTo)
    {
        $this->set('publishedTo', $publishedTo);
		return $this;
    }

    /**
     * @return DateTime
     */
    public function getLiveFrom()
    {
        return $this->get('liveFrom');
    }

    /**
     * @param DateTime $liveFrom
     * @return Item
     */
    public function setLiveFrom(DateTime $liveFrom = null)
    {
        $this->set('liveFrom', $liveFrom);
		return $this;
    }

    /**
     * @return DateTime
     */
    public function getExpiresEnd()
    {
        return $this->get('expiresEnd');
    }

    /**
     * @param DateTime $expiresEnd
     * @return Item
     */
    public function setExpiresEnd(DateTime $expiresEnd = null)
    {
        $this->set('expiresEnd', $expiresEnd);
		return $this;
    }

    /**
     * @return string
     */
    public function getWorkflowStage()
    {
        return $this->get('workflowStage');
    }

    /**
     * @param string $workflowStage
     * @return Item
     */
    public function setWorkflowStage($workflowStage)
    {
        $this->set('workflowStage', $workflowStage);
		return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->get('status');
    }

    /**
     * @param string $status
     * @return Item
     */
    public function setStatus($status)
    {
        $this->set('status', $status);
		return $this;
    }

    /**
     * @return User
     */
    public function getAuthor()
    {
        return $this->get('author');
    }

    /**
     * @param User $author
     * @return Item
     */
    public function setAuthor(User $author = null)
    {
        $this->set('author', $author);
		return $this;
    }

    /**
     * @return DateTime
     */
    public function getAuthoredTime()
    {
        return $this->get('authoredTime');
    }

    /**
     * @param DateTime $authoredTime
     * @return Item
     */
    public function setAuthoredTime(DateTime $authoredTime = null)
    {
        $this->set('authoredTime', $authoredTime);
		return $this;
    }

    /**
     * @return DateTime
     */
    public function getLastModifiedTime()
    {
        return $this->get('lastModifiedTime');
    }

    /**
     * @param DateTime $lastModifiedTime
     * @return Item
     */
    public function setLastModifiedTime(DateTime $lastModifiedTime = null)
    {
        $this->set('lastModifiedTime', $lastModifiedTime);
		return $this;
    }

    /**
     * @return DateTime
     */
    public function getPublishedTime()
    {
        return $this->get('publishedTime');
    }

    /**
     * @param DateTime $publishedTime
     * @return Item
     */
    public function setPublishedTime(DateTime $publishedTime = null)
    {
        $this->set('publishedTime', $publishedTime);
		return $this;
    }

    /**
     * @return DateTime
     */
    public function getRollbackStopPoint()
    {
        return $this->get('rollbackStopPoint');
    }

    /**
     * @param DateTime $rollbackStopPoint
     * @return Item
     */
    public function setRollbackStopPoint(DateTime $rollbackStopPoint = null)
    {
        $this->set('rollbackStopPoint', $rollbackStopPoint);
		return $this;
    }

    /**
     * @return Collection
     */
    public function getFields()
    {
        return $this->get('fields');
    }

    /**
     * @param ItemField $field
     * @return Item
     */
    public function addField(ItemField $field)
    {
        $field->setParentItem($this);
        $this->getFields()->add($field);
		return $this;
    }

    /**
     * @param ItemField $field
     * @return Item
     */
    public function deleteField(ItemField $field)
    {
        $this->getFields()->delete($field);
		return $this;
    }

    /**
     * @return Item
     */
    public function deleteAllFields()
    {
        foreach ($this->getFields() as $field)
        {
            $this->deleteField($field);
        }
		return $this;
    }

    /**
     * @return Collection
     */
    public function getParts()
    {
        return $this->get('parts');
    }

    /**
     * @param ItemPart $part
     * @return Item
     */
    public function addPart(ItemPart $part)
    {
        $part->setParentItem($this);
        $this->getParts()->add($part);
		return $this;
    }

    /**
     * @param ItemPart $part
     * @return Item
     */
    public function deletePart(ItemPart $part)
    {
        $this->getParts()->delete($part);
		return $this;
    }

    /**
     * @return Item
     */
    public function deleteAllParts()
    {
        foreach ($this->getParts() as $part)
        {
            $this->deletePart($part);
        }
		return $this;
    }

    /**
     * @return Collection
     */
    public function getNotes()
    {
        return $this->get('notes');
    }

    /**
     * @param ItemNote $note
     * @return Item
     */
    public function addNote(ItemNote $note)
    {
        $note->setParentItem($this);
        $this->getNotes()->add($note);
		return $this;
    }

    /**
     * @param ItemNote $note
     * @return Item
     */
    public function deleteNote(ItemNote $note)
    {
        $this->getNotes()->delete($note);
		return $this;
    }

    /**
     * @return Item
     */
    public function deleteAllNotes()
    {
        foreach ($this->getNotes() as $note)
        {
            $this->deleteNote($note);
        }
		return $this;
    }

    /**
     * @return Collection
     */
    public function getFreeBlocks()
    {
        return $this->get('freeBlocks');
    }

    /**
     * @param ItemFreeBlock $freeBlock
     * @return Item
     */
    public function addFreeBlock(ItemFreeBlock $freeBlock)
    {
        $freeBlock->setParentItem($this);
        $this->getFreeBlocks()->add($freeBlock);
		return $this;
    }

    /**
     * @param ItemFreeBlock $freeBlock
     * @return Item
     */
    public function deleteFreeBlock(ItemFreeBlock $freeBlock)
    {
        $this->getFreeBlocks()->delete($freeBlock);
		return $this;
    }

    /**
     * @return Item
     */
    public function deleteAllFreeBlocks()
    {
        foreach ($this->getFreeBlocks() as $freeBlock)
        {
            $this->deleteFreeBlock($freeBlock);
        }
		return $this;
    }

    /**
     * @return Collection
     */
    public function getBlockSequences()
    {
        return $this->get('blockSequences');
    }

    /**
     * @param ItemBlockSequence $blockSequence
     * @return Item
     */
    public function addBlockSequence(ItemBlockSequence $blockSequence)
    {
        $blockSequence->setParentItem($this);
        $this->getBlockSequences()->add($blockSequence);
		return $this;
    }

    /**
     * @param ItemBlockSequence $blockSequence
     * @return Item
     */
    public function deleteBlockSequence(ItemBlockSequence $blockSequence)
    {
        $this->getBlockSequences()->delete($blockSequence);
		return $this;
    }

    /**
     * @return Item
     */
    public function deleteAllBlockSequences()
    {
        foreach ($this->getBlockSequences() as $blockSequence) {
            $this->deleteBlockSequence($blockSequence);
        }
		return $this;
    }

    /**
     * @return type
     */
    public function getVersioningTransferValues()
    {
        $values = [];

        foreach ($this->versioningTransferFields as $key) {
            $values[$key] = $this->get($key);
        }

        return $values;
    }

    /**
     * @param array $values
     * @return Item
     */
    public function setVersioningTransferValues(array $values)
    {
        foreach ($values as $field => $value) {

            if (!in_array($field, $this->versioningTransferFields)) {
                continue;
            }

            $this->set($field, $value);

            if ('fields' !== $field && 'parts' !== $field) {
                continue;
            }

            foreach ($value as $child) {
                $child->setParentItem($this);
            }
        }

		return $this;
    }

}
