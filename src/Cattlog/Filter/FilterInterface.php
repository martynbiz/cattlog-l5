<?php namespace Cattlog\Filter;

interface FilterInterface
{
	/**
     * Encode key/value PHP array to desired text for writing to file
     * @param array $data PHP array to encode
     * @return string Encoded text for writing to file
     */
    public function encode($data);

    /**
     * Decode encoded text to key/value PHP array
     * @param string $text Encoded text to convert to PHP key/value array
     * @return array Decoded data as PHP array
     */
    public function decode($text);
}
