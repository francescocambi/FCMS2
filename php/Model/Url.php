<?php
/**
 * User: Francesco
 * Date: 29/09/14
 * Time: 15.12
 */

namespace Model;

/**
 * Class Url
 * @package Model
 * @Entity
 */
class Url {

    /**
     * @var string
     * @Id @Column(type="string", nullable=false)
     */
    protected $url;

    /**
     * @var Page
     * @ManyToOne(targetEntity="Page", inversedBy="pageUrls")
     */
    protected $page;

    /**
     * @param \Model\Page $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }

    /**
     * @return \Model\Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

} 