<?php

class Token
{
    public function __construct(
        public readonly string $type,
        public readonly mixed  $value,
        public readonly int    $line
    ) {}

    public function __toString(): string
    {
        $v = var_export($this->value, true);
        return "Token({$this->type}, {$v}, line:{$this->line})";
    }
}
