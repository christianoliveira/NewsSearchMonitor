<?php
namespace Dev\Pub\Entity;
use Doctrine\ORM\Mapping AS ORM;

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
    private $serp;

    /**
     * @ManyToMany(targetEntity="Dev\Pub\Entity\Project", inversedBy="keyword")
     * @JoinTable(
     *     name="Project_keyword",
     *     joinColumns={@JoinColumn(name="keyword_id", referencedColumnName="id", nullable=false)},
     *     inverseJoinColumns={@JoinColumn(name="project_id", referencedColumnName="id", nullable=false)}
     * )
     */
    private $project;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->serp = new \Doctrine\Common\Collections\ArrayCollection();
        $this->project = new \Doctrine\Common\Collections\ArrayCollection();
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
        $this->serp[] = $serp;

        return $this;
    }

    /**
     * Remove serp
     *
     * @param \Dev\Pub\Entity\Serp $serp
     */
    public function removeSerp(\Dev\Pub\Entity\Serp $serp)
    {
        $this->serp->removeElement($serp);
    }

    /**
     * Get serp
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSerp()
    {
        return $this->serp;
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
        $this->project[] = $project;

        return $this;
    }

    /**
     * Remove project
     *
     * @param \Dev\Pub\Entity\Project $project
     */
    public function removeProject(\Dev\Pub\Entity\Project $project)
    {
        $this->project->removeElement($project);
    }

    /**
     * Get project
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProject()
    {
        return $this->project;
    }
}
