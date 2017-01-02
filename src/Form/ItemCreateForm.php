<?php
namespace Item\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use Item\Service\ItemService;
use Zend\InputFilter\InputFilter;
use Zend\Validator;
use Zend\Filter;

class ItemCreateForm extends Form
{

    /**
     * @var ItemService
     */
    protected $itemService;

    /**
     * @param ItemService $itemService
     * @param Database $db
     */
    public function __construct(ItemService $itemService)
    {
        parent::__construct('item-create');
        $this->itemService = $itemService;

        $this->setAttribute('method', 'post');
        $this->setAttribute('accept-charset', 'UTF-8');

        $this->addElements();
        $this->addInputFilter();
    }

    /**
     * @return void
     */
    protected function addElements()
    {
        $element = new Element\Csrf('token');
        $element->setCsrfValidatorOptions([
            'timeout' => 900,
        ]);
        $this->add($element);

        $element = new Element\Hidden('from');
        $this->add($element);

        $element = new Element\Hidden('provisionalTo');
        $this->add($element);

        $element = new Element\Hidden('provisionalBeneathMenuItemId');
        $this->add($element);

        $element = new Element\Text('name');
        $element->setLabel('Name');
        $element->setOption('description', '
            a-z, 0-9 and hyphens only<br><br>
            The name above will become the permanent link<br>
            to this item e.g. http://www.example.com/<span id="dynname"></span>');
        $element->setAttribute('required', true);
        $this->add($element);

        $element = new Element\Select('typeId');
        $element->setLabel('Type');
        $element->setEmptyOption('');
        $element->setValueOptions($this->getTypeValueOptions());
        $element->setAttribute('required', true);
        $this->add($element);

        $element = new Element\Submit('create');
        $element->setValue('Create item');
        $this->add($element);
    }

    /**
     * @return ItemCreateForm
     */
    protected function addInputFilter()
    {
        $inputFilter = new InputFilter();

        $inputFilter->add([
            'name' => 'from',
            'allow_empty' => true,
            'validators' => [
                [
                    'name' => Validator\InArray::class,
                    'options' => [
                        'haystack' => ['menu', 'standalone'],
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'provisionalTo',
            'allow_empty' => true,
            'validators' => [
                [
                    'name' => Validator\InArray::class,
                    'options' => [
                        'haystack' => ['Menu', 'Standalone'],
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'provisionalBeneathMenuItemId',
            'allow_empty' => true,
            'filters' => [
                ['name' => Filter\ToInt::class],
            ],
        ]);

        $inputFilter->add([
            'name' => 'name',
            'validators' => [
                [
                    'name' => Validator\Regex::class,
                    'break_chain_on_failure' => true,
                    'options' => [
                        'pattern' => '/^[a-z0-9-]+$/',
                    ],
                ],
                [
                    'name' => Validator\Callback::class,
                    'options' => [
                        'callback' => function($value, $context = []) {
                            return $this->itemService->isNameAvailable($value);
                        },
                        'messages' => [
                            Validator\Callback::INVALID_VALUE => 'The name is already in use',
                        ],
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'typeId',
        ]);

        return $this->setInputFilter($inputFilter);
    }

    /**
     * @return array
     */
    protected function getTypeValueOptions()
    {
        $valueOptions = [];

        foreach ($this->itemService->getTypes() as $type) {

            if (in_array($type->name, array(
                'home-page',
                'sitemap-page',
                'idea-stores-page',
            ))) {
                continue;
            }

            $valueOptions[$type->id] = $type->name;
        }

        return $valueOptions;
    }

}
