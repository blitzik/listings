<?php declare(strict_types=1);

namespace blitzik\Doctrine\MySQL;

class SubTime extends \Doctrine\ORM\Query\AST\Functions\FunctionNode
{
    public $timeExpression  = null;
    public $timeExpression2 = null;

    /**
     * @param \Doctrine\ORM\Query\SqlWalker $sqlWalker
     *
     * @return string
     */
    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker): string
    {
        return 'SUBTIME(' . $sqlWalker->walkArithmeticPrimary($this->timeExpression) . ',
                        ' . $sqlWalker->walkArithmeticPrimary($this->timeExpression2) . ')';
    }

    /**
     * @param \Doctrine\ORM\Query\Parser $parser
     *
     * @return void
     */
    public function parse(\Doctrine\ORM\Query\Parser $parser): void
    {
        $parser->match(\Doctrine\ORM\Query\Lexer::T_IDENTIFIER);
        $parser->match(\Doctrine\ORM\Query\Lexer::T_OPEN_PARENTHESIS);

        $this->timeExpression = $parser->ArithmeticPrimary();

        $parser->match(\Doctrine\ORM\Query\Lexer::T_COMMA);

        $this->timeExpression2 = $parser->ArithmeticPrimary();

        $parser->match(\Doctrine\ORM\Query\Lexer::T_CLOSE_PARENTHESIS);
    }

}