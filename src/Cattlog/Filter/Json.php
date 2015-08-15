<?php namespace Cattlog\Filter;

class Json extends FilterAbstract
{
    /**
     * Encode key/value PHP array to desired text for writing to file
     * @param array $data PHP array to encode
     * @return string Encoded text for writing to file
     */
    public function encode($data)
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }

    /**
     * Decode encoded text to key/value PHP array
     * @param string $text Encoded text to convert to PHP key/value array
     * @return array Decoded data as PHP array
     */
    public function decode($text)
    {
        return json_decode($text, true);
    }
}
