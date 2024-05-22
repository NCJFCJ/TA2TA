<?php

namespace IAWP\Utils;

use IAWPSCOPED\DeviceDetector\DeviceDetector;
use IAWPSCOPED\DeviceDetector\Parser\Client\Browser;
use IAWP\Illuminate_Builder;
use IAWP\Query;
use Throwable;
/** @internal */
class Device
{
    use \IAWP\Utils\Singleton;
    private $detector;
    private $type;
    private $os;
    private $browser;
    private function __construct()
    {
        $this->detector = new DeviceDetector($_SERVER['HTTP_USER_AGENT']);
        $cache = new \IAWP\Utils\Device_Cache();
        $cache->load_from_file();
        $this->detector->setCache($cache);
        try {
            @$this->detector->parse();
            $this->type = $this->detect_type($this->detector);
            $this->os = $this->detect_os($this->detector);
            $this->browser = $this->detect_browser($this->detector);
        } catch (Throwable $e) {
            $this->type = null;
            $this->os = null;
            $this->browser = null;
        }
        $cache->save_to_file();
    }
    public function type_id() : ?int
    {
        if (!\is_string($this->type) || $this->type === '') {
            return null;
        }
        $device_types_table = Query::get_table_name(Query::DEVICE_TYPES);
        $id = Illuminate_Builder::get_builder()->select('device_type_id')->from($device_types_table)->where('device_type', '=', $this->type)->value('device_type_id');
        if (\is_null($id)) {
            $id = Illuminate_Builder::get_builder()->from($device_types_table)->insertGetId(['device_type' => $this->type], 'device_type_id');
        }
        return $id;
    }
    public function os_id() : ?int
    {
        if (!\is_string($this->os) || $this->os === '') {
            return null;
        }
        $device_oss_table = Query::get_table_name(Query::DEVICE_OSS);
        $id = Illuminate_Builder::get_builder()->select('device_os_id')->from($device_oss_table)->where('device_os', '=', $this->os)->value('device_os_id');
        if (\is_null($id)) {
            $id = Illuminate_Builder::get_builder()->from($device_oss_table)->insertGetId(['device_os' => $this->os], 'device_os_id');
        }
        return $id;
    }
    public function browser_id() : ?int
    {
        if (!\is_string($this->browser) || $this->browser === '') {
            return null;
        }
        $device_browsers_table = Query::get_table_name(Query::DEVICE_BROWSERS);
        $id = Illuminate_Builder::get_builder()->select('device_browser_id')->from($device_browsers_table)->where('device_browser', '=', $this->browser)->value('device_browser_id');
        if (\is_null($id)) {
            $id = Illuminate_Builder::get_builder()->from($device_browsers_table)->insertGetId(['device_browser' => $this->browser], 'device_browser_id');
        }
        return $id;
    }
    public function is_bot() : bool
    {
        return $this->detector->isBot();
    }
    private function detect_os(DeviceDetector $detector) : ?string
    {
        $os = $detector->getOs('name');
        if ($os === "UNK") {
            return null;
        }
        return $os;
    }
    private function detect_type(DeviceDetector $detector) : ?string
    {
        $detected_type = $detector->getDeviceName();
        $type_mapping = ['Mobile' => ['smartphone', 'phablet'], 'Tablet' => ['tablet'], 'Desktop' => ['desktop'], 'Car' => ['car browser'], 'Console' => ['console'], 'TV' => ['tv'], 'Wearable' => ['wearable']];
        foreach ($type_mapping as $type => $types) {
            if (\in_array($detected_type, $types)) {
                return $type;
            }
        }
        return null;
    }
    private function detect_browser(DeviceDetector $detector) : ?string
    {
        $name = $detector->getClient('name');
        if ($name === 'UNK' || \is_array($name)) {
            return null;
        }
        if ($this->should_use_browser_name($name)) {
            return $name;
        }
        return \ucwords(Browser::getBrowserFamily($name) ?? $name);
    }
    /**
     * Should the browsers name be used instead of the browsers family name?
     *
     * @param string $name
     *
     * @return bool
     */
    private function should_use_browser_name(string $name) : bool
    {
        $exceptions = ['Microsoft Edge'];
        return \in_array($name, $exceptions);
    }
}
