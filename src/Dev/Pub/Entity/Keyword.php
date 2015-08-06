<?php
namespace Dev\Pub\Entity;


/**
 * @Entity
 */
class Keyword
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
    private $name;

    /**
     * @OneToMany(targetEntity="Dev\Pub\Entity\Serp", mappedBy="keyword")
     */
    private $serps;

    /**
     * @ManyToMany(targetEntity="Dev\Pub\Entity\Project", mappedBy="keywords", cascade={"persist", "remove"})
     */
    private $projects;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->serp = new \Doctrine\Common\Collections\ArrayCollection();
        $this->projects = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Keyword
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
     * Add project
     *
     * @param \Dev\Pub\Entity\Project $project
     *
     * @return Keyword
     */
    public function addProject(\Dev\Pub\Entity\Project $project)
    {
        $this->projects[] = $project;

        return $this;
    }

    /**
     * Remove project
     *
     * @param \Dev\Pub\Entity\Project $project
     */
    public function removeProject(\Dev\Pub\Entity\Project $project)
    {
        $this->projects->removeElement($project);
    }

    /**
     * Get projects
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProjects()
    {
        return $this->projects;
    }

    /**
     * Get serps
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSerps()
    {
        return $this->serps;
    }

    /**
     * Add serp
     *
     * @param \Dev\Pub\Entity\Serp $serp
     *
     * @return Keyword
     */
    public function addSerp(\Dev\Pub\Entity\Serp $serp)
    {
        $serp->setKeyword($this);
        $this->serps[] = $serp;

        return $this;
    }

    /**
     * Remove serp
     *
     * @param \Dev\Pub\Entity\Serp $serp
     */
    public function removeSerp(\Dev\Pub\Entity\Serp $serp)
    {
        $this->serps->removeElement($serp);
    }
}
