<?php
# src/AppBundle/Model/Meat/BrowserDetected.php
namespace AppBundle\Model\Meat;

class BrowserDetected
{
    protected $browser;

    protected $vendor;

    protected $osFamily;

    protected $stableVersion;

    protected $clientVersion;

    protected $isOutdated;

    public function setBrowser($browser)
    {
        $this->browser = $browser;
    }

    public function getBrowser()
    {
        return $this->browser;
    }

    public function setVendor($vendor)
    {
        $this->vendor = $vendor;
    }

    public function getVendor()
    {
        return $this->vendor;
    }

    public function setOsFamily($osFamily)
    {
        $this->osFamily = $osFamily;
    }

    public function getOsFamily()
    {
        return $this->osFamily;
    }

    public function setStableVersion($stableVersion)
    {
        $this->stableVersion = $stableVersion;
    }

    public function getStableVersion()
    {
        return $this->stableVersion;
    }

    public function setClientVersion($clientVersion)
    {
        $this->clientVersion = $clientVersion;
    }

    public function getClientVersion()
    {
        return $this->clientVersion;
    }

    public function setIsOutdated($isOutdated)
    {
        $this->isOutdated = $isOutdated;
    }

    public function getIsOutdated()
    {
        return $this->isOutdated;
    }
}