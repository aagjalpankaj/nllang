<?php

class TokenType
{
    // Program markers
    const PROGRAM_START = 'PROGRAM_START'; // hallo
    const PROGRAM_END   = 'PROGRAM_END';   // doei

    // Statement keywords
    const VAR_DECL = 'VAR_DECL'; // stel
    const PRINT    = 'PRINT';    // zeg
    const IF       = 'IF';       // als
    const ELSE_IF  = 'ELSE_IF';  // anders als
    const ELSE     = 'ELSE';     // anders
    const WHILE    = 'WHILE';    // zolang
    const BREAK    = 'BREAK';    // stop
    const CONTINUE = 'CONTINUE'; // verder

    // Literal types
    const NUMBER    = 'NUMBER';
    const STRING    = 'STRING';
    const TRUE      = 'TRUE';      // waar
    const FALSE     = 'FALSE';     // onwaar
    const NULL_TYPE = 'NULL_TYPE'; // niets

    // Identifier
    const IDENTIFIER = 'IDENTIFIER';

    // Arithmetic operators
    const PLUS    = 'PLUS';
    const MINUS   = 'MINUS';
    const STAR    = 'STAR';
    const SLASH   = 'SLASH';
    const PERCENT = 'PERCENT';

    // Assignment operators
    const ASSIGN         = 'ASSIGN';
    const PLUS_ASSIGN    = 'PLUS_ASSIGN';
    const MINUS_ASSIGN   = 'MINUS_ASSIGN';
    const STAR_ASSIGN    = 'STAR_ASSIGN';
    const SLASH_ASSIGN   = 'SLASH_ASSIGN';
    const PERCENT_ASSIGN = 'PERCENT_ASSIGN';

    // Comparison operators
    const EQ  = 'EQ';
    const NEQ = 'NEQ';
    const LT  = 'LT';
    const GT  = 'GT';
    const LTE = 'LTE';
    const GTE = 'GTE';

    // Logical operators
    const AND = 'AND';
    const OR  = 'OR';

    // Delimiters
    const LPAREN    = 'LPAREN';
    const RPAREN    = 'RPAREN';
    const LBRACE    = 'LBRACE';
    const RBRACE    = 'RBRACE';
    const SEMICOLON = 'SEMICOLON';
    const COMMA     = 'COMMA';

    const EOF = 'EOF';
}
