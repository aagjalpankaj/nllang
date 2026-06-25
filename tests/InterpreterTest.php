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

    // -------------------------------------------------------------------------
    // Functions
    // -------------------------------------------------------------------------

    public function testFunctionNoReturn(): void
    {
        $out = $this->interpret('hoi taak zeg_hoi() { zeg "hoi"; } zeg_hoi(); doei');
        $this->assertSame('hoi', $out);
    }

    public function testFunctionWithReturn(): void
    {
        $out = $this->interpret('hoi taak kwadraat(n) { geef n * n; } zeg kwadraat(7); doei');
        $this->assertSame('49', $out);
    }

    public function testFunctionWithMultipleParams(): void
    {
        $out = $this->interpret('hoi taak tel_op(a, b) { geef a + b; } zeg tel_op(3, 4); doei');
        $this->assertSame('7', $out);
    }

    public function testRecursiveFunction(): void
    {
        $out = $this->interpret('hoi taak fac(n) { als (n <= 1) { geef 1; } geef n * fac(n - 1); } zeg fac(5); doei');
        $this->assertSame('120', $out);
    }

    public function testFunctionReturnsNullByDefault(): void
    {
        $out = $this->interpret('hoi taak leeg() { stel a = 1; } zeg leeg(); doei');
        $this->assertSame('niets', $out);
    }

    public function testReturnOutsideFunctionThrows(): void
    {
        $this->expectException(RuntimeException::class);
        $this->interpret('hoi geef 1; doei');
    }

    // -------------------------------------------------------------------------
    // Arrays
    // -------------------------------------------------------------------------

    public function testArrayLiteral(): void
    {
        $out = $this->interpret('hoi stel arr = [1, 2, 3]; zeg arr; doei');
        $this->assertSame('[1, 2, 3]', $out);
    }

    public function testArrayIndex(): void
    {
        $out = $this->interpret('hoi stel arr = [10, 20, 30]; zeg arr[0]; zeg arr[2]; doei');
        $this->assertSame("10\n30", $out);
    }

    public function testArrayIndexAssign(): void
    {
        $out = $this->interpret('hoi stel arr = [1, 2, 3]; arr[1] = 99; zeg arr; doei');
        $this->assertSame('[1, 99, 3]', $out);
    }

    public function testArrayBuiltinLengte(): void
    {
        $out = $this->interpret('hoi stel arr = [1, 2, 3, 4]; zeg lengte(arr); doei');
        $this->assertSame('4', $out);
    }

    public function testArrayBuiltinDuw(): void
    {
        $out = $this->interpret('hoi stel arr = [1, 2]; arr = duw(arr, 3); zeg arr; doei');
        $this->assertSame('[1, 2, 3]', $out);
    }

    public function testArrayBuiltinPop(): void
    {
        $out = $this->interpret('hoi stel arr = [1, 2, 3]; arr = pop(arr); zeg arr; doei');
        $this->assertSame('[1, 2]', $out);
    }

    public function testEmptyArray(): void
    {
        $out = $this->interpret('hoi stel arr = []; zeg lengte(arr); doei');
        $this->assertSame('0', $out);
    }

    // -------------------------------------------------------------------------
    // Logical NOT
    // -------------------------------------------------------------------------

    public function testNietKeyword(): void
    {
        $out = $this->interpret('hoi zeg niet waar; zeg niet onwaar; doei');
        $this->assertSame("onwaar\nwaar", $out);
    }

    public function testBangOperator(): void
    {
        $out = $this->interpret('hoi zeg !waar; zeg !onwaar; doei');
        $this->assertSame("onwaar\nwaar", $out);
    }

    public function testNietInCondition(): void
    {
        $out = $this->interpret('hoi stel x = onwaar; als (niet x) { zeg "ja"; } doei');
        $this->assertSame('ja', $out);
    }

    // -------------------------------------------------------------------------
    // Voor elk (foreach)
    // -------------------------------------------------------------------------

    public function testForEachBasic(): void
    {
        $out = $this->interpret('hoi stel arr = [1, 2, 3]; voor elk x in arr { zeg x; } doei');
        $this->assertSame("1\n2\n3", $out);
    }

    public function testForEachStrings(): void
    {
        $out = $this->interpret('hoi stel arr = ["a", "b", "c"]; voor elk s in arr { zeg s; } doei');
        $this->assertSame("a\nb\nc", $out);
    }

    public function testForEachWithContinue(): void
    {
        $out = $this->interpret('hoi stel arr = [1, 2, 3, 4]; voor elk x in arr { als (x == 2) { verder; } zeg x; } doei');
        $this->assertSame("1\n3\n4", $out);
    }

    public function testForEachWithBreak(): void
    {
        $out = $this->interpret('hoi stel arr = [1, 2, 3, 4]; voor elk x in arr { als (x == 3) { stop; } zeg x; } doei');
        $this->assertSame("1\n2", $out);
    }

    public function testForEachEmptyList(): void
    {
        $out = $this->interpret('hoi voor elk x in [] { zeg x; } zeg "klaar"; doei');
        $this->assertSame('klaar', $out);
    }

    public function testForEachVariableIsScoped(): void
    {
        $out = $this->interpret('hoi stel arr = [1, 2]; voor elk x in arr { zeg x; } doei');
        $this->assertSame("1\n2", $out);
    }

    // -------------------------------------------------------------------------
    // Built-in type conversions
    // -------------------------------------------------------------------------

    public function testTekst(): void
    {
        $out = $this->interpret('hoi zeg tekst(42); doei');
        $this->assertSame('42', $out);
    }

    public function testGetal(): void
    {
        $out = $this->interpret('hoi zeg getal("42") + 1; doei');
        $this->assertSame('43', $out);
    }
}
