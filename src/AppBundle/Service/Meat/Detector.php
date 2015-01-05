<?php
# src/AppBundle/Service/Meat/Detector.php
namespace AppBundle\Service\Meat;

use Symfony\Component\HttpFoundation\RequestStack;

use DeviceDetector\DeviceDetector,
    DeviceDetector\Parser\OperatingSystem,
    DeviceDetector\Parser\Device\DeviceParserAbstract;

use AppBundle\Entity\Meat\Browser,
    AppBundle\Entity\Meat\BrowserVersion;

class Detector
{
    const USER_ERROR_IS_BOT              = 'is_bot';
    const USER_ERROR_IS_MOBILE           = 'is_mobile';
    const USER_ERROR_NOT_BROWSER         = 'not_browser';
    const USER_ERROR_UNSUPPORTED_BROWSER = 'unsupported_browser';
    const USER_ERROR_UNSUPPORTED_OS      = 'unsupported_os';

    private $_request        = NULL;
    private $_deviceDetector = NULL;

    public function __construct(RequestStack $requestStack)
    {
        $this->_request = $requestStack->getCurrentRequest();

        $this->setDeviceDetector($this->_request);

        //---

        /*DeviceParserAbstract::setVersionTruncation(DeviceParserAbstract::VERSION_TRUNCATION_NONE);

        $dd = new DeviceDetector($this->get('request')->headers->get('User-Agent') );

        // OPTIONAL: If called, getBot() will only return true if a bot was detected  (speeds up detection a bit)
        $dd->discardBotInformation();

        $dd->parse();

        if ($dd->isBot()) {
            // handle bots,spiders,crawlers,...
            $botInfo = $dd->getBot();
        } else {
            $clientInfo = $dd->getClient(); // holds information about browser, feed reader, media player, ...
            $osInfo = $dd->getOs();
            $device = $dd->getDevice();
            $brand = $dd->getBrand();
            $model = $dd->getModel();

            var_dump( OperatingSystem::getOsFamily($osInfo['short_name']) );

            var_dump(
                $clientInfo, $osInfo, $device, $brand, $model
            );
        }*/
    }

    private function setDeviceDetector($_request)
    {
        DeviceParserAbstract::setVersionTruncation(DeviceParserAbstract::VERSION_TRUNCATION_NONE);

        $this->_deviceDetector = new DeviceDetector($_request->headers->get('User-Agent') );

        $this->_deviceDetector->discardBotInformation();
    }

    public function getDetectedDevice()
    {
        if( !$this->_deviceDetector instanceof DeviceDetector )
            return FALSE;

        $this->_deviceDetector->parse();

        if( $this->_deviceDetector->isBot() )
            return self::USER_ERROR_IS_BOT;

        if( $this->_deviceDetector->isMobile() )
            return self::USER_ERROR_IS_MOBILE;

        $clientInformation = $this->_deviceDetector->getClient();
        $osInformation     = $this->_deviceDetector->getOs();

        if( $clientInformation['type'] !== 'browser' )
            return self::USER_ERROR_NOT_BROWSER;

        $osInformation['family'] = OperatingSystem::getOsFamily($osInformation['short_name']);

        if( !array_filter($clientInformation) || !array_filter($osInformation) )
            return FALSE;

        return [
            'client' => $clientInformation,
            'os'     => $osInformation
        ];
    }

    public function getClientBrowser($browsers, $detectedDevice)
    {
        if( empty($detectedDevice['client']['name']) )
            return FALSE;

        $detectedDeviceClientName = $detectedDevice['client']['name'];

        foreach($browsers as $browser) {
            if( $this->isDetectedBrowser($browser, $detectedDeviceClientName) )
                return $browser;
        }

        return self::USER_ERROR_UNSUPPORTED_BROWSER;
    }

    private function isDetectedBrowser(Browser $browser, $detectedDeviceClientName)
    {
        return $browser->getUnpackedName() === $detectedDeviceClientName;
    }

    public function getClientBrowserVersion(Browser $clientBrowser, $detectedDevice)
    {
        if( empty($detectedDevice['os']['family']) )
            return FALSE;

        $detectedDeviceOsFamily = $detectedDevice['os']['family'];

        foreach($clientBrowser->getBrowserVersion() as $browserVersion) {
            if( $this->isDetectedBrowserVersion($browserVersion, $detectedDeviceOsFamily) )
                return $browserVersion;
        }

        return self::USER_ERROR_UNSUPPORTED_OS;
    }

    private function isDetectedBrowserVersion(BrowserVersion $browserVersion, $detectedDeviceOsFamily)
    {
        return $browserVersion->getName() === $detectedDeviceOsFamily;
    }

    public function isClientOutdated(BrowserVersion $clientBrowserVersion, $detectedDevice)
    {
        if( empty($detectedDevice['client']['version']) )
            return FALSE;

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
}