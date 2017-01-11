<?php
namespace Boxspaced\CmsItemModule\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Form\Element;
use Zend\Filter;

abstract class AbstractItemPartFieldset extends Fieldset implements InputFilterProviderInterface
{

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct($name);

        $element = new Element\Hidden('orderBy');
        $this->add($element);

        $element = new Element\Hidden('delete');
        $this->add($element);
    }

    /**
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return [
            'orderBy' => [
                'allow_empty' => true,
                'filters' => [
                    ['name' => Filter\ToInt::class],
                ],
            ],
            'delete' => [
                'allow_empty' => true,
                'filters' => [
                    ['name' => Filter\Boolean::class],
                ],
            ],
        ];
    }

}
