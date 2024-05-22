<?php

namespace IAWP\Utils;

use IAWPSCOPED\DeviceDetector\Cache\CacheInterface;
/** @internal */
class Device_Cache implements CacheInterface
{
    private $cache = [];
    private $dirty = \false;
    /**
     * @param string $id
     * @return mixed
     */
    public function fetch(string $id)
    {
        return $this->cache[$id] ?? null;
    }
    /**
     * @param string $id
     * @return bool
     */
    public function contains(string $id) : bool
    {
        return \array_key_exists($id, $this->cache);
    }
    /**
     * @param string $id
     * @param $data
     * @param int $lifeTime
     * @return bool
     */
    public function save(string $id, $data, int $lifeTime = 0) : bool
    {
        $this->dirty = \true;
        $this->cache[$id] = $data;
        return \true;
    }
    /**
     * @param string $id
     * @return bool
     */
    public function delete(string $id) : bool
    {
        $this->dirty = \true;
        unset($this->cache[$id]);
        return \true;
    }
    /**
     * @return bool
     */
    public function flushAll() : bool
    {
        $this->dirty = \true;
        $this->cache = [];
        return \true;
    }
    public function load_from_file()
    {
        if (!\file_exists($this->file())) {
            $this->cache = [];
            return;
        }
        $json = \file_get_contents($this->file());
        if ($json === \false) {
            $this->cache = [];
            return;
        }
        $data = \json_decode($json, \true);
        if ($data === null) {
            $this->cache = [];
            return;
        }
        $this->cache = $data;
    }
    public function save_to_file()
    {
        if ($this->dirty === \false) {
            return;
        }
        $this->dirty = \false;
        $contents = \json_encode($this->cache);
        if ($contents === \false) {
            \unlink($this->file());
            return;
        }
        $response = \file_put_contents($this->file(), $contents);
        if ($response === \false) {
            \wp_mkdir_p(\IAWPSCOPED\iawp_temp_path_to(''));
            \file_put_contents($this->file(), $contents);
        }
    }
    private function file() : string
    {
        return \IAWPSCOPED\iawp_temp_path_to('device-detector.json');
    }
}
