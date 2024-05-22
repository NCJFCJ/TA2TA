<?php

namespace IAWP;

/**
 * Reads a list of icons from a directory allowing them to be searched. Supports defaults.
 * @internal
 */
class Icon_Directory
{
    private $directory;
    private $alt_text;
    private $files;
    public function __construct(string $directory, string $alt_text)
    {
        $this->directory = $directory;
        $this->alt_text = $alt_text;
        $this->files = $this->get_files_in_directory($directory);
    }
    public function find(string $icon) : string
    {
        $icon_url = $this->find_icon_url($icon);
        if (\is_null($icon_url)) {
            return '';
        }
        return '<img class="flag" alt="' . \esc_attr($this->alt_text) . '" src="' . $icon_url . '"/>';
    }
    private function find_icon_url(string $icon) : ?string
    {
        $file_name = $this->convert_icon_name_to_file_name($icon);
        if (\in_array($file_name, $this->files)) {
            return $this->get_url_for_file($file_name);
        }
        if (\in_array('default.svg', $this->files)) {
            return $this->get_url_for_file('default.svg');
        }
        return null;
    }
    private function convert_icon_name_to_file_name(string $string) : string
    {
        return \str_replace([' ', '/'], '-', \strtolower($string)) . '.svg';
    }
    private function get_url_for_file(string $file_name) : string
    {
        return \IAWPSCOPED\iawp_url_to($this->directory . $file_name) . '?version=' . \IAWP_VERSION;
    }
    private function get_files_in_directory(string $directory) : array
    {
        $files = \scandir(\IAWPSCOPED\iawp_path_to($directory));
        if ($files === \false) {
            return [];
        }
        $files = \array_diff($files, ['..', '.']);
        $files = \array_values($files);
        return $files;
    }
}
