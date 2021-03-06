<?php

namespace SKuhnow\LexoRank\Utils;

class StringBuilder
{

    private $str;

    public function __construct(string $str = '')
    {
        $this->str = $str;
    }

    public function getLength(): int
    {
        return strlen($this->str);
    }

    public function setLength(int $value): void
    {
        $this->str = substr($this->str, 0, $value);
    }

    public function append(string $str): StringBuilder
    {
        $this->str = $this->str . $str;
        return $this;
    }

    public function remove(int $startIndex, int $length): StringBuilder
    {
        $this->str = substr($this->str, 0, $startIndex) . substr($this->str, $startIndex + $length);
        return $this;
    }

    public function insert(int $index, string $value): StringBuilder
    {
        $this->str = substr($this->str, 0, $index) . $value . substr($this->str, $index);
        return $this;
    }

    public function __toString(): string
    {
        return $this->str;
    }
}
