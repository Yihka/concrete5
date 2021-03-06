<?php
namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Attribute\StandardSetManager;
use Concrete\Core\Entity\Attribute\Category;
use Doctrine\Common\Collections\ArrayCollection;
use Concrete\Core\Entity\Attribute\Type as AttributeType;

abstract class AbstractStandardCategory extends AbstractCategory implements StandardCategoryInterface
{
    protected $categoryEntity;

    public function setCategoryEntity(Category $category)
    {
        $this->categoryEntity = $category;
    }

    public function getCategoryEntity()
    {
        return $this->categoryEntity;
    }

    public function getSetManager()
    {
        if (!isset($this->setManager)) {
            $this->setManager = new StandardSetManager($this->categoryEntity, $this->entityManager);
        }
        return $this->setManager;
    }

    /**
     * @deprecated
     */
    public function addSet($handle, $name, $pkg = null, $locked = null)
    {
        $manager = $this->getSetManager();
        return $manager->addSet($handle, $name, $pkg, $locked);
    }

    public function getAttributeTypes()
    {
        return $this->getCategoryEntity()->getAttributeTypes();
    }

    public function associateAttributeKeyType(AttributeType $type)
    {
        /**
         * @var $types ArrayCollection
         */
        $types = $this->getCategoryEntity()->getAttributeTypes();
        if (!$types->contains($type)) {
            $types->add($type);
        }
        $this->entityManager->persist($this->getCategoryEntity());
        $this->entityManager->flush();
    }

    public function delete()
    {
        parent::delete();
        $this->entityManager->remove($this->getCategoryEntity());
        $this->entityManager->flush();
    }

}
