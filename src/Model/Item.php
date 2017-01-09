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
        'nav_text',
        'meta_keywords',
        'meta_description',
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
        return $this->get('version_of');
    }

    /**
     * @param Item $versionOf
     * @return Item
     */
    public function setVersionOf(Item $versionOf)
    {
        $this->set('version_of', $versionOf);
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
        return $this->get('teaser_template');
    }

    /**
     * @param ItemTeaserTemplate $teaserTemplate
     * @return Item
     */
    public function setTeaserTemplate(ItemTeaserTemplate $teaserTemplate = null)
    {
        $this->set('teaser_template', $teaserTemplate);
		return $this;
    }

    /**
     * @return string
     */
    public function getColourScheme()
    {
        return $this->get('colour_scheme');
    }

    /**
     * @param string $colourScheme
     * @return Item
     */
    public function setColourScheme($colourScheme)
    {
        $this->set('colour_scheme', $colourScheme);
		return $this;
    }

    /**
     * @return string
     */
    public function getNavText()
    {
        return $this->get('nav_text');
    }

    /**
     * @param string $navText
     * @return Item
     */
    public function setNavText($navText)
    {
        $this->set('nav_text', $navText);
		return $this;
    }

    /**
     * @return string
     */
    public function getMetaKeywords()
    {
        return $this->get('meta_keywords');
    }

    /**
     * @param string $metaKeywords
     * @return Item
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->set('meta_keywords', $metaKeywords);
		return $this;
    }

    /**
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->get('meta_description');
    }

    /**
     * @param string $metaDescription
     * @return Item
     */
    public function setMetaDescription($metaDescription)
    {
        $this->set('meta_description', $metaDescription);
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
        return $this->get('provisional_location');
    }

    /**
     * @param ProvisionalLocation $provisionalLocation
     * @return Item
     */
    public function setProvisionalLocation(ProvisionalLocation $provisionalLocation = null)
    {
        $this->set('provisional_location', $provisionalLocation);
		return $this;
    }

    /**
     * @return string
     */
    public function getPublishedTo()
    {
        return $this->get('published_to');
    }

    /**
     * @param string $publishedTo
     * @return Item
     */
    public function setPublishedTo($publishedTo)
    {
        $this->set('published_to', $publishedTo);
		return $this;
    }

    /**
     * @return DateTime
     */
    public function getLiveFrom()
    {
        return $this->get('live_from');
    }

    /**
     * @param DateTime $liveFrom
     * @return Item
     */
    public function setLiveFrom(DateTime $liveFrom = null)
    {
        $this->set('live_from', $liveFrom);
		return $this;
    }

    /**
     * @return DateTime
     */
    public function getExpiresEnd()
    {
        return $this->get('expires_end');
    }

    /**
     * @param DateTime $expiresEnd
     * @return Item
     */
    public function setExpiresEnd(DateTime $expiresEnd = null)
    {
        $this->set('expires_end', $expiresEnd);
		return $this;
    }

    /**
     * @return string
     */
    public function getWorkflowStage()
    {
        return $this->get('workflow_stage');
    }

    /**
     * @param string $workflowStage
     * @return Item
     */
    public function setWorkflowStage($workflowStage)
    {
        $this->set('workflow_stage', $workflowStage);
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
        return $this->get('authored_time');
    }

    /**
     * @param DateTime $authoredTime
     * @return Item
     */
    public function setAuthoredTime(DateTime $authoredTime = null)
    {
        $this->set('authored_time', $authoredTime);
		return $this;
    }

    /**
     * @return DateTime
     */
    public function getLastModifiedTime()
    {
        return $this->get('last_modified_time');
    }

    /**
     * @param DateTime $lastModifiedTime
     * @return Item
     */
    public function setLastModifiedTime(DateTime $lastModifiedTime = null)
    {
        $this->set('last_modified_time', $lastModifiedTime);
		return $this;
    }

    /**
     * @return DateTime
     */
    public function getPublishedTime()
    {
        return $this->get('published_time');
    }

    /**
     * @param DateTime $publishedTime
     * @return Item
     */
    public function setPublishedTime(DateTime $publishedTime = null)
    {
        $this->set('published_time', $publishedTime);
		return $this;
    }

    /**
     * @return DateTime
     */
    public function getRollbackStopPoint()
    {
        return $this->get('rollback_stop_point');
    }

    /**
     * @param DateTime $rollbackStopPoint
     * @return Item
     */
    public function setRollbackStopPoint(DateTime $rollbackStopPoint = null)
    {
        $this->set('rollback_stop_point', $rollbackStopPoint);
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
        return $this->get('free_blocks');
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
        return $this->get('block_sequences');
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
