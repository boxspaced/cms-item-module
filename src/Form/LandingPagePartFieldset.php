<?php
namespace Item\Form;

use Zend\Form\Element;
use Zend\Filter;

class LandingPagePartFieldset extends AbstractItemPartFieldset
{

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct($name);

        $element = new Element\Textarea('leftColumnBody');
        $element->setLabel('Left column');
        $element->setAttributes([
            'class' => 'wysiwyg',
            'rows' => 4,
            'cols' => 60,
        ]);
        $this->add($element);

        $element = new Element\Textarea('centreColumnBody');
        $element->setLabel('Centre column');
        $element->setAttributes([
            'class' => 'wysiwyg',
            'rows' => 4,
            'cols' => 60,
        ]);
        $this->add($element);
    }

    /**
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return parent::getInputFilterSpecification() + [
            'leftColumnBody' => [
                'allow_empty' => true,
                'filters' => [
                    ['name' => Filter\StringTrim::class],
                ],
            ],
            'centreColumnBody' => [
                'allow_empty' => true,
                'filters' => [
                    ['name' => Filter\StringTrim::class],
                ],
            ],
        ];
    }

}
