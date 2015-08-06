<?php
namespace Dev\Pub\Entity;


/**
 * @Entity
 */
class SerpResult
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @Column(type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @Column(type="integer", nullable=true)
     */
    private $rank;

    /**
     * @Column(type="integer", nullable=true)
     */
    private $subrank;

    /**
     * @Column(type="string", length=255, nullable=true)
     */
    private $updated_time;

    /**
     * @Column(type="string", length=255, nullable=true)
     */
    private $site;

    /**
     * @ManyToOne(targetEntity="Dev\Pub\Entity\Serp", inversedBy="serpResults")
     */
    private $serp;

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
     * Set type
     *
     * @param string $type
     *
     * @return SerpResult
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return SerpResult
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return SerpResult
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return SerpResult
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set rank
     *
     * @param string $rank
     *
     * @return SerpResult
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank
     *
     * @return string
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set updatedTime
     *
     * @param \DateTime $updatedTime
     *
     * @return SerpResult
     */
    public function setUpdatedTime($updatedTime)
    {
        $this->updated_time = $updatedTime;

        return $this;
    }

    /**
     * Get updatedTime
     *
     * @return \DateTime
     */
    public function getUpdatedTime()
    {
        return $this->updated_time;
    }

    /**
     * Set site
     *
     * @param string $site
     *
     * @return SerpResult
     */
    public function setSite($site)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get site
     *
     * @return string
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Set serp
     *
     * @param \Dev\Pub\Entity\Serp $serp
     *
     * @return SerpResult
     */
    public function setSerp(\Dev\Pub\Entity\Serp $serp)
    {
        $this->serp = $serp;

        return $this;
    }

    /**
     * Get serp
     *
     * @return \Dev\Pub\Entity\Serp
     */
    public function getSerp()
    {
        return $this->serp;
    }

    /**
     * Set subrank
     *
     * @param \int $subrank
     *
     * @return SerpResult
     */
    public function setSubrank($subrank)
    {
        $this->subrank = $subrank;

        return $this;
    }

    /**
     * Get subrank
     *
     * @return \int
     */
    public function getSubrank()
    {
        return $this->subrank;
    }
}
