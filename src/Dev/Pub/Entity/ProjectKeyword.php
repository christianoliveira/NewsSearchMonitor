<?php
namespace Dev\Pub\Entity;

/**
 * @Entity
 */
class ProjectKeyword
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Manytoone(targetEntity="Dev\Pub\Entity\Project")
     */
    private $project;

    /**
     * @Manytoone(targetEntity="Dev\Pub\Entity\Keyword")
     */
    private $keyword;

  

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
     * Set project
     *
     * @param \Dev\Pub\Entity\Project $project
     *
     * @return ProjectKeyword
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

    /**
     * Set keyword
     *
     * @param \Dev\Pub\Entity\Keyword $keyword
     *
     * @return ProjectKeyword
     */
    public function setKeyword(\Dev\Pub\Entity\Keyword $keyword = null)
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
