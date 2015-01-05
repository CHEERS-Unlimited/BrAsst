<?php
# src/AppBundle/Service/Meat/Detector.php
namespace AppBundle\Service\Meat;

use Symfony\Component\HttpFoundation\RequestStack;

use DeviceDetector\DeviceDetector,
    DeviceDetector\Parser\OperatingSystem,
    DeviceDetector\Parser\Device\DeviceParserAbstract;

class Detector
{
    const USER_ERROR_IS_BOT    = 'is_bot';
    const USER_ERROR_IS_MOBILE = 'is_mobile';

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

        $osInformation['family'] = OperatingSystem::getOsFamily($osInformation['short_name']);

        return [
            'client' => $clientInformation,
            'os'     => $osInformation
        ];
    }
}