<?php
namespace Pms\FinanceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TransactionLine
 *
 * @ORM\Entity
 * @ORM\Table(name="fin_transaction_lines")
 */
class TransactionLine
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Transaction")
     * @ORM\JoinColumn(name="transaction_id", referencedColumnName="id", nullable=false)
     */
    protected $transaction;

    /**
     * @ORM\ManyToOne(targetEntity="Scope")
     * @ORM\JoinColumn(name="scope_id", referencedColumnName="id", nullable=false)
     */
    protected $scope;

    /**
     * @ORM\Column(type="float")
     */
    protected $value;

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
     * Set value
     *
     * @param float $value
     * @return TransactionLine
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return float 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set transaction
     *
     * @param \Pms\FinanceBundle\Entity\Transaction $transaction
     * @return TransactionLine
     */
    public function setTransaction(Transaction $transaction = null)
    {
        $this->transaction = $transaction;

        return $this;
    }

    /**
     * Get transaction
     *
     * @return \Pms\FinanceBundle\Entity\Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * Set scope
     *
     * @param \Pms\FinanceBundle\Entity\Scope $scope
     * @return TransactionLine
     */
    public function setScope(Scope $scope = null)
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
}
