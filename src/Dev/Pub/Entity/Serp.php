<?php
namespace Dev\Pub\Entity;

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
     * @Column(type="text", nullable=true)
     */
    private $newsHtml;

    /**
     * @OneToMany(targetEntity="Dev\Pub\Entity\SerpResult", mappedBy="serp", cascade={"persist", "remove"},  fetch="EAGER")
     */
    private $serpResults;

    /**
     * @ManyToOne(targetEntity="Dev\Pub\Entity\Keyword", inversedBy="serps", fetch="EAGER")
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

    /**
     * Add serpResult
     *
     * @param \Dev\Pub\Entity\SerpResult $serpResult
     *
     * @return Serp
     */
    public function addSerpResult(\Dev\Pub\Entity\SerpResult $serpResult)
    {
        $serpResult->setSerp($this);
        $this->serpResults[] = $serpResult;

        return $this;
    }

    /**
     * Remove serpResult
     *
     * @param \Dev\Pub\Entity\SerpResult $serpResult
     */
    public function removeSerpResult(\Dev\Pub\Entity\SerpResult $serpResult)
    {
        $this->serpResults->removeElement($serpResult);
    }

    /**
     * Get serpResults
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSerpResults()
    {
        return $this->serpResults;
    }

    /**
     * Set newsHtml
     *
     * @param string $newsHtml
     *
     * @return Serp
     */
    public function setNewsHtml($newsHtml)
    {
        $this->newsHtml = $newsHtml;

        return $this;
    }

    /**
     * Get newsHtml
     *
     * @return string
     */
    public function getNewsHtml()
    {
        return $this->newsHtml;
    }
}
