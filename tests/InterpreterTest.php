<?php

use PHPUnit\Framework\TestCase;

class InterpreterTest extends TestCase
{
    private function interpret(string $source): string
    {
        ob_start();
        try {
            $tokens  = (new Lexer($source))->tokenize();
            $program = (new Parser($tokens))->parse();
            (new Interpreter())->run($program);
        } finally {
            $output = ob_get_clean();
        }
        return trim($output);
    }

    public function testHelloWorld(): void
    {
        $out = $this->interpret('hoi zeg "Hoi, wereld!"; doei');
        $this->assertSame('Hoi, wereld!', $out);
    }

    public function testVariableDeclaration(): void
    {
        $out = $this->interpret('hoi stel a = 42; zeg a; doei');
        $this->assertSame('42', $out);
    }

    public function testMultipleVariablesOneStatement(): void
    {
        $out = $this->interpret('hoi stel a, b = 5, c; zeg b; doei');
        $this->assertSame('5', $out);
    }

    public function testArithmetic(): void
    {
        $out = $this->interpret('hoi stel a = 10; stel b = 3; zeg a + b; zeg a - b; zeg a * b; zeg a % b; doei');
        $this->assertSame("13\n7\n30\n1", $out);
    }

    public function testStringConcatenation(): void
    {
        $out = $this->interpret('hoi zeg "Neder" + "land"; doei');
        $this->assertSame('Nederland', $out);
    }

    public function testBooleans(): void
    {
        $out = $this->interpret('hoi zeg waar; zeg onwaar; doei');
        $this->assertSame("waar\nonwaar", $out);
    }

    public function testNull(): void
    {
        $out = $this->interpret('hoi stel a = niets; zeg a; doei');
        $this->assertSame('niets', $out);
    }

    public function testComparison(): void
    {
        $out = $this->interpret('hoi zeg 10 == 10; zeg 10 != 3; zeg 5 > 3; zeg 5 <= 5; doei');
        $this->assertSame("waar\nwaar\nwaar\nwaar", $out);
    }

    public function testIfThen(): void
    {
        $out = $this->interpret('hoi als (waar) { zeg "ja"; } doei');
        $this->assertSame('ja', $out);
    }

    public function testIfElse(): void
    {
        $out = $this->interpret('hoi als (onwaar) { zeg "nee"; } anders { zeg "ja"; } doei');
        $this->assertSame('ja', $out);
    }

    public function testElseIf(): void
    {
        $out = $this->interpret('hoi stel x = 70; als (x >= 90) { zeg "A"; } anders als (x >= 70) { zeg "B"; } anders { zeg "C"; } doei');
        $this->assertSame('B', $out);
    }

    public function testWhileLoop(): void
    {
        $out = $this->interpret('hoi stel i = 0; zolang (i < 3) { i += 1; zeg i; } doei');
        $this->assertSame("1\n2\n3", $out);
    }

    public function testBreak(): void
    {
        $out = $this->interpret('hoi stel i = 0; zolang (i < 10) { i += 1; als (i == 3) { stop; } zeg i; } doei');
        $this->assertSame("1\n2", $out);
    }

    public function testContinue(): void
    {
        $out = $this->interpret('hoi stel i = 0; zolang (i < 5) { i += 1; als (i == 3) { verder; } zeg i; } doei');
        $this->assertSame("1\n2\n4\n5", $out);
    }

    public function testScoping(): void
    {
        $out = $this->interpret('hoi stel x = 5; { stel x = 99; zeg x; } zeg x; doei');
        $this->assertSame("99\n5", $out);
    }

    public function testCodeAfterDoeiIsIgnored(): void
    {
        $out = $this->interpret('hoi zeg "ok"; doei dit wordt genegeerd');
        $this->assertSame('ok', $out);
    }

    public function testDivisionByZeroThrows(): void
    {
        $this->expectException(RuntimeException::class);
        $this->interpret('hoi zeg 1 / 0; doei');
    }

    public function testUndefinedVariableThrows(): void
    {
        $this->expectException(RuntimeException::class);
        $this->interpret('hoi zeg onbekend; doei');
    }
}
