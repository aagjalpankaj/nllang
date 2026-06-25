<?php

abstract class Node {}

// Statements

class ProgramNode extends Node
{
    /** @param Node[] $body */
    public function __construct(public readonly array $body) {}
}

class VarDeclNode extends Node
{
    /** @param array<string, Node|null> $declarations name => init expression or null */
    public function __construct(public readonly array $declarations) {}
}

class PrintNode extends Node
{
    /** @param Node[] $expressions */
    public function __construct(public readonly array $expressions) {}
}

class IfNode extends Node
{
    /**
     * @param array<array{condition: Node, branch: BlockNode}> $elseIfs
     */
    public function __construct(
        public readonly Node      $condition,
        public readonly BlockNode $thenBranch,
        public readonly array     $elseIfs,
        public readonly ?BlockNode $elseBranch
    ) {}
}

class WhileNode extends Node
{
    public function __construct(
        public readonly Node      $condition,
        public readonly BlockNode $body
    ) {}
}

class BreakNode extends Node {}

class ContinueNode extends Node {}

class BlockNode extends Node
{
    /** @param Node[] $statements */
    public function __construct(public readonly array $statements) {}
}

class ExprStmtNode extends Node
{
    public function __construct(public readonly Node $expression) {}
}

// Expressions

class AssignNode extends Node
{
    public function __construct(
        public readonly string $name,
        public readonly string $op,
        public readonly Node   $value
    ) {}
}

class BinaryNode extends Node
{
    public function __construct(
        public readonly string $op,
        public readonly Node   $left,
        public readonly Node   $right
    ) {}
}

class UnaryNode extends Node
{
    public function __construct(
        public readonly string $op,
        public readonly Node   $operand
    ) {}
}

class NumberNode extends Node
{
    public function __construct(public readonly int|float $value) {}
}

class StringNode extends Node
{
    public function __construct(public readonly string $value) {}
}

class BoolNode extends Node
{
    public function __construct(public readonly bool $value) {}
}

class NullNode extends Node {}

class IdentifierNode extends Node
{
    public function __construct(public readonly string $name) {}
}
