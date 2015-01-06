<?php
# src/AppBundle/Service/Meat/Detector.php
namespace AppBundle\Service\Meat;

use Symfony\Component\HttpFoundation\RequestStack,
    Symfony\Component\HttpKernel\Exception\HttpException;

use DeviceDetector\DeviceDetector,
    DeviceDetector\Parser\OperatingSystem,
    DeviceDetector\Parser\Device\DeviceParserAbstract;

use AppBundle\Entity\Meat\Browser,
    AppBundle\Entity\Meat\BrowserVersion,
    AppBundle\Model\Meat\BrowserDetected;

class Detector
{
    const USER_ERROR_IS_BOT              = 'is_bot';
    const USER_ERROR_IS_MOBILE           = 'is_mobile';
    const USER_ERROR_NOT_BROWSER         = 'not_browser';
    const USER_ERROR_UNSUPPORTED_BROWSER = 'unsupported_browser';
    const USER_ERROR_UNSUPPORTED_OS      = 'unsupported_os';

    private $user_error = NULL;

    private $_request        = NULL;
    private $_deviceDetector = NULL;

    public function __construct(RequestStack $requestStack)
    {
        $this->_request = $requestStack->getCurrentRequest();

        $this->setDeviceDetector($this->_request);
    }

    public function getUserError()
    {
        return $this->user_error;
    }

    private function setDeviceDetector($_request)
    {
        DeviceParserAbstract::setVersionTruncation(DeviceParserAbstract::VERSION_TRUNCATION_NONE);

        $this->_deviceDetector = new DeviceDetector($_request->headers->get('User-Agent') );

        $this->_deviceDetector->discardBotInformation();
    }

    public function getDetectedDevice()
    {
        if( !($this->_deviceDetector instanceof DeviceDetector) )
            throw new HttpException(500, 'DeviceDetector is not set');

        $this->_deviceDetector->parse();

        if( $this->_deviceDetector->isBot() ) {
            $this->user_error = self::USER_ERROR_IS_BOT;
            return FALSE;
        }

        if( $this->_deviceDetector->isMobile() ) {
            $this->user_error = self::USER_ERROR_IS_MOBILE;
            return FALSE;
        }

        $clientInformation = $this->_deviceDetector->getClient();
        $osInformation     = $this->_deviceDetector->getOs();

        if( $clientInformation['type'] !== 'browser' ) {
            $this->user_error = self::USER_ERROR_NOT_BROWSER;
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

    public function getClientBrowser($browsers, $detectedDevice)
    {
        if( empty($detectedDevice['client']['name']) )
            throw new HttpException(500, 'Invalid parameter');

        $detectedDeviceClientName = $detectedDevice['client']['name'];

        foreach($browsers as $browser) {
            if( $this->isDetectedBrowser($browser, $detectedDeviceClientName) )
                return $browser;
        }

        $this->user_error = self::USER_ERROR_UNSUPPORTED_BROWSER;
        return FALSE;
    }

    private function isDetectedBrowser(Browser $browser, $detectedDeviceClientName)
    {
        return $browser->getUnpackedName() === $detectedDeviceClientName;
    }

    public function getClientBrowserVersion(Browser $clientBrowser, $detectedDevice)
    {
        if( empty($detectedDevice['os']['family']) )
            throw new HttpException(500, 'Invalid parameter');

        $detectedDeviceOsFamily = $detectedDevice['os']['family'];

        foreach($clientBrowser->getBrowserVersion() as $browserVersion) {
            if( $this->isDetectedBrowserVersion($browserVersion, $detectedDeviceOsFamily) )
                return $browserVersion;
        }

        $this->user_error = self::USER_ERROR_UNSUPPORTED_OS;
        return FALSE;
    }

    private function isDetectedBrowserVersion(BrowserVersion $browserVersion, $detectedDeviceOsFamily)
    {
        return $browserVersion->getName() === $detectedDeviceOsFamily;
    }

    public function isClientOutdated(BrowserVersion $clientBrowserVersion, $detectedDevice)
    {
        if( empty($detectedDevice['client']['version']) )
            throw new HttpException(500, 'Invalid parameter');

        $detectedDeviceClientVersion = $detectedDevice['client']['version'];
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

    public function getDetectedBrowser($browsers)
    {
        $browserDetected = new BrowserDetected;
        
        if( !($detectedDevice = $this->getDetectedDevice()) )
            return FALSE;

        $browserDetected->setClientVersion($detectedDevice['client']['version']);

        if( !(($clientBrowser = $this->getClientBrowser($browsers, $detectedDevice)) instanceof Browser) )
            return FALSE;

        $browserDetected->setBrowser($clientBrowser->getUnpackedName());
        $browserDetected->setVendor($clientBrowser->getUnpackedVendor());

        if( !(($clientBrowserVersion = $this->getClientBrowserVersion($clientBrowser, $detectedDevice)) instanceof BrowserVersion) )
            return FALSE;

        $browserDetected->setOsFamily($clientBrowserVersion->getName());
        $browserDetected->setStableVersion($clientBrowserVersion->getVersion());

        $isOutdated = $this->isClientOutdated($clientBrowserVersion, $detectedDevice);

        $browserDetected->setIsOutdated($isOutdated);

        return $browserDetected;
    }
}