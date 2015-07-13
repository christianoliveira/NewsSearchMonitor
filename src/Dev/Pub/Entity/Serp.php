<?php
namespace Dev\Pub\Entity;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @Entity
 */
class Serp
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(type="datetime", nullable=true)
     */
    private $timestamp;

    /**
     * @Column(type="text", nullable=true)
     */
    private $html;

    /**
     * @OneToMany(targetEntity="Dev\Pub\Entity\SerpResult", mappedBy="serp")
     */
    private $serpResult;

    /**
     * @ManyToOne(targetEntity="Dev\Pub\Entity\Keyword", inversedBy="serp")
     * @JoinColumn(name="keyword_id", referencedColumnName="id", nullable=false)
     */
    private $keyword;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->serpResult = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set timestamp
     *
     * @param \DateTime $timestamp
     *
     * @return Serp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Get timestamp
     *
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Set html
     *
     * @param string $html
     *
     * @return Serp
     */
    public function setHtml($html)
    {
        $this->html = $html;

        return $this;
    }

    /**
     * Get html
     *
     * @return string
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * Add serpResult
     *
     * @param \Dev\Pub\Entity\SerpResult $serpResult
     *
     * @return Serp
     */
    public function addSerpResult(\Dev\Pub\Entity\SerpResult $serpResult)
    {
        $this->serpResult[] = $serpResult;

        return $this;
    }

    /**
     * Remove serpResult
     *
     * @param \Dev\Pub\Entity\SerpResult $serpResult
     */
    public function removeSerpResult(\Dev\Pub\Entity\SerpResult $serpResult)
    {
        $this->serpResult->removeElement($serpResult);
    }

    /**
     * Get serpResult
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSerpResult()
    {
        return $this->serpResult;
    }

    /**
     * Set keyword
     *
     * @param \Dev\Pub\Entity\Keyword $keyword
     *
     * @return Serp
     */
    public function setKeyword(\Dev\Pub\Entity\Keyword $keyword)
    {
        $this->keyword = $keyword;

        return $this;
    }

    /**
     * Get keyword
     *
     * @return \Dev\Pub\Entity\Keyword
     */
    public function getKeyword()
    {
        return $this->keyword;
    }
}
