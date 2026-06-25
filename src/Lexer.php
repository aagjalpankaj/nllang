<?php

class Lexer
{
    private int    $pos  = 0;
    private int    $line = 1;
    private string $src;
    private int    $len;

    public function __construct(string $src)
    {
        $this->src = $src;
        $this->len = strlen($src);
    }

    public function tokenize(): array
    {
        $tokens = [];

        while ($this->pos < $this->len) {
            $this->skipWhitespaceAndComments();
            if ($this->pos >= $this->len) {
                break;
            }
            $tokens[] = $this->nextToken();
        }

        $tokens[] = new Token(TokenType::EOF, null, $this->line);
        return $tokens;
    }

    private function skipWhitespaceAndComments(): void
    {
        while ($this->pos < $this->len) {
            $ch = $this->src[$this->pos];

            if ($ch === "\n") {
                $this->line++;
                $this->pos++;
            } elseif ($ch === ' ' || $ch === "\t" || $ch === "\r") {
                $this->pos++;
            } elseif ($this->startsWith('//')) {
                while ($this->pos < $this->len && $this->src[$this->pos] !== "\n") {
                    $this->pos++;
                }
            } elseif ($this->startsWith('/*')) {
                $this->pos += 2;
                while ($this->pos < $this->len) {
                    if ($this->src[$this->pos] === "\n") {
                        $this->line++;
                    }
                    if ($this->startsWith('*/')) {
                        $this->pos += 2;
                        break;
                    }
                    $this->pos++;
                }
            } else {
                break;
            }
        }
    }

    private function startsWith(string $str): bool
    {
        return substr($this->src, $this->pos, strlen($str)) === $str;
    }

    private function nextToken(): Token
    {
        $ch   = $this->src[$this->pos];
        $line = $this->line;

        if (ctype_digit($ch)) {
            return $this->scanNumber($line);
        }
        if ($ch === '"' || $ch === "'") {
            return $this->scanString($line);
        }
        if (ctype_alpha($ch) || $ch === '_') {
            return $this->scanWord($line);
        }
        return $this->scanSymbol($line);
    }

    private function scanNumber(int $line): Token
    {
        $start  = $this->pos;
        $hasDot = false;

        while ($this->pos < $this->len) {
            $ch = $this->src[$this->pos];
            if (ctype_digit($ch)) {
                $this->pos++;
            } elseif ($ch === '.' && !$hasDot
                && $this->pos + 1 < $this->len
                && ctype_digit($this->src[$this->pos + 1])
            ) {
                $hasDot = true;
                $this->pos++;
            } else {
                break;
            }
        }

        $raw   = substr($this->src, $start, $this->pos - $start);
        $value = $hasDot ? (float) $raw : (int) $raw;
        return new Token(TokenType::NUMBER, $value, $line);
    }

    private function scanString(int $line): Token
    {
        $quote = $this->src[$this->pos++];
        $buf   = '';

        while ($this->pos < $this->len) {
            $ch = $this->src[$this->pos];

            if ($ch === $quote) {
                $this->pos++;
                return new Token(TokenType::STRING, $buf, $line);
            }

            if ($ch === "\n") {
                $this->error("Afgebroken tekst op regel {$line}");
            }

            if ($ch === '\\' && $this->pos + 1 < $this->len) {
                $this->pos++;
                $esc = $this->src[$this->pos++];
                $buf .= match($esc) {
                    'n'  => "\n",
                    't'  => "\t",
                    '\\' => '\\',
                    '"'  => '"',
                    "'"  => "'",
                    default => '\\' . $esc,
                };
                continue;
            }

            $buf .= $ch;
            $this->pos++;
        }

        $this->error("Afgebroken tekst op regel {$line}");
    }

    private function scanWord(int $line): Token
    {
        $start = $this->pos;
        while ($this->pos < $this->len
            && (ctype_alnum($this->src[$this->pos]) || $this->src[$this->pos] === '_')
        ) {
            $this->pos++;
        }
        $word = substr($this->src, $start, $this->pos - $start);

        return match ($word) {
            'hoi'    => new Token(TokenType::PROGRAM_START, $word, $line),
            'doei'   => new Token(TokenType::PROGRAM_END, $word, $line),
            'stel'   => new Token(TokenType::VAR_DECL, $word, $line),
            'zeg'    => new Token(TokenType::PRINT, $word, $line),
            'als'    => new Token(TokenType::IF, $word, $line),
            'anders' => $this->scanAnders($line),
            'zolang' => new Token(TokenType::WHILE, $word, $line),
            'stop'   => new Token(TokenType::BREAK, $word, $line),
            'verder' => new Token(TokenType::CONTINUE, $word, $line),
            'waar'   => new Token(TokenType::TRUE, true, $line),
            'onwaar' => new Token(TokenType::FALSE, false, $line),
            'niets'  => new Token(TokenType::NULL_TYPE, null, $line),
            default  => new Token(TokenType::IDENTIFIER, $word, $line),
        };
    }

    // 'anders' was just consumed; peek ahead for 'als' to emit ELSE_IF vs ELSE.
    private function scanAnders(int $line): Token
    {
        $tmp = $this->pos;

        // Skip only horizontal whitespace — anders als must be on one line.
        while ($tmp < $this->len && ($this->src[$tmp] === ' ' || $this->src[$tmp] === "\t")) {
            $tmp++;
        }

        if ($tmp + 3 <= $this->len && substr($this->src, $tmp, 3) === 'als') {
            $after = $tmp + 3;
            $atEnd = $after >= $this->len;
            if ($atEnd || (!ctype_alnum($this->src[$after]) && $this->src[$after] !== '_')) {
                $this->pos = $after;
                return new Token(TokenType::ELSE_IF, 'anders als', $line);
            }
        }

        return new Token(TokenType::ELSE, 'anders', $line);
    }

    private function scanSymbol(int $line): Token
    {
        // Try two-character tokens first.
        if ($this->pos + 1 < $this->len) {
            $two = substr($this->src, $this->pos, 2);
            $tok = match ($two) {
                '==' => new Token(TokenType::EQ, $two, $line),
                '!=' => new Token(TokenType::NEQ, $two, $line),
                '<=' => new Token(TokenType::LTE, $two, $line),
                '>=' => new Token(TokenType::GTE, $two, $line),
                '+=' => new Token(TokenType::PLUS_ASSIGN, $two, $line),
                '-=' => new Token(TokenType::MINUS_ASSIGN, $two, $line),
                '*=' => new Token(TokenType::STAR_ASSIGN, $two, $line),
                '/=' => new Token(TokenType::SLASH_ASSIGN, $two, $line),
                '%=' => new Token(TokenType::PERCENT_ASSIGN, $two, $line),
                '&&' => new Token(TokenType::AND, $two, $line),
                '||' => new Token(TokenType::OR, $two, $line),
                default => null,
            };
            if ($tok !== null) {
                $this->pos += 2;
                return $tok;
            }
        }

        $ch = $this->src[$this->pos++];
        return match ($ch) {
            '+'  => new Token(TokenType::PLUS, $ch, $line),
            '-'  => new Token(TokenType::MINUS, $ch, $line),
            '*'  => new Token(TokenType::STAR, $ch, $line),
            '/'  => new Token(TokenType::SLASH, $ch, $line),
            '%'  => new Token(TokenType::PERCENT, $ch, $line),
            '='  => new Token(TokenType::ASSIGN, $ch, $line),
            '<'  => new Token(TokenType::LT, $ch, $line),
            '>'  => new Token(TokenType::GT, $ch, $line),
            '('  => new Token(TokenType::LPAREN, $ch, $line),
            ')'  => new Token(TokenType::RPAREN, $ch, $line),
            '{'  => new Token(TokenType::LBRACE, $ch, $line),
            '}'  => new Token(TokenType::RBRACE, $ch, $line),
            ';'  => new Token(TokenType::SEMICOLON, $ch, $line),
            ','  => new Token(TokenType::COMMA, $ch, $line),
            default => $this->error("Onbekend teken '{$ch}' op regel {$line}"),
        };
    }

    private function error(string $msg): never
    {
        throw new RuntimeException("[Lexer] {$msg}");
    }
}
