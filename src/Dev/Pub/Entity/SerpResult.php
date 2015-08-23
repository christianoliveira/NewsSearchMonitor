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
     * @ManyToOne(targetEntity="Dev\Pub\Entity\Serp", inversedBy="serpResults", fetch="EAGER")
     */
    private $serp;

    /**
     * @Column(type="string", length=255, nullable=true)
     */
    private $UrlTitle;

    /**
     * @Column(type="string", length=255, nullable=true)
     */
    private $UrlH1;

    /**
     * @Column(type="string", length=255, nullable=true)
     */
    private $UrlH2;

    /**
     * @Column(type="string", length=255, nullable=true)
     */
    private $UrlDate;

     /**
     * @Column(type="string", length=255, nullable=true)
     */
    private $UrlDateIssued;

    /**
     * @Column(type="string", length=255, nullable=true)
     */
    private $UrlTextDate;

    /**
     * @Column(type="integer", nullable=true)
     */
    private $UrlCharacterCount;

    /**
     * @Column(type="integer", nullable=true)
     */
    private $UrlImageCount;

    /**
     * @Column(type="string", length=255, nullable=true)
     */
    private $UrlFirstImageAlt;

    /**
     * @Column(type="integer", nullable=true)
     */
    private $UrlOutLinksCount;

    /**
     * @Column(type="integer", nullable=true)
     */
    private $UrlInLinksCount;

    /**
     * @Column(type="integer", nullable=true)
     */
    private $UrlTweetCount;

    /**
     * @Column(type="integer", nullable=true)
     */
    private $UrlFbLikeCount;

    /**
     * @Column(type="integer", nullable=true)
     */
    private $UrlFbShareCount;

    /**
     * @Column(type="integer", nullable=true)
     */
    private $UrlFbCommentCount;

    /**
     * @Column(type="integer", nullable=true)
     */
    private $UrlFbTotalCount;

    /**
     * @Column(type="integer", nullable=true)
     */
    private $UrlPlusOneCount;

    /**
     * @Column(type="integer", nullable=true)
     */
    private $UrlMobileFriendly;

    /**
     * @Column(type="float", nullable=true)
     */
    private $UrlSpeedLoad;

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

    /**
     * Set urlTitle
     *
     * @param string $urlTitle
     *
     * @return SerpResult
     */
    public function setUrlTitle($urlTitle)
    {
        $this->UrlTitle = $urlTitle;

        return $this;
    }

    /**
     * Get urlTitle
     *
     * @return string
     */
    public function getUrlTitle()
    {
        return $this->UrlTitle;
    }

    /**
     * Set urlH1
     *
     * @param string $urlH1
     *
     * @return SerpResult
     */
    public function setUrlH1($urlH1)
    {
        $this->UrlH1 = $urlH1;

        return $this;
    }

    /**
     * Get urlH1
     *
     * @return string
     */
    public function getUrlH1()
    {
        return $this->UrlH1;
    }

    /**
     * Set urlH2
     *
     * @param string $urlH2
     *
     * @return SerpResult
     */
    public function setUrlH2($urlH2)
    {
        $this->UrlH2 = $urlH2;

        return $this;
    }

    /**
     * Get urlH2
     *
     * @return string
     */
    public function getUrlH2()
    {
        return $this->UrlH2;
    }

    /**
     * Set urlDate
     *
     * @param \DateTime $urlDate
     *
     * @return SerpResult
     */
    public function setUrlDate($urlDate)
    {
        $this->UrlDate = $urlDate;

        return $this;
    }

    /**
     * Get urlDate
     *
     * @return \DateTime
     */
    public function getUrlDate()
    {
        return $this->UrlDate;
    }

    /**
     * Set urlDateIssued
     *
     * @param \DateTime $urlDateIssued
     *
     * @return SerpResult
     */
    public function setUrlDateIssued($urlDateIssued)
    {
        $this->UrlDateIssued = $urlDateIssued;

        return $this;
    }

    /**
     * Get urlDateIssued
     *
     * @return \DateTime
     */
    public function getUrlDateIssued()
    {
        return $this->UrlDateIssued;
    }

    /**
     * Set urlTextDate
     *
     * @param \DateTime $urlTextDate
     *
     * @return SerpResult
     */
    public function setUrlTextDate($urlTextDate)
    {
        $this->UrlTextDate = $urlTextDate;

        return $this;
    }

    /**
     * Get urlTextDate
     *
     * @return \DateTime
     */
    public function getUrlTextDate()
    {
        return $this->UrlTextDate;
    }

    /**
     * Set urlCharacterCount
     *
     * @param integer $urlCharacterCount
     *
     * @return SerpResult
     */
    public function setUrlCharacterCount($urlCharacterCount)
    {
        $this->UrlCharacterCount = $urlCharacterCount;

        return $this;
    }

    /**
     * Get urlCharacterCount
     *
     * @return integer
     */
    public function getUrlCharacterCount()
    {
        return $this->UrlCharacterCount;
    }

    /**
     * Set urlImageCount
     *
     * @param integer $urlImageCount
     *
     * @return SerpResult
     */
    public function setUrlImageCount($urlImageCount)
    {
        $this->UrlImageCount = $urlImageCount;

        return $this;
    }

    /**
     * Get urlImageCount
     *
     * @return integer
     */
    public function getUrlImageCount()
    {
        return $this->UrlImageCount;
    }

    /**
     * Set urlFirstImageAlt
     *
     * @param string $urlFirstImageAlt
     *
     * @return SerpResult
     */
    public function setUrlFirstImageAlt($urlFirstImageAlt)
    {
        $this->UrlFirstImageAlt = $urlFirstImageAlt;

        return $this;
    }

    /**
     * Get urlFirstImageAlt
     *
     * @return string
     */
    public function getUrlFirstImageAlt()
    {
        return $this->UrlFirstImageAlt;
    }

    /**
     * Set urlOutLinksCount
     *
     * @param integer $urlOutLinksCount
     *
     * @return SerpResult
     */
    public function setUrlOutLinksCount($urlOutLinksCount)
    {
        $this->UrlOutLinksCount = $urlOutLinksCount;

        return $this;
    }

    /**
     * Get urlOutLinksCount
     *
     * @return integer
     */
    public function getUrlOutLinksCount()
    {
        return $this->UrlOutLinksCount;
    }

    /**
     * Set urlTweetCount
     *
     * @param integer $urlTweetCount
     *
     * @return SerpResult
     */
    public function setUrlTweetCount($urlTweetCount)
    {
        $this->UrlTweetCount = $urlTweetCount;

        return $this;
    }

    /**
     * Get urlTweetCount
     *
     * @return integer
     */
    public function getUrlTweetCount()
    {
        return $this->UrlTweetCount;
    }

    /**
     * Set urlFbLikeCount
     *
     * @param integer $urlFbLikeCount
     *
     * @return SerpResult
     */
    public function setUrlFbLikeCount($urlFbLikeCount)
    {
        $this->UrlFbLikeCount = $urlFbLikeCount;

        return $this;
    }

    /**
     * Get urlFbLikeCount
     *
     * @return integer
     */
    public function getUrlFbLikeCount()
    {
        return $this->UrlFbLikeCount;
    }

    /**
     * Set urlFbShareCount
     *
     * @param integer $urlFbShareCount
     *
     * @return SerpResult
     */
    public function setUrlFbShareCount($urlFbShareCount)
    {
        $this->UrlFbShareCount = $urlFbShareCount;

        return $this;
    }

    /**
     * Get urlFbShareCount
     *
     * @return integer
     */
    public function getUrlFbShareCount()
    {
        return $this->UrlFbShareCount;
    }

    /**
     * Set urlFbCommentCount
     *
     * @param integer $urlFbCommentCount
     *
     * @return SerpResult
     */
    public function setUrlFbCommentCount($urlFbCommentCount)
    {
        $this->UrlFbCommentCount = $urlFbCommentCount;

        return $this;
    }

    /**
     * Get urlFbCommentCount
     *
     * @return integer
     */
    public function getUrlFbCommentCount()
    {
        return $this->UrlFbCommentCount;
    }

    /**
     * Set urlFbTotalCount
     *
     * @param integer $urlFbTotalCount
     *
     * @return SerpResult
     */
    public function setUrlFbTotalCount($urlFbTotalCount)
    {
        $this->UrlFbTotalCount = $urlFbTotalCount;

        return $this;
    }

    /**
     * Get urlFbTotalCount
     *
     * @return integer
     */
    public function getUrlFbTotalCount()
    {
        return $this->UrlFbTotalCount;
    }

    /**
     * Set urlPlusOneCount
     *
     * @param integer $urlPlusOneCount
     *
     * @return SerpResult
     */
    public function setUrlPlusOneCount($urlPlusOneCount)
    {
        $this->UrlPlusOneCount = $urlPlusOneCount;

        return $this;
    }

    /**
     * Get urlPlusOneCount
     *
     * @return integer
     */
    public function getUrlPlusOneCount()
    {
        return $this->UrlPlusOneCount;
    }

    /**
     * Set urlMobileFriendly
     *
     * @param integer $urlMobileFriendly
     *
     * @return SerpResult
     */
    public function setUrlMobileFriendly($urlMobileFriendly)
    {
        $this->UrlMobileFriendly = $urlMobileFriendly;

        return $this;
    }

    /**
     * Get urlMobileFriendly
     *
     * @return integer
     */
    public function getUrlMobileFriendly()
    {
        return $this->UrlMobileFriendly;
    }

    /**
     * Set urlSpeedLoad
     *
     * @param float $urlSpeedLoad
     *
     * @return SerpResult
     */
    public function setUrlSpeedLoad($urlSpeedLoad)
    {
        $this->UrlSpeedLoad = $urlSpeedLoad;

        return $this;
    }

    /**
     * Get urlSpeedLoad
     *
     * @return float
     */
    public function getUrlSpeedLoad()
    {
        return $this->UrlSpeedLoad;
    }

    /**
     * Set urlInLinksCount
     *
     * @param integer $urlInLinksCount
     *
     * @return SerpResult
     */
    public function setUrlInLinksCount($urlInLinksCount)
    {
        $this->UrlInLinksCount = $urlInLinksCount;

        return $this;
    }

    /**
     * Get urlInLinksCount
     *
     * @return integer
     */
    public function getUrlInLinksCount()
    {
        return $this->UrlInLinksCount;
    }
}
