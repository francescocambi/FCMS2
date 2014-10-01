<?php

namespace Model;

/**
 * Class Block
 * @package Model
 * @Entity
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="blockType", type="string")
 */
abstract class Block {

    /**
     * @var int
     * @Id @Column(type="integer") @GeneratedValue
     */
    protected $id;

    /**
     * @var string
     * @Column(type="string", nullable=false, unique=true)
     */
    protected $name;

    /**
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $description;

    /**
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $bgurl;

    /**
     * @var int
     * @Column(type="integer", nullable=true)
     */
    protected $bgred;

    /**
     * @var int
     * @Column(type="integer", nullable=true)
     */
	protected $bggreen;

    /**
     * @var int
     * @Column(type="integer", nullable=true)
     */
	protected $bgblue;

    /**
     * @var decimal
     * @Column(type="decimal", precision=3, scale=2, nullable=true)
     */
    protected $bgopacity;

    /**
     * @var boolean
     * @Column(type="boolean", nullable=true)
     */
    protected $bgrepeatx;

    /**
     * @var boolean
     * @Column(type="boolean", nullable=true)
     */
    protected $bgrepeaty;

    /**
     * @var string
     * @Column(type="string", length=10, nullable=true)
     */
    protected $bgsize;

    /** Transient
     * @var /BlockStyle
     */
    protected $blockStyle;

    /**
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $blockStyleClassName;

    /**
     * @param int $bgblue
     */
    public function setBgblue($bgblue)
    {
        $this->bgblue = $bgblue;
    }

    /**
     * @return int
     */
    public function getBgblue()
    {
        return $this->bgblue;
    }

    /**
     * @param int $bggreen
     */
    public function setBggreen($bggreen)
    {
        $this->bggreen = $bggreen;
    }

    /**
     * @return int
     */
    public function getBggreen()
    {
        return $this->bggreen;
    }

    /**
     * @param float $bgopacity
     */
    public function setBgopacity($bgopacity)
    {
        $this->bgopacity = $bgopacity;
    }

    /**
     * @return float
     */
    public function getBgopacity()
    {
        return $this->bgopacity;
    }

    /**
     * @param int $bgred
     */
    public function setBgred($bgred)
    {
        $this->bgred = $bgred;
    }

    /**
     * @return int
     */
    public function getBgred()
    {
        return $this->bgred;
    }

    /**
     * @param boolean $bgrepeatx
     */
    public function setBgrepeatx($bgrepeatx)
    {
        $this->bgrepeatx = $bgrepeatx;
    }

    /**
     * @return boolean
     */
    public function getBgrepeatx()
    {
        return $this->bgrepeatx;
    }

    /**
     * @param boolean $bgrepeaty
     */
    public function setBgrepeaty($bgrepeaty)
    {
        $this->bgrepeaty = $bgrepeaty;
    }

    /**
     * @return boolean
     */
    public function getBgrepeaty()
    {
        return $this->bgrepeaty;
    }

    /**
     * @param string $bgsize
     */
    public function setBgsize($bgsize)
    {
        $this->bgsize = $bgsize;
    }

    /**
     * @return string
     */
    public function getBgsize()
    {
        return $this->bgsize;
    }

    /**
     * @param string $bgurl
     */
    public function setBgurl($bgurl)
    {
        $this->bgurl = $bgurl;
    }

    /**
     * @return string
     */
    public function getBgurl()
    {
        return $this->bgurl;
    }

    /**
     * @param \BlockStyle $blockStyle
     */
    public function setBlockStyle($blockStyle)
    {
        $this->blockStyle = $blockStyle;
        $this->blockStyleClassName = get_class($blockStyle);
    }

    /**
     * @return \BlockStyle
     */
    public function getBlockStyle()
    {
        if ( is_null($this->blockStyle) && !is_null($this->blockStyleClassName) ) {
            $classname = "\\".$this->blockStyleClassName;
            $this->blockStyle = new $classname;
        }
        return $this->blockStyle;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $blockStyleClassName
     */
    public function setBlockStyleClassName($blockStyleClassName)
    {
        if (!is_null($blockStyleClassName)) {
            $this->blockStyleClassName = $blockStyleClassName;
            $classname = "\\".$blockStyleClassName;
            $this->blockStyle = new $classname;
        }
    }

    /**
     * @return string
     */
    public function getBlockStyleClassName()
    {
        if (is_null($this->blockStyleClassName) && !is_null($this->blockStyle)) {
            $this->blockStyleClassName = get_class($this->blockStyle);
        }
        return $this->blockStyleClassName;
    }

    /**
     * @param \Request $request
     * @return string
     */
    public abstract function getHTML($request);

    /**
     * @return string
     */
    public function getBackgroundCSS() {
		$stringcss = "background:";
		if ($this->bgurl != "") {
            $stringcss .= " url('".$this->bgurl."')";
            if ($this->bgrepeatx == 1 && $this->bgrepeaty == 1) $stringcss .= " repeat";
            if ($this->bgrepeatx == 1 && $this->bgrepeaty == 0) $stringcss .= " repeat-x";
            if ($this->bgrepeatx == 0 && $this->bgrepeaty == 1) $stringcss .= " repeat-y";
            if ($this->bgrepeatx == 0 && $this->bgrepeaty == 0) $stringcss .= " no-repeat";
        }

		if ( !is_null($this->bgred) && !is_null($this->bggreen) && !is_null($this->bgblue) ) {
			$opacity = 1;
			if (!is_null($this->bgopacity) && $this->bgopacity <= 1)
				$opacity = $this->bgopacity;
			$stringcss .= " rgba($this->bgred, $this->bggreen, $this->bgblue, $opacity)";
		}
		
		if ($stringcss == "background:") {
			$stringcss = "";
		} else {
            $stringcss .= "; ";
		}
		
		if ($this->bgsize != "") $stringcss .= "background-size: $this->bgsize;";
		
		return $stringcss;
	}

}

?>