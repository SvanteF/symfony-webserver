<?php

namespace App\Card;

class Card
{
    protected string $value;
    protected string $color;

    public function __construct(string $value, string $color)
    {
        $this->value = $value;
        $this->color = $color;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getAsString(): string
    {
        return "[$this->color $this->value]";
    }
}
