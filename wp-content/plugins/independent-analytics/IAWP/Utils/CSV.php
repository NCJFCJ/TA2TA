<?php

namespace IAWP\Utils;

/** @internal */
class CSV
{
    private $header;
    private $rows;
    /**
     * @param array $header
     * @param array[] $rows
     */
    public function __construct(array $header, array $rows)
    {
        $this->header = $header;
        $this->rows = $rows;
    }
    public function to_string() : string
    {
        $delimiter = ',';
        $enclosure = '"';
        $escape_character = '\\';
        $f = \fopen('php://memory', 'r+');
        \fputcsv($f, $this->header, $delimiter, $enclosure, $escape_character);
        foreach ($this->rows as $row) {
            \fputcsv($f, $row, $delimiter, $enclosure, $escape_character);
        }
        \rewind($f);
        return \wp_kses(\stream_get_contents($f), 'strip');
    }
}
