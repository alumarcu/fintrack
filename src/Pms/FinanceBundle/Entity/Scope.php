<?php
namespace Pms\FinanceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Scope
 *
 * @ORM\Entity(repositoryClass="Pms\FinanceBundle\Repository\ScopeRepository")
 * @ORM\Table(name="fin_scopes")
 */
class Scope
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Scope
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->name;
    }

}
