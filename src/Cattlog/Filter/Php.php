<?php namespace Cattlog\Filter;

class Php extends FilterAbstract
{
    /**
     * Encode key/value PHP array to desired text for writing to file
     * @param array $data PHP array to encode
     * @return string Encoded text for writing to file
     */
    public function encode($data)
    {
        return '<'.'?php' . PHP_EOL .
        PHP_EOL .
        'return ' . var_export($data, true) . ';';
    }

    /**
     * Decode encoded text to key/value PHP array
     * @param string $text Encoded text to convert to PHP key/value array
     * @return array Decoded data as PHP array
     */
    public function decode($text) // **test with empty array, doesn't break?
    {
        // Need a safe way of doing this, eval is evil! :)
        preg_match_all("/\'(.*)\' => \'(.*)\'/", $text, $matches);

        $keys = $matches[1];
        $values = $matches[2];

        // build data
        $data = array();
        foreach ($keys as $i => $key) {
            $data[$key] = $values[$i];
        }

        return $data;
    }
}
