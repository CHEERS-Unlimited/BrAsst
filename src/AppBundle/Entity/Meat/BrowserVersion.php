<?php
# src/AppBundle/Entity/Meat/BrowserVersion.php
namespace AppBundle\Entity\Meat;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\Meat\BrowserVersionRepository")
 * @ORM\Table(name="meat_browser_version")
 */
class BrowserVersion
{
    const SYSTEM_WINDOWS = "Windows";
    const SYSTEM_LINUX   = "Linux";
    const SYSTEM_MAC     = "Mac";

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $version;

    /**
     * @ORM\ManyToOne(targetEntity="Browser", inversedBy="browserVersion")
     * @ORM\JoinColumn(name="browser_id", referencedColumnName="id")
     */
    protected $browser;

    public static function getSystemList()
    {
        return [
            self::SYSTEM_WINDOWS,
            self::SYSTEM_LINUX,
            self::SYSTEM_MAC
        ];
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
     * @return BrowserVersion
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
     * Set version
     *
     * @param string $version
     * @return BrowserVersion
     */
    public function setVersion($version)
    {
        $this->version = $version;
    
        return $this;
    }

    /**
     * Get version
     *
     * @return string 
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set browser
     *
     * @param \AppBundle\Entity\Meat\Browser $browser
     * @return BrowserVersion
     */
    public function setBrowser(\AppBundle\Entity\Meat\Browser $browser = null)
    {
        $this->browser = $browser;
    
        return $this;
    }

    /**
     * Get browser
     *
     * @return \AppBundle\Entity\Meat\Browser 
     */
    public function getBrowser()
    {
        return $this->browser;
    }
}
