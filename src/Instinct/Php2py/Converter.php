<?php

/*
 * (c) Alexandre Quercia <alquerci@email.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Instinct\Php2py;

use InvalidArgumentException;

use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Alexandre Quercia <alquerci@email.com>
 */
class Converter implements ConverterInterface
{
    private $imports = array();

    /**
     * {@inheritDoc}
     */
    public function convert($content, $force = false)
    {
        $this->imports = array();

        $tokens = Tokens::fromCode($content);

        // tags
        if (!$force) {
            if (T_OPEN_TAG !== $tokens[0]->getId()) {
                return $content;
            }
        }
        $content = $this->removePhpTags($tokens);

        // operators
        $this->convertTernaireOperator($tokens);
        $this->convertConcatenationOperator($tokens);
        $this->convertObjectOperator($tokens);
        $this->convertScopeResolutionOperator($tokens);
        $this->convertIdenticalOperator($tokens);
        $this->convertNotIdenticalOperator($tokens);
        $this->convertErrorControlOperators($tokens);
        $this->convertNotOperators($tokens);
        $this->convertAndOperators($tokens);
        $this->convertOrOperators($tokens);
        $this->convertTypeOperators($tokens);

        // code structures
        $this->convertIncrement($tokens);
        $this->convertXEqual($tokens);
        $this->convertCurlyVariable($tokens);
        $this->convertVariable($tokens);
        $this->convertArray($tokens);

        // keywords
        $this->convertTrue($tokens);
        $this->convertFalse($tokens);
        $this->convertNull($tokens);
        $this->convertFunction($tokens);
        $this->convertObjectInitialization($tokens);
        $this->convertDeclare($tokens);

        // cast
        $this->convertBoolCasting($tokens);
        $this->convertIntCasting($tokens);
        $this->convertStringCasting($tokens);
        $this->convertNullCasting($tokens);

        // constants
        $this->convertDirMagicConstants($tokens);
        $this->convertFileMagicConstants($tokens);
        $this->convertNamspaceMagicConstants($tokens);

        // string
        $this->convertSingleQuote($tokens);
        $this->convertDoubleQuote($tokens);
        $this->convertBlockQuote($tokens);

        // comments
        $this->convertOneLineComments($tokens);
        $this->convertMultiLineComments($tokens);

        $this->doAddImports($tokens);

        return $tokens->generateCode();
    }

    private function removePhpTags(Tokens $tokens)
    {
        if (T_OPEN_TAG !== $tokens[0]->getId()) {
            return $content;
        }

        $tokens[0]->clear();
    }

    private function convertOneLineComments(Tokens $tokens)
    {
        foreach ($tokens as $token) {
            if (!$token->isGivenKind(T_COMMENT)) {
                continue;
            }

            $content = $token->getContent();

            $content = preg_replace('#^//#', '#', $content);

            $token->setContent($content);
        }
    }

    private function convertMultiLineComments(Tokens $tokens)
    {
        foreach ($tokens as $token) {
            if (!$token->isGivenKind(T_COMMENT)) {
                continue;
            }

            $content = $token->getContent();

            $content = preg_replace('#^/\*\s*\*?\s*#', '"""', $content);
            $content = preg_replace('#\x20?\*/$#', '"""', $content);
            $content = preg_replace('/^(\s*) \* /m', '$1', $content);

            $token->setContent($content);
        }
    }

    private function convertVariable(Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_VARIABLE)) {
                continue;
            }

            $isCurly = false;

            $prevIndex = $index - 1;
            $prevToken = $tokens[$prevIndex];

            if ($prevToken->isGivenKind(T_START_HEREDOC)) {
                continue;
            }

            if ($prevToken->isGivenKind(T_DOLLAR_OPEN_CURLY_BRACES)) {
                continue;
            }

            if ($prevToken->isGivenKind(T_PAAMAYIM_NEKUDOTAYIM)) {
                continue;
            }

            if ($prevToken->isGivenKind(T_OBJECT_OPERATOR)) {
                continue;
            }

            if ($prevToken->isGivenKind(T_CURLY_OPEN)) {
                $isCurly = true;

                $curlyOpenIndex = $prevIndex;
                $curlyOpenToken = $prevToken;

                $prevIndex = $tokens->getPrevMeaningfulToken($prevIndex);
                $prevToken = $tokens[$prevIndex];
            }

            if (!$isCurly && $prevToken->equals(new Token('{'))) {
                continue;
            }

            $nextIndex = $this->findVariableBlockEnd($tokens, $index);

            if ($isCurly) {
                if (!$tokens[$nextIndex]->isGivenKind(CT_CURLY_CLOSE)) {
                    continue;
                }

                $curlyOpenToken->clear();
                $tokens[$nextIndex]->clear();
                $nextIndex = $tokens->getNextMeaningfulToken($nextIndex);
            }

            $quote = '"';
            $requiredQuote = $prevToken->isGivenKind(T_ENCAPSED_AND_WHITESPACE)
                || $tokens[$nextIndex]->isGivenKind(T_ENCAPSED_AND_WHITESPACE)
            ;
            while ($requiredQuote) {
                $prevIndex = $tokens->getPrevMeaningfulToken($prevIndex);

                if (null === $prevIndex) {
                    break;
                }

                if ($tokens[$prevIndex]->isGivenKind(T_END_HEREDOC)) {
                    break;
                }

                if ($tokens[$prevIndex]->isGivenKind(T_START_HEREDOC)) {
                    $quote .= '""';

                    break;
                }
            }

            if ($prevToken->isGivenKind(T_ENCAPSED_AND_WHITESPACE)) {
                $prevToken->setContent($prevToken->getContent().$quote.'+str(');
            } elseif ($prevToken->equals(new Token('"'))) {
                $prevToken->setContent($prevToken->getContent().$quote.'+str(');
            }

            $nextToken = $tokens[$nextIndex];
            if ($nextToken->isGivenKind(T_ENCAPSED_AND_WHITESPACE)) {
                $nextToken->setContent(')+'.$quote.$nextToken->getContent());
            } elseif ($nextToken->equals(new Token('"'))) {
                $nextToken->setContent(')+'.$quote.$nextToken->getContent());
            }

            $content = $token->getContent();

            if (0 === strpos($content, '$')) {
                $content = substr($content, 1);
            }

            $token->setContent($content);
        }
    }

    private function convertCurlyVariable(Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_DOLLAR_OPEN_CURLY_BRACES)) {
                continue;
            }

            $token->setContent('vars()[');

            $prevIndex = $tokens->getPrevMeaningfulToken($index);
            $prevToken = $tokens[$prevIndex];

            $varIndex = $tokens->getNextMeaningfulToken($index);
            $varToken = $tokens[$varIndex];

            $closeIndex = $index;
            while (true) {
                if ($tokens[++$closeIndex]->isGivenKind(CT_DOLLAR_CLOSE_CURLY_BRACES)) {
                    break;
                }
            }
            $closeToken = $tokens[$closeIndex];

            $nextIndex = $tokens->getNextMeaningfulToken($closeIndex);
            $nextToken = $tokens[$nextIndex];

            $quote = '"';
            $requiredQuote = $prevToken->isGivenKind(T_ENCAPSED_AND_WHITESPACE)
            || $nextToken->isGivenKind(T_ENCAPSED_AND_WHITESPACE)
            ;

            $i = $prevIndex;
            while ($requiredQuote) {
                $i = $tokens->getPrevMeaningfulToken($i);

                if (null === $i) {
                    break;
                }

                if ($tokens[$i]->isGivenKind(T_END_HEREDOC)) {
                    break;
                }

                if ($tokens[$i]->isGivenKind(T_START_HEREDOC)) {
                    $quote .= '""';

                    break;
                }
            }

            if ($prevToken->isGivenKind(T_ENCAPSED_AND_WHITESPACE)) {
                $prevToken->setContent($prevToken->getContent().$quote.'+str(');
            } elseif ($prevToken->equals(new Token('"'))) {
                $prevToken->setContent($prevToken->getContent().$quote.'+str(');
            }

            if ($nextToken->isGivenKind(T_ENCAPSED_AND_WHITESPACE)) {
                $nextToken->setContent(')+'.$quote.$nextToken->getContent());
            } elseif ($nextToken->equals(new Token('"'))) {
                $nextToken->setContent(')+'.$quote.$nextToken->getContent());
            }

            if ($varToken->isGivenKind(T_STRING_VARNAME)) {
                $tokens->insertAt($varIndex + 1, new Token('"'));
                $tokens->insertAt($varIndex, new Token('"'));
            } else {
                $content = $varToken->getContent();
                if (0 === strpos($content, '$')) {
                    $varToken->setContent(substr($content, 1));
                }
            }

            $closeToken->setContent(']');
        }
    }

    private function convertIncrement(Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            $sign = '';
            $t = null;
            if ($token->isGivenKind(T_INC)) {
                $sign = '+';
                $t = T_PLUS_EQUAL;
            } elseif ($token->isGivenKind(T_DEC)) {
                $sign = '-';
                $t = T_MINUS_EQUAL;
            } else {
                continue;
            }

            $varIndex = $tokens->getNextMeaningfulToken($index);
            if ($tokens[$varIndex]->isGivenKind(T_VARIABLE)) {
                // pre increment
                $items = array();
                $items[] = new Token(array(T_WHITESPACE, ' '));
                $items[] = new Token($sign);
                $items[] = new Token(array(T_WHITESPACE, ' '));
                $items[] = new Token(array(T_LNUMBER, '1'));

                $tokens[$index]->clear();
                $tokens->insertAt($varIndex + 1, $items);

                $endStmtIndex = $tokens->getNextTokenOfKind($varIndex, array(';'));

                $items = array();
                $items[] = new Token(array(T_WHITESPACE, ' '));
                $items[] = clone $tokens[$varIndex];
                $items[] = new Token(array(T_WHITESPACE, ' '));
                $items[] = new Token(array($t, $sign.'='));
                $items[] = new Token(array(T_WHITESPACE, ' '));
                $items[] = new Token(array(T_LNUMBER, '1'));
                $items[] = new Token(';');

                $tokens->insertAt($endStmtIndex + 1, $items);
            }

            $varIndex = $tokens->getPrevMeaningfulToken($index);
            if ($tokens[$varIndex]->isGivenKind(T_VARIABLE)) {
                // post increment
                $endStmtIndex = $tokens->getNextTokenOfKind($index, array(';'));

                $items = array();
                $items[] = new Token(array(T_WHITESPACE, ' '));
                $items[] = clone $tokens[$varIndex];
                $items[] = new Token(array(T_WHITESPACE, ' '));
                $items[] = new Token(array($t, $sign.'='));
                $items[] = new Token(array(T_WHITESPACE, ' '));
                $items[] = new Token(array(T_LNUMBER, '1'));
                $items[] = new Token(';');

                $tokens[$index]->clear();
                $tokens->insertAt($endStmtIndex + 1, $items);
            }
        }
    }

    private function convertXEqual(Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            $sign = '';
            $t = null;
            if ($token->isGivenKind(T_PLUS_EQUAL)) {
                $sign = '+';
                $t = T_PLUS_EQUAL;
            } elseif ($token->isGivenKind(T_MINUS_EQUAL)) {
                $sign = '-';
                $t = T_MINUS_EQUAL;
            } else {
                continue;
            }

            $varIndex = $tokens->getPrevMeaningfulToken($index);
            if ($tokens[$varIndex]->isGivenKind(T_VARIABLE)) {
                $prevIndex = $tokens->getPrevMeaningfulToken($varIndex);
                if ($tokens[$prevIndex]->equals('=')) {
                    $items = array();
                    $items[] = new Token(array(T_WHITESPACE, ' '));
                    $items[] = clone $tokens[$varIndex];
                    $items[] = new Token(array(T_WHITESPACE, ' '));
                    $items[] = new Token($sign);

                    $tokens[$index] = new Token('=');
                    $tokens->insertAt($index + 1, $items);
                }
            }
        }
    }

    private function convertArray(Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_ARRAY)) {
                continue;
            }

            $openIndex = $tokens->getNextMeaningfulToken($index);
            if (!$tokens[$openIndex]->equals('(')) {
                continue;
            }

            $closeIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openIndex);
            if ($closeIndex === $tokens->getNextMeaningfulToken($openIndex)) {
                continue;
            }

            $tokens->insertAt($openIndex + 1, new Token('['));
            ++$closeIndex;
            $tokens->insertAt($closeIndex++, new Token(']'));

            $endValueIndex = $openIndex + 1;
            $i = 0;
            while (true) {
                $valueIndex = $tokens->getNextMeaningfulToken($endValueIndex);
                $endValueIndex = $tokens->getNextTokenOfKind($valueIndex, array(',', ']'));
                $tokens->insertAt($endValueIndex++, new Token(')'));

                $items = array(
                    new Token('('),
                    new Token(array(T_LNUMBER, $i++)),
                    new Token(','),
                    new Token(array(T_WHITESPACE, ' ')),
                );

                for ($y = $valueIndex; $endValueIndex > $y; $y++) {
                    if ($tokens[$y]->isGivenKind(T_DOUBLE_ARROW)) {
                        $items = array(
                            new Token('('),
                        );

                        $tokens->removeLeadingWhitespace($y);
                        $tokens[$y]->setContent(',');

                        break;
                    }
                }

                $tokens->insertAt($valueIndex, $items);
                $valueIndex += count($items);
                $endValueIndex += count($items);

                if ($tokens[$endValueIndex]->equals(']')) {
                    break;
                }
            }
        }
    }

    private function convertTrue(Tokens $tokens)
    {
        foreach ($tokens as $token) {
            if (!$token->isGivenKind(T_STRING)) {
                continue;
            }

            $content = $token->getContent();

            $content = preg_replace('/^true$/i', 'True', $content);

            $token->setContent($content);
        }
    }

    private function convertFalse(Tokens $tokens)
    {
        foreach ($tokens as $token) {
            if (!$token->isGivenKind(T_STRING)) {
                continue;
            }

            $content = $token->getContent();

            $content = preg_replace('/^false$/i', 'False', $content);

            $token->setContent($content);
        }
    }

    private function convertNull(Tokens $tokens)
    {
        foreach ($tokens as $token) {
            if (!$token->isGivenKind(T_STRING)) {
                continue;
            }

            $content = $token->getContent();

            $content = preg_replace('/^null$/i', 'None', $content);

            $token->setContent($content);
        }
    }

    private function convertFunction(Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_FUNCTION)) {
                continue;
            }

            $token->setContent('def');

            // function name
            $nextIndex = $tokens->getNextMeaningfulToken($index);
            if ($tokens[$nextIndex]->equals('&')) {
                // reference
                $nextIndex = $tokens->getNextMeaningfulToken($nextIndex);
            }

            // ()
            if ($tokens[$nextIndex]->equals('(')) {
                // closure
                continue;
            } else {
                $openIndex = $tokens->getNextMeaningfulToken($nextIndex);
            }
            $closeIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openIndex);

            $colonIndex = $closeIndex + 1;
            if (!$tokens[$colonIndex]->equals(new Token(':'))) {
                $tokens->insertAt($colonIndex, new Token(':'));
            }

            // {}
            $openIndex = $tokens->getNextMeaningfulToken($colonIndex);
            $closeIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $openIndex);

            $tokens[$openIndex]->clear();
            $nextToken = $tokens[$openIndex + 1];
            if ($nextToken->isWhitespace()) {
                $nextToken->setContent(preg_replace('/^.*\n/', '', $nextToken->getContent()));
            }
            $tokens[$closeIndex]->clear();
            $nextToken = $tokens[$closeIndex + 1];
            if ($nextToken->isWhitespace()) {
                $nextToken->setContent(preg_replace('/^.*\n/', '', $nextToken->getContent()));
            }
        }
    }

    private function convertFileMagicConstants(Tokens $tokens)
    {
        $this->addImport('os');

        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_FILE)) {
                continue;
            }

            $token->setContent('os.path.realpath(__file__)');
        }
    }

    private function convertDirMagicConstants(Tokens $tokens)
    {
        $this->addImport('os');

        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_DIR)) {
                continue;
            }

            $token->setContent('os.path.dirname(os.path.realpath(__file__))');
        }
    }

    private function convertNamspaceMagicConstants(Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_NS_C)) {
                continue;
            }

            $token->setContent('__name__');
        }
    }

    private function convertBoolCasting(Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_BOOL_CAST)) {
                continue;
            }

            $token->setContent('bool(');
            $tokens->removeTrailingWhitespace($index);
            $nextIndex = $tokens->getNextTokenOfKind($index, array(new Token(';')));
            $tokens->insertAt($nextIndex, new Token(')'));
        }
    }

    private function convertIntCasting(Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_INT_CAST)) {
                continue;
            }

            $token->setContent('int(');
            $tokens->removeTrailingWhitespace($index);
            $nextIndex = $tokens->getNextTokenOfKind($index, array(new Token(';')));
            $tokens->insertAt($nextIndex, new Token(')'));
        }
    }

    private function convertStringCasting(Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_STRING_CAST)) {
                continue;
            }

            $token->setContent('str(');
            $tokens->removeTrailingWhitespace($index);
            $nextIndex = $tokens->getNextTokenOfKind($index, array(new Token(';')));
            $tokens->insertAt($nextIndex, new Token(')'));
        }
    }

    private function convertNullCasting(Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_UNSET_CAST)) {
                continue;
            }

            $token->setContent('None');
            $tokens->removeTrailingWhitespace($index);
            $endIndex = $tokens->getNextTokenOfKind($index, array(';'));
            $nextIndex = $index + 1;
            while ($endIndex > $nextIndex) {
                $tokens[$nextIndex++]->clear();
            }
        }
    }

    private function convertConcatenationOperator(Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if ($token->equals(new Token('.'))) {
                $token->setContent('+');
            }

            if ($token->isGivenKind(T_CONCAT_EQUAL)) {
                $token->setContent('+=');
            }
        }
    }

    private function convertObjectOperator(Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_OBJECT_OPERATOR)) {
                continue;
            }

            $token->setContent('.');
        }
    }

    private function convertIdenticalOperator(Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_IS_IDENTICAL)) {
                continue;
            }

            $op = 'is';

            if (!$tokens[$index - 1]->isWhitespace()) {
                $op = ' '.$op;
            }

            if (!$tokens[$index + 1]->isWhitespace()) {
                $op .= ' ';
            }

            $token->setContent($op);
        }
    }

    private function convertNotIdenticalOperator(Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_IS_NOT_IDENTICAL)) {
                continue;
            }

            $op = 'is not';

            if (!$tokens[$index - 1]->isWhitespace()) {
                $op = ' '.$op;
            }

            if (!$tokens[$index + 1]->isWhitespace()) {
                $op .= ' ';
            }

            $token->setContent($op);
        }
    }

    private function convertNotOperators(Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!($token->equals(new Token('!')))) {
                continue;
            }

            $op = 'not';

            if (!($tokens[$index - 1]->isWhitespace() || $tokens[$index - 1]->equals(new Token('(')))) {
                $op = ' '.$op;
            }

            if (!$tokens[$index + 1]->isWhitespace()) {
                $op .= ' ';
            }

            $token->setContent($op);
        }
    }

    private function convertAndOperators(Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_BOOLEAN_AND)) {
                continue;
            }

            $op = 'and';

            if (!$tokens[$index - 1]->isWhitespace()) {
                $op = ' '.$op;
            }

            if (!$tokens[$index + 1]->isWhitespace()) {
                $op .= ' ';
            }

            $token->setContent($op);
        }
    }

    private function convertOrOperators(Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_BOOLEAN_OR)) {
                continue;
            }

            $op = 'or';

            if (!$tokens[$index - 1]->isWhitespace()) {
                $op = ' '.$op;
            }

            if (!$tokens[$index + 1]->isWhitespace()) {
                $op .= ' ';
            }

            $token->setContent($op);
        }
    }

    private function convertTypeOperators(Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_INSTANCEOF)) {
                continue;
            }

            $prevIndex = $tokens->getPrevMeaningfulToken($index);
            $prevToken = $tokens[$prevIndex];
            $nextIndex = $tokens->getNextMeaningfulToken($index);
            $nextToken = $tokens[$nextIndex];

            $line = $token->getLine();
            $call = array(
                new Token(array(T_STRING, 'isinstance', $line)),
                new Token('('),
                clone $prevToken,
                new Token(','),
                new Token(array(T_WHITESPACE, ' ', $line)),
            );

            $prevToken->clear();
            $tokens->removeLeadingWhitespace($index);
            $tokens->removeTrailingWhitespace($index);

            $tokens->insertAt($nextIndex + 1, new Token(')'));

            unset($tokens[$index]);
            $tokens->insertAt($index, $call);
        }
    }

    private function convertErrorControlOperators(Tokens $tokens)
    {
    }

    private function convertObjectInitialization(Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_NEW)) {
                continue;
            }

            $token->clear();

            $classIndex = $tokens->getNextMeaningfulToken($index);
            $parenthesisIndex = $tokens->getNextMeaningfulToken($classIndex);
            $parenthesisToken = $tokens[$parenthesisIndex];

            if (!$parenthesisToken->equals('(')) {
                $tokens->insertAt($classIndex + 1, array(new Token('('), new Token(')')));
            }

            $tokens->removeTrailingWhitespace($index);
        }
    }

    private function convertDeclare(Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_DECLARE)) {
                continue;
            }

            $openIndex = $tokens->getNextMeaningfulToken($index);
            $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openIndex);
            $semicolonIndex = $tokens->getNextMeaningfulToken($endIndex);
            if ($tokens[$semicolonIndex]->equals(';')) {
                $endIndex = $semicolonIndex;
            }

            $nextIndex = $tokens->getNextMeaningfulToken($openIndex);
            if (!$tokens[$nextIndex]->isGivenKind(T_STRING)
                || 'encoding' !== $tokens[$nextIndex]->getContent()
            ) {
                continue;
            }

            $equalIndex = $tokens->getNextMeaningfulToken($nextIndex);
            if (!$tokens[$equalIndex]->equals('=')) {
                continue;
            }

            $codingIndex = $tokens->getNextMeaningfulToken($equalIndex);
            if (!$tokens[$codingIndex]->isGivenKind(T_CONSTANT_ENCAPSED_STRING)) {
                continue;
            }

            $coding = substr($tokens[$codingIndex]->getContent(), 1, -1);

            for ($i = $index; $endIndex >= $i; $i++) {
                $tokens[$i]->clear();
            }

            $codingDeclaration = sprintf('# -*- coding: %s -*-', $coding);
            $tokens->insertAt($index, new Token(array(T_COMMENT, $codingDeclaration, $token->getLine())));
        }
    }

    private function convertScopeResolutionOperator(Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_PAAMAYIM_NEKUDOTAYIM)) {
                continue;
            }

            $token->setContent('.');
        }
    }

    private function convertTernaireOperator(Tokens $tokens)
    {
        $ternaryLevel = 0;

        foreach ($tokens as $index => $token) {
            if ($token->equals('?')) {
                ++$ternaryLevel;

                // condition
                $conditionIndex = $tokens->getPrevTokenOfKind($index, array(':', ';', '='));
                $conditionIndex = $tokens->getNextMeaningfulToken($conditionIndex);
                $endConditionIndex = $tokens->getPrevMeaningfulToken($index);
                $tokens->removeTrailingWhitespace($endConditionIndex);

                // else
                $elseIndex = $tokens->getNextTokenOfKind($index, array(':'));

                // if true
                $ifTrueIndex = $tokens->getNextMeaningfulToken($index);
                $endIfTrueIndex = $tokens->getPrevMeaningfulToken($elseIndex);
                $tokens->removeTrailingWhitespace($endIfTrueIndex);

                $items = array();

                $items[] = new Token('(');

                if ($elseIndex === $ifTrueIndex) {
                    for ($i = $conditionIndex; $endConditionIndex >= $i; $i++) {
                        $items[] = clone $tokens[$i];
                    }
                } else {
                    for ($i = $ifTrueIndex; $endIfTrueIndex >= $i; $i++) {
                        $items[] = clone $tokens[$i];
                        $tokens[$i]->clear();
                    }
                }

                $items[] = new Token(array(T_WHITESPACE, ' '));
                $items[] = new Token('if');
                $items[] = new Token(array(T_WHITESPACE, ' '));

                for ($i = $conditionIndex; $endConditionIndex >= $i; $i++) {
                    $items[] = clone $tokens[$i];
                    $tokens[$i]->clear();
                }

                unset($tokens[$index]);
                $tokens->insertAt($index, $items);
            } elseif ($ternaryLevel && $token->equals(':')) {
                $ifFalseIndex = $tokens->getNextMeaningfulToken($index);
                $endIfFalseIndex = $tokens->getNextTokenOfKind($ifFalseIndex, array('?', ';'));
                $endIfFalseIndex = $tokens->getPrevMeaningfulToken($endIfFalseIndex);

                $tokens->insertAt($endIfFalseIndex + 1, new Token(')'));

                $i = $index;
                while ($tokens[--$i]->isEmpty()) {
                }
                $index += $tokens->ensureWhitespaceAtIndex($i, 1, ' ');
                $i = $index;
                while ($tokens[++$i]->isEmpty()) {
                }
                $tokens->ensureWhitespaceAtIndex($i, 0, ' ');

                $token->setContent('else');

                --$ternaryLevel;
            }
        }
    }

    private function convertSingleQuote(Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_CONSTANT_ENCAPSED_STRING)) {
                continue;
            }

            $content = $token->getContent();

            if (0 === strpos($content, "'")) {
                $content = preg_replace('#([^\x5C])(\x5C(?:[nrtvfab]|[0-7]{1,3}|x[0-9A-Fa-f]{1,2}))#', '$1\\\\$2', $content);

                if (false !== strpos($content, "\n")) {
                    $content = "''".$content."''";
                }
            }

            if (0 === strpos($content, '"')) {
                $content = preg_replace('#([^\x5C])(\x5C(?:[ab]))#', '$1\\\\$2', $content);
                $content = preg_replace('#([^\x5C])(\x5C)[e]#', '$1$2x1B', $content);

                if(false !== strpos($content, "\n")) {
                    $content = '""'.$content.'""';
                }

                $content = preg_replace('#([^\x5C])\x5C([$])#', '$1$2', $content);
            }


            $token->setContent($content);
        }
    }

    private function convertDoubleQuote(Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_ENCAPSED_AND_WHITESPACE)) {
                continue;
            }

            $content = $token->getContent();

            $content = preg_replace('#([^\x5C])(\x5C(?:[ab]))#', '$1\\\\$2', $content);
            $content = preg_replace('#([^\x5C])(\x5C)[e]#', '$1$2x1B', $content);
            $content = preg_replace('#([^\x5C])\x5C([$])#', '$1$2', $content);

            $token->setContent($content);
        }
    }

    private function convertBlockQuote(Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_START_HEREDOC)) {
                continue;
            }

            $singleQuoted = false;
            if (false !== strpos($token->getContent(), "'")) {
                $singleQuoted = true;
            }

            $i = $index;
            while (!$tokens[$i]->isGivenKind(T_END_HEREDOC)) {
                $i++;

                $contentToken = $tokens[$i];

                if (!$contentToken->isGivenKind(T_ENCAPSED_AND_WHITESPACE)) {
                    continue;
                }

                $content = $contentToken->getContent();

                if ($singleQuoted) {
                    $content = preg_replace('#([^\x5C])(\x5C(?:[nrtvfab]|[0-7]{1,3}|x[0-9A-Fa-f]{1,2}))#', '$1\\\\$2', $content);
                } else {
                    $content = preg_replace('#([^\x5C])(\x5C(?:[ab]))#', '$1\\\\$2', $content);
                    $content = preg_replace('#([^\x5C])(\x5C)[e]#', '$1$2x1B', $content);
                    $content = preg_replace('#([^\x5C])\x5C([$])#', '$1$2', $content);
                }

                $contentToken->setContent($content);
            }
            $endIndex = $i;

            $token->setContent('"""\\'."\n");

            $prevEndIndex = $tokens->getPrevMeaningfulToken($endIndex);
            $tokens[$prevEndIndex]->setContent(preg_replace('/\n$/', "\\\n", $tokens[$prevEndIndex]->getContent()));

            $tokens[$endIndex]->setContent('"""');
        }
    }

    private function findVariableBlockEnd(Tokens $tokens, $index)
    {
        $nextIndex = $index + 1;
        while (true) {
            while (true) {
                $openIndex = $nextIndex;
                try {
                    $closeIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_SQUARE_BRACE, $openIndex);
                } catch (InvalidArgumentException $e) {
                    break;
                }

                $nextIndex = $tokens->getNextMeaningfulToken($openIndex);
                if ($tokens[$nextIndex]->isGivenKind(T_STRING)) {
                    $tokens[$nextIndex] = new Token(array(
                        T_CONSTANT_ENCAPSED_STRING,
                        '"'.$tokens[$nextIndex]->getContent().'"',
                        $tokens[$nextIndex]->getLine(),
                    ));
                }

                $nextIndex = $tokens->getNextMeaningfulToken($closeIndex);
            }

            if (!$tokens[$nextIndex]->isGivenKind(T_OBJECT_OPERATOR)) {
                if ($tokens[$nextIndex]->isGivenKind(T_PAAMAYIM_NEKUDOTAYIM)) {
                    $nextIndex = $tokens->getNextMeaningfulToken($nextIndex);

                    if (!$tokens[$nextIndex]->isGivenKind(T_VARIABLE)) {
                        break;
                    }

                    $content = $tokens[$nextIndex]->getContent();
                    if (0 === strpos($content, '$')) {
                        $tokens[$nextIndex]->setContent(substr($content, 1));
                    }

                    $nextIndex = $tokens->getNextMeaningfulToken($nextIndex);
                }

                break;
            }

            $nextIndex = $tokens->getNextMeaningfulToken($nextIndex);

            if (!$tokens[$nextIndex]->isGivenKind(T_STRING)) {
                break;
            }

            $nextIndex = $tokens->getNextMeaningfulToken($nextIndex);
        }

        return $nextIndex;
    }

    private function addImport($name, $from = null)
    {
        $data = array($name, $from);

        $this->imports[hash('sha256', serialize($data))] = $data;
    }

    private function doAddImports(Tokens $tokens)
    {
        $tokens->clearEmptyTokens();

        $index = $tokens->getNextMeaningfulToken(0);
        $index = $tokens->getPrevNonWhitespace($index) + 1;

        $tokens->insertAt($index, new Token(array(T_WHITESPACE, "\n")));

        ++$index;

        foreach ($this->imports as $data) {
            $index = $this->doAddImport($tokens, $index, $data[0], $data[1]);
        }
    }

    private function doAddImport(Tokens $tokens, $index, $name, $from = null)
    {
        $importStmt = array(
            new Token(array(T_WHITESPACE, "\n")),
            new Token('import'),
            new Token(array(T_WHITESPACE, ' ')),
            new Token($name),
            new Token(';'),
        );

        $tokens->insertAt($index, $importStmt);

        return $index + count($importStmt);
    }
}
