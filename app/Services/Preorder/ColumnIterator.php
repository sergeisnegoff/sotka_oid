<?php

namespace App\Services\Preorder;

class ColumnIterator
{
    private $currentColumn;

    public function __construct(string $starting)
    {
        $this->currentColumn = $starting;
    }

    public function getCurrent()
    {
        return $this->currentColumn;
    }

    public function setNext()
    {
        $length = strlen($this->currentColumn);
        $letters = str_split($this->currentColumn);

        $letters[$length - 1] = $this->incrementLetter($letters[$length - 1]);


        for ($i = $length - 1; $i >= 0; $i--) {
            if ($letters[$i] === 'A') {
                if ($i > 0) {
                    $letters[$i - 1] = $this->incrementLetter($letters[$i - 1]);
                } else {

                    array_unshift($letters, 'A');
                }
            } else {
                break;
            }
        }

        $this->currentColumn = implode('', $letters);
    }

    private function incrementLetter($letter)
    {
        if ($letter === 'Z') {
            return 'A';
        } elseif ($letter === 'Y') {
            return 'Z';
        } else {
            return chr(ord($letter) + 1);
        }
    }

    public function getRange(string $from = 'A') {
        $iterator = new self($from);
        $out = [];
        while ($iterator->getCurrent() !== $this->currentColumn) {
            $out[] = $iterator->getCurrent();
            $iterator->setNext();
        }
        $out[] = $this->currentColumn;
        return $out;
    }
}
