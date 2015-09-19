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
     * @Column(type="string", length=255)
     */
    private $name;

    /**
     * @OneToMany(targetEntity="Dev\Pub\Entity\Serp", mappedBy="keyword", fetch="EAGER")
     */
    private $serps;

    /**
     * @ManyToOne(targetEntity="Dev\Pub\Entity\Project", inversedBy="keywords", fetch="EAGER")
     */
    private $project;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->serps = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add serp
     *
     * @param \Dev\Pub\Entity\Serp $serp
     *
     * @return Keyword
     */
    public function addSerp(\Dev\Pub\Entity\Serp $serp)
    {
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
     * Set project
     *
     * @param \Dev\Pub\Entity\Project $project
     *
     * @return Keyword
     */
    public function setProject(\Dev\Pub\Entity\Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \Dev\Pub\Entity\Project
     */
    public function getProject()
    {
        return $this->project;
    }
}
