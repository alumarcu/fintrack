<?php
namespace Pms\FinanceBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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
     * @ORM\Column(type="date")
     */
    protected $dateRecorded;

    /**
     * @ORM\Column(type="date")
     */
    protected $dateOccurred;

    /**
     * @ORM\OneToMany(targetEntity="TransactionLine", mappedBy="transaction")
     */
    protected $lines;


    public function __construct()
    {
        $this->lines = new ArrayCollection();
    }

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
     * @param mixed $destinationAccount
     */
    public function setDestinationAccount($destinationAccount)
    {
        $this->destinationAccount = $destinationAccount;

        return $this;
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

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSourceAccount()
    {
        return $this->sourceAccount;
    }

    /**
     * @return mixed
     */
    public function getLines()
    {
        return $this->lines;
    }

}
