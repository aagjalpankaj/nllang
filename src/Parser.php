<?php

class Parser
{
    private int $pos = 0;

    /** @param Token[] $tokens */
    public function __construct(private readonly array $tokens) {}

    public function parse(): ProgramNode
    {
        $this->expect(TokenType::PROGRAM_START, "'hoi'");

        $body = [];
        while (!$this->check(TokenType::PROGRAM_END) && !$this->check(TokenType::EOF)) {
            $body[] = $this->parseStatement();
        }

        $this->expect(TokenType::PROGRAM_END, "'doei'");
        return new ProgramNode($body);
    }

    // -------------------------------------------------------------------------
    // Statements
    // -------------------------------------------------------------------------

    private function parseStatement(): Node
    {
        return match ($this->current()->type) {
            TokenType::VAR_DECL => $this->parseVarDecl(),
            TokenType::PRINT    => $this->parsePrint(),
            TokenType::IF       => $this->parseIf(),
            TokenType::WHILE    => $this->parseWhile(),
            TokenType::BREAK    => $this->parseBreak(),
            TokenType::CONTINUE => $this->parseContinue(),
            TokenType::LBRACE   => $this->parseBlock(),
            default             => $this->parseExprStmt(),
        };
    }

    private function parseVarDecl(): VarDeclNode
    {
        $this->advance(); // stel
        $decls = [];

        do {
            $name = $this->expect(TokenType::IDENTIFIER, 'variabelenaam')->value;
            $init = null;
            if ($this->match(TokenType::ASSIGN)) {
                $init = $this->parseExpression();
            }
            $decls[$name] = $init;
        } while ($this->match(TokenType::COMMA));

        $this->expect(TokenType::SEMICOLON, "';'");
        return new VarDeclNode($decls);
    }

    private function parsePrint(): PrintNode
    {
        $this->advance(); // zeg
        $exprs = [$this->parseExpression()];

        while ($this->match(TokenType::COMMA)) {
            $exprs[] = $this->parseExpression();
        }

        $this->expect(TokenType::SEMICOLON, "';'");
        return new PrintNode($exprs);
    }

    private function parseIf(): IfNode
    {
        $this->advance(); // als
        $this->expect(TokenType::LPAREN, "'('");
        $cond = $this->parseExpression();
        $this->expect(TokenType::RPAREN, "')'");
        $then = $this->parseBlock();

        $elseIfs    = [];
        $elseBranch = null;

        while ($this->check(TokenType::ELSE_IF)) {
            $this->advance();
            $this->expect(TokenType::LPAREN, "'('");
            $eic = $this->parseExpression();
            $this->expect(TokenType::RPAREN, "')'");
            $eib      = $this->parseBlock();
            $elseIfs[] = ['condition' => $eic, 'branch' => $eib];
        }

        if ($this->match(TokenType::ELSE)) {
            $elseBranch = $this->parseBlock();
        }

        return new IfNode($cond, $then, $elseIfs, $elseBranch);
    }

    private function parseWhile(): WhileNode
    {
        $this->advance(); // zolang
        $this->expect(TokenType::LPAREN, "'('");
        $cond = $this->parseExpression();
        $this->expect(TokenType::RPAREN, "')'");
        $body = $this->parseBlock();
        return new WhileNode($cond, $body);
    }

    private function parseBreak(): BreakNode
    {
        $this->advance();
        $this->expect(TokenType::SEMICOLON, "';'");
        return new BreakNode();
    }

    private function parseContinue(): ContinueNode
    {
        $this->advance();
        $this->expect(TokenType::SEMICOLON, "';'");
        return new ContinueNode();
    }

    private function parseBlock(): BlockNode
    {
        $this->expect(TokenType::LBRACE, "'{'");
        $stmts = [];
        while (!$this->check(TokenType::RBRACE) && !$this->check(TokenType::EOF)) {
            $stmts[] = $this->parseStatement();
        }
        $this->expect(TokenType::RBRACE, "'}'");
        return new BlockNode($stmts);
    }

    private function parseExprStmt(): ExprStmtNode
    {
        $expr = $this->parseExpression();
        $this->expect(TokenType::SEMICOLON, "';'");
        return new ExprStmtNode($expr);
    }

    // -------------------------------------------------------------------------
    // Expressions — precedence climbing (low → high)
    // -------------------------------------------------------------------------

    private function parseExpression(): Node
    {
        return $this->parseAssignment();
    }

    private function parseAssignment(): Node
    {
        static $assignOps = [
            TokenType::ASSIGN,
            TokenType::PLUS_ASSIGN,
            TokenType::MINUS_ASSIGN,
            TokenType::STAR_ASSIGN,
            TokenType::SLASH_ASSIGN,
            TokenType::PERCENT_ASSIGN,
        ];

        // Lookahead: IDENTIFIER followed by an assignment operator?
        if ($this->check(TokenType::IDENTIFIER)
            && isset($this->tokens[$this->pos + 1])
            && in_array($this->tokens[$this->pos + 1]->type, $assignOps, true)
        ) {
            $name  = $this->advance()->value;
            $op    = $this->advance()->value;
            $value = $this->parseAssignment(); // right-associative
            return new AssignNode($name, $op, $value);
        }

        return $this->parseOr();
    }

    private function parseOr(): Node
    {
        $node = $this->parseAnd();
        while ($this->check(TokenType::OR)) {
            $op    = $this->advance()->value;
            $right = $this->parseAnd();
            $node  = new BinaryNode($op, $node, $right);
        }
        return $node;
    }

    private function parseAnd(): Node
    {
        $node = $this->parseEquality();
        while ($this->check(TokenType::AND)) {
            $op    = $this->advance()->value;
            $right = $this->parseEquality();
            $node  = new BinaryNode($op, $node, $right);
        }
        return $node;
    }

    private function parseEquality(): Node
    {
        $node = $this->parseRelational();
        while ($this->check(TokenType::EQ) || $this->check(TokenType::NEQ)) {
            $op    = $this->advance()->value;
            $right = $this->parseRelational();
            $node  = new BinaryNode($op, $node, $right);
        }
        return $node;
    }

    private function parseRelational(): Node
    {
        static $ops = [TokenType::LT, TokenType::GT, TokenType::LTE, TokenType::GTE];
        $node = $this->parseAdditive();
        while (in_array($this->current()->type, $ops, true)) {
            $op    = $this->advance()->value;
            $right = $this->parseAdditive();
            $node  = new BinaryNode($op, $node, $right);
        }
        return $node;
    }

    private function parseAdditive(): Node
    {
        $node = $this->parseMultiplicative();
        while ($this->check(TokenType::PLUS) || $this->check(TokenType::MINUS)) {
            $op    = $this->advance()->value;
            $right = $this->parseMultiplicative();
            $node  = new BinaryNode($op, $node, $right);
        }
        return $node;
    }

    private function parseMultiplicative(): Node
    {
        static $ops = [TokenType::STAR, TokenType::SLASH, TokenType::PERCENT];
        $node = $this->parseUnary();
        while (in_array($this->current()->type, $ops, true)) {
            $op    = $this->advance()->value;
            $right = $this->parseUnary();
            $node  = new BinaryNode($op, $node, $right);
        }
        return $node;
    }

    private function parseUnary(): Node
    {
        if ($this->check(TokenType::MINUS)) {
            $op      = $this->advance()->value;
            $operand = $this->parseUnary();
            return new UnaryNode($op, $operand);
        }
        return $this->parsePrimary();
    }

    private function parsePrimary(): Node
    {
        $tok = $this->current();

        switch ($tok->type) {
            case TokenType::NUMBER:
                $this->advance();
                return new NumberNode($tok->value);

            case TokenType::STRING:
                $this->advance();
                return new StringNode($tok->value);

            case TokenType::TRUE:
                $this->advance();
                return new BoolNode(true);

            case TokenType::FALSE:
                $this->advance();
                return new BoolNode(false);

            case TokenType::NULL_TYPE:
                $this->advance();
                return new NullNode();

            case TokenType::IDENTIFIER:
                $this->advance();
                return new IdentifierNode($tok->value);

            case TokenType::LPAREN:
                $this->advance();
                $expr = $this->parseExpression();
                $this->expect(TokenType::RPAREN, "')'");
                return $expr;

            default:
                $this->error(
                    "Onverwacht token '{$tok->value}' op regel {$tok->line}"
                );
        }
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function current(): Token
    {
        return $this->tokens[$this->pos];
    }

    private function advance(): Token
    {
        $tok = $this->tokens[$this->pos];
        if ($tok->type !== TokenType::EOF) {
            $this->pos++;
        }
        return $tok;
    }

    private function check(string $type): bool
    {
        return $this->current()->type === $type;
    }

    private function match(string $type): bool
    {
        if ($this->check($type)) {
            $this->advance();
            return true;
        }
        return false;
    }

    private function expect(string $type, string $what): Token
    {
        if (!$this->check($type)) {
            $tok = $this->current();
            $got = $tok->value !== null ? "'{$tok->value}'" : $tok->type;
            $this->error("Verwacht {$what} maar kreeg {$got} op regel {$tok->line}");
        }
        return $this->advance();
    }

    private function error(string $msg): never
    {
        throw new RuntimeException("[Parser] {$msg}");
    }
}
