<?php

require_once "functions.php";

class ServiceWorker {

    private $_cache_name; /// Cache name
    private $_files; /// Files to cache
    private $_version; /// Cache version
    private $_base = 36;

    public function __construct($files = null, $cache_name = null) {
        if (@$files) {
            $this->_files = $files;
        } else {
            $this->_files = array(); // Default list of files
        }
        $this->_cache_name = @$cache_name ? $cache_name : parse_text($_SERVER["HTTP_HOST"]);
        $this->_version = 0;

        $this->_hashFiles();
        $this->_calculateVersion();
    }

    public function getFiles() {
        return $this->_files;
    }

    public function getCacheName() {
        return $this->_cache_name;
    }

    public function getVersion() {
        return $this->_version;
    }

    private function _hashFiles() {
        foreach ($this->_files as $cont => $file):
            // Check if the file exists. If it doesn't, we remove it from the list
            if (!file_exists($_SERVER["DOCUMENT_ROOT"] . '/' . $file)) {
                unset($this->_files[$cont]);
            } else {
                $this->_files[$cont] = h($file, $this->_version);
            }
        endforeach;
    }

    private function _calculateVersion() {
        // We version (which is an integer in base10) to base36
        $this->_version = base_convert($this->_version, 10, $this->_base);
    }
}