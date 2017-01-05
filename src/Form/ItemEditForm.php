<?php
namespace Item\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use Workflow\Service\WorkflowService;
use Zend\Filter\StaticFilter;
use Item\Service;
use Zend\InputFilter\InputFilter;
use Zend\Validator;
use Zend\Filter;
use Item\Exception;

class ItemEditForm extends Form
{

    /**
     * @var int
     */
    protected $itemId;

    /**
     * @var Service\ItemService
     */
    protected $itemService;

    /**
     * @var WorkflowService
     */
    protected $workflowService;

    /**
     * @param string $name
     * @param int $itemId
     * @param Service\ItemService $itemService
     * @param WorkflowService $workflowService
     */
    public function __construct(
        $name,
        $itemId,
        Service\ItemService $itemService,
        WorkflowService $workflowService
    )
    {
        parent::__construct($name);
        $this->itemId = $itemId;
        $this->itemService = $itemService;
        $this->workflowService = $workflowService;

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

        $element = new Element\Hidden('id');
        $this->add($element);

        $element = new Element\Hidden('from');
        $this->add($element);

        $element = new Element\Hidden('partial');
        $this->add($element);

        $element = new Element\Hidden('selectedPart');
        $this->add($element);

        $element = new Element\Text('navText');
        $element->setLabel('Navigation text');
        $element->setOption('description', 'Navigation text\' appears in the menu (if published to menu) and the \'bread crumbs');
        $element->setAttribute('required', true);
        $this->add($element);

        $element = new Element\Text('title');
        $element->setLabel('Title');
        $element->setAttribute('required', true);
        $this->add($element);

        $element = new Element\Textarea('metaKeywords');
        $element->setLabel('Meta keywords');
        $element->setAttributes(array(
            'rows' => 4,
            'cols' => 60,
        ));
        $this->add($element);

        $element = new Element\Textarea('metaDescription');
        $element->setLabel('Meta description');
        $element->setAttributes(array(
            'rows' => 4,
            'cols' => 60,
        ));
        $this->add($element);

        $fieldsetClass = StaticFilter::execute($this->getName(), Filter\Word\DashToCamelCase::class);
        $fieldsetClass = sprintf('Application\\Form\\%sFieldset', $fieldsetClass);

        if (class_exists($fieldsetClass)) {
            $this->add(new $fieldsetClass('fields'));
        }

        $partFieldsetClass = StaticFilter::execute($this->getName(), Filter\Word\DashToCamelCase::class);
        $partFieldsetClass = sprintf('Application\\Form\\%sPartFieldset', $partFieldsetClass);

        if (class_exists($partFieldsetClass)) {

            $partFieldset = new $partFieldsetClass('part');

            if (!($partFieldset instanceof AbstractItemPartFieldset)) {
                throw new Exception\LogicException(sprintf('Part fieldset must extend: %s', AbstractItemPartFieldset::class));
            }

            $element = new Element\Collection('parts');
            $element->setCount(1);
            $element->setTargetElement($partFieldset);
            $this->add($element);
        }

        $element = new Element\Submit('preview');
        $element->setValue('Preview');
        $this->add($element);

        $templateValueOptions = $this->getTemplateValueOptions();

        $element = new Element\Select('previewTemplateId');
        $element->setValueOptions($templateValueOptions);
        $this->add($element);

        $element = new Element\Textarea('note');
        $element->setLabel('Add a note');
        $element->setAttributes(array(
            'rows' => 4,
            'cols' => 60,
        ));
        $this->add($element);

        $element = new Element\Submit('save');
        $element->setValue('Save');
        $this->add($element);

        $element = new Element\Submit('publish');
        $element->setValue('Publish');
        $this->add($element);
    }

    /**
     * @return ItemEditForm
     */
    protected function addInputFilter()
    {
        $inputFilter = new InputFilter();

        $inputFilter->add([
            'name' => 'id',
            'filters' => [
                ['name' => Filter\ToInt::class],
            ],
        ]);

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
            'name' => 'partial',
            'allow_empty' => true,
            'filters' => [
                ['name' => Filter\Boolean::class],
            ],
        ]);

        $inputFilter->add([
            'name' => 'selectedPart',
            'allow_empty' => true,
            'filters' => [
                ['name' => Filter\ToInt::class],
            ],
        ]);

        $inputFilter->add([
            'name' => 'numParts',
            'allow_empty' => true,
            'filters' => [
                ['name' => Filter\ToInt::class],
            ],
        ]);

        $inputFilter->add([
            'name' => 'navText',
            'filters' => [
                ['name' => Filter\StringTrim::class],
                ['name' => Filter\StripTags::class],
            ],
        ]);

        $inputFilter->add([
            'name' => 'title',
            'filters' => [
                ['name' => Filter\StringTrim::class],
                ['name' => Filter\StripTags::class],
            ],
        ]);

        $inputFilter->add([
            'name' => 'metaKeywords',
            'allow_empty' => true,
            'filters' => [
                ['name' => Filter\StringTrim::class],
                ['name' => Filter\StripTags::class],
            ],
        ]);

        $inputFilter->add([
            'name' => 'metaDescription',
            'allow_empty' => true,
            'filters' => [
                ['name' => Filter\StringTrim::class],
                ['name' => Filter\StripTags::class],
            ],
        ]);

        $inputFilter->add([
            'name' => 'note',
            'allow_empty' => true,
            'filters' => [
                ['name' => Filter\StringTrim::class],
                ['name' => Filter\StripTags::class],
            ],
        ]);

        $inputFilter->add([
            'name' => 'previewTemplateId',
            'allow_empty' => true,
        ]);

        return $this->setInputFilter($inputFilter);
    }

    /**
     * @return Service\ItemType
     */
    protected function getItemType()
    {
        $meta = $this->itemService->getItemMeta($this->itemId);
        return $this->itemService->getType($meta->typeId);
    }

    /**
     * @return array
     */
    protected function getTemplateValueOptions()
    {
        $type = $this->getItemType();

        $valueOptions = [];

        if ($this->workflowService->getStatus(Service\ItemService::MODULE_NAME, $this->itemId) === WorkflowService::WORKFLOW_STATUS_NEW) {

            foreach ($type->templates as $template) {
                $valueOptions[$template->id] = $template->name;
            }
        }

        return $valueOptions;
    }

    /**
     * @param Service\Item $item
     * @return AbstractItemBuilderForm
     */
    public function populateFromItem(Service\Item $item)
    {
        $values = (array) $item;

        $fields = $values['fields'];
        $parts = $values['parts'];

        $values['fields'] = [];
        $values['parts'] = [];

        foreach ($fields as $field) {
            $values['fields'][$field->name] = $field->value;
        }

        foreach ($parts as $key => $part) {

            $part = (array) $part;

            foreach ($part['fields'] as $partField) {
                $part[$partField->name] = $partField->value;

            }

            unset($part['fields']);

            $values['parts'][$key] = $part;
        }

        return parent::populateValues($values);
    }

}
