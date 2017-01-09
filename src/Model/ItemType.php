<?php
namespace Item\Model;

use Boxspaced\EntityManager\Entity\AbstractEntity;
use Boxspaced\EntityManager\Collection\Collection;

class ItemType extends AbstractEntity
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
     * @return ItemType
     */
    public function setId($id)
    {
        $this->set('id', $id);
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->get('name');
    }

    /**
     * @param string $name
     * @return ItemType
     */
    public function setName($name)
    {
        $this->set('name', $name);
		return $this;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->get('icon');
    }

    /**
     * @param string $icon
     * @return ItemType
     */
    public function setIcon($icon)
    {
        $this->set('icon', $icon);
		return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->get('description');
    }

    /**
     * @param string $description
     * @return ItemType
     */
    public function setDescription($description)
    {
        $this->set('description', $description);
		return $this;
    }

    /**
     * @return bool
     */
    public function getMultipleParts()
    {
        return $this->get('multiple_parts');
    }

    /**
     * @param bool $multipleParts
     * @return ItemType
     */
    public function setMultipleParts($multipleParts)
    {
        $this->set('multiple_parts', $multipleParts);
		return $this;
    }

    /**
     * @return Collection
     */
    public function getTemplates()
    {
        return $this->get('templates');
    }

    /**
     * @param ItemTemplate $template
     * @return ItemType
     */
    public function addTemplate(ItemTemplate $template)
    {
        $template->setParentType($this);
        $this->getTemplates()->add($template);
		return $this;
    }

    /**
     * @param ItemTemplate $template
     * @return ItemType
     */
    public function deleteTemplate(ItemTemplate $template)
    {
        $this->getTemplates()->delete($template);
		return $this;
    }

    /**
     * @return ItemType
     */
    public function deleteAllTemplates()
    {
        foreach ($this->getTemplates() as $template) {
            $this->deleteTemplate($template);
        }
		return $this;
    }

    /**
     * @return Collection
     */
    public function getTeaserTemplates()
    {
        return $this->get('teaser_templates');
    }

    /**
     * @param ItemTeaserTemplate $teaserTemplate
     * @return ItemType
     */
    public function addTeaserTemplate(ItemTeaserTemplate $teaserTemplate)
    {
        $teaserTemplate->setParentType($this);
        $this->getTeaserTemplates()->add($teaserTemplate);
		return $this;
    }

    /**
     * @param ItemTeaserTemplate $teaserTemplate
     * @return ItemType
     */
    public function deleteTeaserTemplate(ItemTeaserTemplate $teaserTemplate)
    {
        $this->getTeaserTemplates()->delete($teaserTemplate);
		return $this;
    }

    /**
     * @return ItemType
     */
    public function deleteAllTeaserTemplates()
    {
        foreach ($this->getTeaserTemplates() as $teaserTemplate) {
            $this->deleteTeaserTemplate($teaserTemplate);
        }
		return $this;
    }

}
