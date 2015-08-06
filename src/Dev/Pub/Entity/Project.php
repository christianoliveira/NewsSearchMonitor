<?php
namespace Dev\Pub\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Project
 *
 * @Table(name="Project")
 * @Entity()
 */
class Project
{
    /**
     * @Id
     * @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @Column(name="search_engine", type="string", length=255, nullable=true)
     */
    private $search_engine;

    /**
     * @Column(name="country", type="string", length=255, nullable=true)
     */
    private $country;

    /**
     * @Column(name="language", type="string", length=255, nullable=true)
     */
    private $language;

    /**
     * @Column(name="start_date", type="date", nullable=true)
     */
    private $start_date;

    /**
     * @Column(name="end_date", type="date", nullable=true)
     */
    private $end_date;

    /**
     * @ManyToMany(targetEntity="Dev\Pub\Entity\Keyword", inversedBy="projects", cascade={"persist", "remove"})
     * @JoinTable(name="Project_keyword")
     * 
     */
    private $keywords;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->keywords = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return Project
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
     * Set searchEngine
     *
     * @param string $searchEngine
     *
     * @return Project
     */
    public function setSearchEngine($searchEngine)
    {
        $this->search_engine = $searchEngine;

        return $this;
    }

    /**
     * Get searchEngine
     *
     * @return string
     */
    public function getSearchEngine()
    {
        return $this->search_engine;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return Project
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set language
     *
     * @param string $language
     *
     * @return Project
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return Project
     */
    public function setStartDate($startDate)
    {
        $this->start_date = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->start_date;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     *
     * @return Project
     */
    public function setEndDate($endDate)
    {
        $this->end_date = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->end_date;
    }

    /**
     * Add keyword
     *
     * @param \Dev\Pub\Entity\Keyword $keyword
     *
     * @return Project
     */
    public function addKeyword(\Dev\Pub\Entity\Keyword $keyword)
    {
        $keyword->addProject($this);
        $this->keywords[] = $keyword;

        return $this;
    }

    /**
     * Remove keyword
     *
     * @param \Dev\Pub\Entity\Keyword $keyword
     */
    public function removeKeyword(\Dev\Pub\Entity\Keyword $keyword)
    {
        $this->keywords->removeElement($keyword);
    }

    /**
     * Get keywords
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getKeywords()
    {
        return $this->keywords;
    }
}
