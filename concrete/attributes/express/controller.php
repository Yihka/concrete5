<?php
namespace Concrete\Attribute\Express;

use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Attribute\Controller as AttributeTypeController;
use Concrete\Core\Entity\Attribute\Key\Type\ExpressType;
use Concrete\Core\Entity\Attribute\Value\Value\ExpressValue;
use Doctrine\ORM\Query\Expr;

class Controller extends AttributeTypeController
{
    public $helpers = array('form');

    protected $searchIndexFieldDefinition = array('type' => 'integer', 'options' => array('notnull' => false));

    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('database');
    }

    public function saveKey($data)
    {
        /**
         * @var $type ExpressType
         */
        $type = $this->getAttributeKeyType();
        $id = $data['exEntityID'];
        $entity = $this->entityManager->getRepository('Concrete\Core\Entity\Express\Entity')
            ->findOneById($id);
        if (is_object($entity)) {
            $type->setEntity($entity);
        }
        return $type;
    }

    public function type_form()
    {
        $this->load();
    }

    public function form()
    {
        $entry = null;
        if ($this->attributeValue) {
            $value = $this->attributeValue->getValueObject();
            if (is_object($value)) {
                $entry = $value->getSelectedEntries()[0];
            }
        }
        $form_selector = $this->app->make('form/express/entry_selector');
        print $form_selector->selectEntry($this->getEntity(), $this->field('value'), $entry);
    }


    public function getSearchIndexValue()
    {
        $o = $this->attributeValue;
        if (is_object($o)) {
            $e = $o->getValue()->getSelectedEntries()[0];
            if (is_object($e)) {
                return $e->getID();
            }
        }
    }

    public function saveValue($entry)
    {
        $selected = array();
        if (!is_array($entry)) {
            $selected[] = $entry;
        } else {
            $selected = $entry;
        }
        $av = new ExpressValue();
        $av->setSelectedEntries($selected);
        return $av;
    }

    public function saveForm($data)
    {
        $entity = $this->entityManager->getRepository('Concrete\Core\Entity\Express\Entry')
            ->findOneById($data['value']);
        return $this->saveValue($entity);
    }

    protected function getEntity()
    {
        $type = $this->getAttributeKeyType();
        if (is_object($type)) {
            return $type->getEntity();
        }

    }
    protected function load()
    {
        $entityID = 0;
        $entities = array();
        $entity = $this->getEntity();
        if (is_object($entity)) {
            $entityID = $entity->getID();
        }
        $r = $this->entityManager->getRepository('Concrete\Core\Entity\Express\Entity')
            ->findAll();
        foreach ($r as $entity) {
            $entities[$entity->getID()] = $entity->getName();
        }
        $this->set('entityID', $entityID);
        $this->set('entities', $entities);
    }

    public function createAttributeKeyType()
    {
        return new ExpressType();
    }
}
