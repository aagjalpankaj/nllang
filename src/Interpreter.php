<?php

// Used as exceptions to unwind the call stack for break/continue.
class BreakSignal extends Exception {}
class ContinueSignal extends Exception {}

class Interpreter
{
    private Scope $global;

    public function __construct()
    {
        $this->global = new Scope();
    }

    public function run(ProgramNode $program): void
    {
        try {
            $this->execBody($program->body, $this->global);
        } catch (BreakSignal) {
            throw new RuntimeException("[Runtime] 'stop' gebruikt buiten een lus");
        } catch (ContinueSignal) {
            throw new RuntimeException("[Runtime] 'verder' gebruikt buiten een lus");
        }
    }

    // -------------------------------------------------------------------------
    // Statement execution
    // -------------------------------------------------------------------------

    private function execute(Node $node, Scope $scope): void
    {
        match (true) {
            $node instanceof VarDeclNode  => $this->execVarDecl($node, $scope),
            $node instanceof PrintNode    => $this->execPrint($node, $scope),
            $node instanceof IfNode       => $this->execIf($node, $scope),
            $node instanceof WhileNode    => $this->execWhile($node, $scope),
            $node instanceof BreakNode    => throw new BreakSignal(),
            $node instanceof ContinueNode => throw new ContinueSignal(),
            $node instanceof BlockNode    => $this->execBlock($node, $scope),
            $node instanceof ExprStmtNode => $this->evaluate($node->expression, $scope),
            default => $this->error("Onbekend knooppunttype: " . get_class($node)),
        };
    }

    /** Execute a list of statements in the given scope (no new scope created). */
    private function execBody(array $stmts, Scope $scope): void
    {
        foreach ($stmts as $stmt) {
            $this->execute($stmt, $scope);
        }
    }

    /** Execute a BlockNode with a fresh child scope. */
    private function execBlock(BlockNode $block, Scope $parent): void
    {
        $scope = new Scope($parent);
        foreach ($block->statements as $stmt) {
            $this->execute($stmt, $scope);
        }
    }

    private function execVarDecl(VarDeclNode $node, Scope $scope): void
    {
        foreach ($node->declarations as $name => $initExpr) {
            $value = ($initExpr !== null) ? $this->evaluate($initExpr, $scope) : null;
            $scope->define($name, $value);
        }
    }

    private function execPrint(PrintNode $node, Scope $scope): void
    {
        $parts = [];
        foreach ($node->expressions as $expr) {
            $parts[] = $this->stringify($this->evaluate($expr, $scope));
        }
        echo implode(' ', $parts) . "\n";
    }

    private function execIf(IfNode $node, Scope $scope): void
    {
        if ($this->isTruthy($this->evaluate($node->condition, $scope))) {
            $this->execBlock($node->thenBranch, $scope);
            return;
        }

        foreach ($node->elseIfs as $branch) {
            if ($this->isTruthy($this->evaluate($branch['condition'], $scope))) {
                $this->execBlock($branch['branch'], $scope);
                return;
            }
        }

        if ($node->elseBranch !== null) {
            $this->execBlock($node->elseBranch, $scope);
        }
    }

    private function execWhile(WhileNode $node, Scope $scope): void
    {
        while ($this->isTruthy($this->evaluate($node->condition, $scope))) {
            try {
                $this->execBlock($node->body, $scope);
            } catch (BreakSignal) {
                break;
            } catch (ContinueSignal) {
                continue;
            }
        }
    }

    // -------------------------------------------------------------------------
    // Expression evaluation
    // -------------------------------------------------------------------------

    private function evaluate(Node $node, Scope $scope): mixed
    {
        return match (true) {
            $node instanceof NumberNode     => $node->value,
            $node instanceof StringNode     => $node->value,
            $node instanceof BoolNode       => $node->value,
            $node instanceof NullNode       => null,
            $node instanceof IdentifierNode => $scope->get($node->name),
            $node instanceof AssignNode     => $this->evalAssign($node, $scope),
            $node instanceof BinaryNode     => $this->evalBinary($node, $scope),
            $node instanceof UnaryNode      => $this->evalUnary($node, $scope),
            default => $this->error("Kan uitdrukking niet evalueren: " . get_class($node)),
        };
    }

    private function evalAssign(AssignNode $node, Scope $scope): mixed
    {
        $rhs = $this->evaluate($node->value, $scope);

        $result = match ($node->op) {
            '='  => $rhs,
            '+=' => $this->add($scope->get($node->name), $rhs),
            '-=' => $scope->get($node->name) - $rhs,
            '*=' => $scope->get($node->name) * $rhs,
            '/=' => $this->divide($scope->get($node->name), $rhs),
            '%=' => $scope->get($node->name) % $rhs,
        };

        $scope->set($node->name, $result);
        return $result;
    }

    private function evalBinary(BinaryNode $node, Scope $scope): mixed
    {
        $left = $this->evaluate($node->left, $scope);

        // Short-circuit logical operators before evaluating the right side.
        if ($node->op === '&&') {
            return $this->isTruthy($left) ? $this->evaluate($node->right, $scope) : $left;
        }
        if ($node->op === '||') {
            return $this->isTruthy($left) ? $left : $this->evaluate($node->right, $scope);
        }

        $right = $this->evaluate($node->right, $scope);

        return match ($node->op) {
            '+'  => $this->add($left, $right),
            '-'  => $left - $right,
            '*'  => $left * $right,
            '/'  => $this->divide($left, $right),
            '%'  => $left % $right,
            '==' => $this->isEqual($left, $right),
            '!=' => !$this->isEqual($left, $right),
            '<'  => $left < $right,
            '>'  => $left > $right,
            '<=' => $left <= $right,
            '>=' => $left >= $right,
            default => $this->error("Onbekende operator '{$node->op}'"),
        };
    }

    private function evalUnary(UnaryNode $node, Scope $scope): mixed
    {
        $val = $this->evaluate($node->operand, $scope);
        return match ($node->op) {
            '-' => -$val,
            default => $this->error("Onbekende unaire operator '{$node->op}'"),
        };
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function add(mixed $a, mixed $b): mixed
    {
        // String concatenation when either operand is a string.
        if (is_string($a) || is_string($b)) {
            return $this->stringify($a) . $this->stringify($b);
        }
        return $a + $b;
    }

    private function divide(mixed $a, mixed $b): float|int
    {
        if ($b == 0) {
            $this->error("Deling door nul");
        }
        $result = $a / $b;
        // Return int when the result is a whole number.
        return (is_int($a) && is_int($b) && $a % $b === 0) ? (int) $result : $result;
    }

    private function isEqual(mixed $a, mixed $b): bool
    {
        // null only equals null.
        if ($a === null || $b === null) {
            return $a === $b;
        }
        // Numeric comparison across int/float.
        if ((is_int($a) || is_float($a)) && (is_int($b) || is_float($b))) {
            return $a == $b;
        }
        return $a === $b;
    }

    private function isTruthy(mixed $val): bool
    {
        if ($val === null)  return false;
        if (is_bool($val)) return $val;
        if (is_int($val) || is_float($val)) return $val !== 0 && $val !== 0.0;
        if (is_string($val)) return $val !== '';
        return true;
    }

    private function stringify(mixed $val): string
    {
        if ($val === null)  return 'niets';
        if ($val === true)  return 'waar';
        if ($val === false) return 'onwaar';
        // PHP already converts 10.0 → "10", 10.5 → "10.5".
        return (string) $val;
    }

    private function error(string $msg): never
    {
        throw new RuntimeException("[Runtime] {$msg}");
    }
}
