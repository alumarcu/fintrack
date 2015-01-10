<?php
namespace Pms\FinanceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Account
 *
 * @ORM\Entity(repositoryClass="Pms\FinanceBundle\Repository\AccountRepository")
 * @ORM\Table(name="fin_accounts")
 */
class Account
{
    // TODO: Should belong with user preferences for the app!
    const DATE_FORMAT = 'd-m-Y';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $displayName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $bankName;

    /**
     * @ORM\Column(type="string", length=10)
     */
    protected $currency;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $isFavorite;

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
     * Set displayName
     *
     * @param string $displayName
     * @return Account
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;

        return $this;
    }

    /**
     * Get displayName
     *
     * @return string 
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Set bankName
     *
     * @param string $bankName
     * @return Account
     */
    public function setBankName($bankName)
    {
        $this->bankName = $bankName;

        return $this;
    }

    /**
     * Get bankName
     *
     * @return string 
     */
    public function getBankName()
    {
        return $this->bankName;
    }

    /**
     * Set currency
     *
     * @param string $currency
     * @return Account
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string 
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set isFavorite
     *
     * @param integer $isFavorite
     * @return Account
     */
    public function setIsFavorite($isFavorite)
    {
        $this->isFavorite = $isFavorite;

        return $this;
    }

    /**
     * Get isFavorite
     *
     * @return integer 
     */
    public function getIsFavorite()
    {
        return $this->isFavorite;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->displayName;
    }
}
