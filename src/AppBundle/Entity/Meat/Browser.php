<?php
# src/AppBundle/Entity/Meat/Browser.php
namespace AppBundle\Entity\Meat;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\Meat\BrowserRepository")
 * @ORM\Table(name="meat_browsers")
 */
class Browser
{
    const VENDOR_GOOGLE    = "google";
    const VENDOR_MICROSOFT = "microsoft";
    const VENDOR_MOZILLA   = "mozilla_foundation";
    const VENDOR_OPERA     = "opera_software";
    const VENDOR_APPLE     = "apple_inc.";

    const BROWSER_CHROME   = "chrome";
    const BROWSER_EXPLORER = "internet_explorer";
    const BROWSER_FIREFOX  = "firefox";
    const BROWSER_OPERA    = "opera";
    const BROWSER_SAFARI   = "safari";

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $vendor;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $marketShare;

    /**
     * @ORM\OneToMany(targetEntity="BrowserVersion", mappedBy="browser")
     */
    protected $browserVersion;

    public static function getVendorList()
    {
        return [
            self::VENDOR_GOOGLE,
            self::VENDOR_MICROSOFT,
            self::VENDOR_MOZILLA,
            self::VENDOR_OPERA,
            self::VENDOR_APPLE
        ];
    }

    public static function getBrowserList()
    {
        return [
            self::BROWSER_CHROME,
            self::BROWSER_EXPLORER,
            self::BROWSER_FIREFOX,
            self::BROWSER_OPERA,
            self::BROWSER_SAFARI
        ];
    }

    public static function getVendorBrowserList()
    {
        return [
            [
                'vendor'  => self::VENDOR_GOOGLE,
                'browser' => self::BROWSER_CHROME
            ],
            [
                'vendor'  => self::VENDOR_MICROSOFT,
                'browser' => self::BROWSER_EXPLORER
            ],
            [
                'vendor'  => self::VENDOR_MOZILLA,
                'browser' => self::BROWSER_FIREFOX
            ],
            [
                'vendor'  => self::VENDOR_OPERA,
                'browser' => self::BROWSER_OPERA
            ],
            [
                'vendor'  => self::VENDOR_APPLE,
                'browser' => self::BROWSER_SAFARI
            ]
        ];
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->browserVersion = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set vendor
     *
     * @param string $vendor
     * @return Browser
     */
    public function setVendor($vendor)
    {
        $this->vendor = $vendor;
    
        return $this;
    }

    /**
     * Get vendor
     *
     * @return string 
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Browser
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

    public function getUnpackedName()
    {
        return implode(' ', array_map(
            'ucfirst',
            explode(' ', str_replace('_', ' ', $this->name))
        ));
    }

    /**
     * Set marketShare
     *
     * @param string $marketShare
     * @return Browser
     */
    public function setMarketShare($marketShare)
    {
        $this->marketShare = $marketShare;
    
        return $this;
    }

    /**
     * Get marketShare
     *
     * @return string 
     */
    public function getMarketShare()
    {
        return $this->marketShare;
    }

    /**
     * Add browserVersion
     *
     * @param \AppBundle\Entity\Meat\BrowserVersion $browserVersion
     * @return Browser
     */
    public function addBrowserVersion(\AppBundle\Entity\Meat\BrowserVersion $browserVersion)
    {
        $this->browserVersion[] = $browserVersion;
    
        return $this;
    }

    /**
     * Remove browserVersion
     *
     * @param \AppBundle\Entity\Meat\BrowserVersion $browserVersion
     */
    public function removeBrowserVersion(\AppBundle\Entity\Meat\BrowserVersion $browserVersion)
    {
        $this->browserVersion->removeElement($browserVersion);
    }

    /**
     * Get browserVersion
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBrowserVersion()
    {
        return $this->browserVersion;
    }
}
