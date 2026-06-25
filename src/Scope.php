<?php

class Scope
{
    private array  $vars = [];

    public function __construct(private readonly ?Scope $parent = null) {}

    public function define(string $name, mixed $value = null): void
    {
        $this->vars[$name] = $value;
    }

    public function get(string $name): mixed
    {
        if (array_key_exists($name, $this->vars)) {
            return $this->vars[$name];
        }
        if ($this->parent !== null) {
            return $this->parent->get($name);
        }
        throw new RuntimeException("[Runtime] Onbekende variabele '{$name}'");
    }

    public function set(string $name, mixed $value): void
    {
        if (array_key_exists($name, $this->vars)) {
            $this->vars[$name] = $value;
            return;
        }
        if ($this->parent !== null) {
            $this->parent->set($name, $value);
            return;
        }
        throw new RuntimeException("[Runtime] Onbekende variabele '{$name}'");
    }
}
