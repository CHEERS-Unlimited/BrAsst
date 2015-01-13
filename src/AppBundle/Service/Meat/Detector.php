<?php
# src/AppBundle/Service/Meat/Detector.php
namespace AppBundle\Service\Meat;

use Symfony\Component\HttpKernel\Exception\HttpException;

use DeviceDetector\DeviceDetector,
    DeviceDetector\Parser\OperatingSystem,
    DeviceDetector\Parser\Device\DeviceParserAbstract;

use AppBundle\Entity\Meat\Browser,
    AppBundle\Entity\Meat\BrowserVersion,
    AppBundle\Model\Meat\BrowserDetected;

class Detector
{
    const USER_ERROR_IS_BOT              = 'user_error_is_bot';
    const USER_ERROR_IS_MOBILE           = 'user_error_is_mobile';
    const USER_ERROR_NOT_BROWSER         = 'user_error_not_browser';

    const USER_WARNING_UNSUPPORTED_BROWSER = 'user_warning_unsupported_browser';
    const USER_WARNING_UNSUPPORTED_OS      = 'user_warning_unsupported_os';

    private $userError = NULL;

    private $_deviceDetector = NULL;

    public function __construct()
    {
        DeviceParserAbstract::setVersionTruncation(DeviceParserAbstract::VERSION_TRUNCATION_NONE);
    }

    public function getUserError()
    {
        return $this->userError;
    }

    private function setDeviceDetector($httpUserAgent)
    {
        $this->_deviceDetector = new DeviceDetector($httpUserAgent);

        $this->_deviceDetector->discardBotInformation();
    }

    public function getDetectedDevice()
    {
        if( !($this->_deviceDetector instanceof DeviceDetector) )
            throw new HttpException(500, 'DeviceDetector is not set');

        $this->_deviceDetector->parse();

        if( $this->_deviceDetector->isBot() ) {
            $this->userError = self::USER_ERROR_IS_BOT;
            return FALSE;
        }

        if( $this->_deviceDetector->isMobile() ) {
            $this->userError = self::USER_ERROR_IS_MOBILE;
            return FALSE;
        }

        $clientInformation = $this->_deviceDetector->getClient();
        $osInformation     = $this->_deviceDetector->getOs();

        if( $clientInformation['type'] !== 'browser' ) {
            $this->userError = self::USER_ERROR_NOT_BROWSER;
            return FALSE;
        }

        $osInformation['family'] = OperatingSystem::getOsFamily($osInformation['short_name']);

        if( !array_filter($clientInformation) || !array_filter($osInformation) )
            throw new HttpException(500, 'DeviceDetector failed to complete task');

        return [
            'client' => $clientInformation,
            'os'     => $osInformation
        ];
    }

    public function getClientBrowser($browsers, BrowserDetected $browserDetected)
    {
        if( !($detectedDeviceClientName = $browserDetected->getBrowser()) )
            throw new HttpException(500, 'Invalid parameter');

        foreach($browsers as $browser) {
            if( $this->isDetectedBrowser($browser, $detectedDeviceClientName) )
                return $browser;
        }

        $browserDetected->setUserWarning(self::USER_WARNING_UNSUPPORTED_BROWSER);
        return $browserDetected;
    }

    private function isDetectedBrowser(Browser $browser, $detectedDeviceClientName)
    {
        return $browser->getUnpackedName() === $detectedDeviceClientName;
    }

    public function getClientBrowserVersion(Browser $clientBrowser, BrowserDetected $browserDetected)
    {
        if( !($detectedDeviceOsFamily = $browserDetected->getOsFamily()) )
            throw new HttpException(500, 'Invalid parameter');

        foreach($clientBrowser->getBrowserVersion() as $browserVersion) {
            if( $this->isDetectedBrowserVersion($browserVersion, $detectedDeviceOsFamily) )
                return $browserVersion;
        }

        $browserDetected->setUserWarning(self::USER_WARNING_UNSUPPORTED_OS);
        return $browserDetected;
    }

    private function isDetectedBrowserVersion(BrowserVersion $browserVersion, $detectedDeviceOsFamily)
    {
        return $browserVersion->getName() === $detectedDeviceOsFamily;
    }

    public function isClientOutdated(BrowserVersion $clientBrowserVersion, BrowserDetected $browserDetected)
    {
        if( !($detectedDeviceClientVersion = $browserDetected->getClientVersion()) )
            throw new HttpException(500, 'Invalid parameter');

        $detectedDeviceStableVersion = $clientBrowserVersion->getVersion();

        $currentClientVersion = explode('.', $detectedDeviceClientVersion);
        $currentStableVersion = explode('.', $detectedDeviceStableVersion);

        foreach($currentStableVersion as $position => $subVersion)
        {
            if( isset($currentClientVersion[$position]) )
            {
                if( $currentClientVersion[$position] < $subVersion ) {
                    return TRUE;
                }
            }
        }

        return FALSE;
    }

    public function getDetectedBrowser($httpUserAgent, $browsers)
    {
        $this->setDeviceDetector($httpUserAgent);

        $browserDetected = new BrowserDetected;

        if( !($detectedDevice = $this->getDetectedDevice()) )
            return FALSE;

        $browserDetected->setBrowser($detectedDevice['client']['name']);
        $browserDetected->setOsFamily($detectedDevice['os']['family']);
        $browserDetected->setClientVersion($detectedDevice['client']['version']);

        if( !(($result = $this->getClientBrowser($browsers, $browserDetected)) instanceof Browser) )
            //@return BrowserDetected
            return $result;
        else
            $clientBrowser = $result;

        $browserDetected->setVendor($clientBrowser->getUnpackedVendor());
        $browserDetected->setStableVersionLink($clientBrowser->getLink());

        if( !(($result = $this->getClientBrowserVersion($clientBrowser, $browserDetected)) instanceof BrowserVersion) )
            //@return BrowserDetected
            return $result;
        else
            $clientBrowserVersion = $result;

        $browserDetected->setStableVersion($clientBrowserVersion->getVersion());

        $isOutdated = $this->isClientOutdated($clientBrowserVersion, $browserDetected);

        $browserDetected->setIsOutdated($isOutdated);

        return $browserDetected;
    }
}