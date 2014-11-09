<?php
namespace Pms\FinanceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Account
 *
 * @ORM\Entity(repositoryClass="Pms\FinanceBundle\Repository\TransactionRepository")
 * @ORM\Table(name="fin_transaction")
 */
class Transaction
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="float")
     */
    protected $value;

    /**
     * @ORM\ManyToOne(targetEntity="Account")
     * @ORM\JoinColumn(name="source_account_id", referencedColumnName="id", nullable=true)
     */
    protected $sourceAccount;

    /**
     * @ORM\ManyToOne(targetEntity="Account")
     * @ORM\JoinColumn(name="destination_account_id", referencedColumnName="id", nullable=true)
     */
    protected $destinationAccount;

    /**
     * @ORM\ManyToOne(targetEntity="Scope")
     * @ORM\JoinColumn(name="scope_id", referencedColumnName="id", nullable=true)
     */
    protected $scope;

    /**
     * @ORM\Column(type="date")
     */
    protected $dateRecorded;

    /**
     * @ORM\Column(type="date")
     */
    protected $dateOccurred;

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
     * Set dateRecorded
     *
     * @param \DateTime $dateRecorded
     * @return Transaction
     */
    public function setDateRecorded($dateRecorded)
    {
        $this->dateRecorded = $dateRecorded;

        return $this;
    }

    /**
     * Get dateRecorded
     *
     * @return \DateTime 
     */
    public function getDateRecorded()
    {
        return $this->dateRecorded;
    }

    /**
     * Set dateOccurred
     *
     * @param \DateTime $dateOccurred
     * @return Transaction
     */
    public function setDateOccurred($dateOccurred)
    {
        $this->dateOccurred = $dateOccurred;

        return $this;
    }

    /**
     * Get dateOccurred
     *
     * @return \DateTime 
     */
    public function getDateOccurred()
    {
        return $this->dateOccurred;
    }

    /**
     * Set value
     *
     * @param integer $value
     * @return Transaction
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return integer 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return Transaction
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set scope
     *
     * @param \Pms\FinanceBundle\Entity\Scope $scope
     * @return Transaction
     */
    public function setScope($scope)
    {
        $this->scope = $scope;

        return $this;
    }

    /**
     * Get scope
     *
     * @return \Pms\FinanceBundle\Entity\Scope 
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * @param mixed $destinationAccount
     */
    public function setDestinationAccount($destinationAccount)
    {
        $this->destinationAccount = $destinationAccount;
    }

    /**
     * @return mixed
     */
    public function getDestinationAccount()
    {
        return $this->destinationAccount;
    }

    /**
     * @param mixed $sourceAccount
     */
    public function setSourceAccount($sourceAccount)
    {
        $this->sourceAccount = $sourceAccount;
    }

    /**
     * @return mixed
     */
    public function getSourceAccount()
    {
        return $this->sourceAccount;
    }


}
