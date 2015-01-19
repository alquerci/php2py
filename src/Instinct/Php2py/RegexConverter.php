<?php

/*
 * (c) Alexandre Quercia <alquerci@email.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Instinct\Php2py;

/**
 * @author Alexandre Quercia <alquerci@email.com>
 */
class Converter implements ConverterInterface
{
    private $patterns = array(
        array('public\s+([\w\s]*)\s+(\w+)\((.*)\)', '\1 \2(\3)', 1),
        array('protected\s+([\w\s]*)\s+(\w+)\((.*)\)', '\1 _\2(\3)', 1),
        array('private\s+([\w\s]*)\s+(\w+)\((.*)\)', '\1 __\2(\3)', 1),
        array('( +)([\w\s]*)function\s+(\w+)\((.*?)(\w+)\s+(\$\w+)(.*)\)', "\\1\\2function \\3(\\4\\6\\7)\n\\1    assert isinstance(\\6, \\5);", 10),
        array('( +)([\w\s]*)function\s+(\w+)\((.*)\);?', '\1\2def \3(self, \4):', 1),
        array('public([\w\s]*)\$(\w+)(\s+=\s+.+?);', '#\1 self.\2\3;', 1),
        array('protected([\w\s]*)\$(\w+)(\s+=\s+.+?);', '#\1 self._\2\3;', 1),
        array('private([\w\s]*)\$(\w+)(\s+=\s+.+?);', '#\1 self.__\2\3;', 1),
        array('const\s+(\w+)(\s+=\s+.+?);', '\1\2;', 1),
        array('public([\w\s]*)\$(\w+);', '#\1 self.\2 = None;', 1),
        array('protected([\w\s]*)\$(\w+);', '#\1 self._\2 = None;', 1),
        array('private([\w\s]*)\$(\w+);', '#\1 self.__\2 = None;', 1),
        array('(\$)this', '\1self', 1),
        array('\$(\w)', '\1', 1),

//         // comments
        array('(/\*[^\n]*[^\*]*?\n[ ]+)\*([^/])', '\1  \2', 30),
        // array('(/\*[^\n]*[^@]*?\n[ ]+)(@[\w]+)([^:\w]|\s)', '\1\2:\3', 30),
        array('/\*[^\w@\s]*((?:.|\s)*?)\*/\n(\s*)(.*)', "\\3\n\\2    \"\"\"\\1\n\\2    \"\"\"", 1),

        array('(\n[ ]*)interface\s+([\w\s]+)([\{\n])', '\1@interface\1class \2\3', 1),
        array('(\n[ ]*)abstract[ ]+class\s+([\w\s]+)([\{\n])', '\1@abstract\1class \2\3', 1),
        array('(\n)class\s+(\w+)([\{\n])', '\1class \2(Object):\3', 1),
        array('(\n)class\s+(\w+)\s+(.*?)([\{\n])', '\1class \2(\3):\4', 1),
        array('(\n)class\s+(\w+)\((?:extends)(.*?)(?:implements)(.*?)\):', '\1class \2(\3, \4):', 1),
        array('(\n)class\s+(\w+)\(extends +(.+?)\):', '\1class \2(\3):', 1),
        array('(\s+)\{([^\{\}]*)\}(\n)', '\1\2\3', 10), // remove body { }
        array('(def|class) (\w+)\((?:,\s*)?([\w,]*)\)', '\1 \2(\3)', 1),
        array('(def|class) (\w+)\(([\w,]*)(?:,\s*)?\)', '\1 \2(\3)', 1),
        array('__construct\(', '__init__(', 1),
        array('__destruct\(', '__del__(', 1),
        array('(null|NULL)', 'None', 1),
        array('(true|TRUE)', 'True', 1),
        array('(false|FALSE)', 'False', 1),
        array('(\&\&)', 'and', 1),
        array('(\|\|)', 'or', 1),
        array('(\w+)(?:->|::)(\w+)', '\1.\2', 2),
        array('(None|False|True)\s*===\s*', '\1 is ', 1),
        array('(None|False|True)\s*!==\s*', '\1 is not ', 1),
        array('new\s+(\x5C?\w+\()', '\1', 1),
        array('throws?(\s+)', 'raise\1', 1),
        array('(\s+)\x5C(\w+)', '\1\2', 1),
        array('(if\s+.*)!', '\1 not ', 1),
        array('elseif', 'elif', 1),
        array('(else|if)(.*)', '\1\2:', 1),
        array('(\w+) instanceof (\w+)', 'isinstance(\1, \2)', 1),
        array(' +\n', "\n", 1),
    );

    /**
     * {@inheritDoc}
     */
    public function convert($content, $force = false)
    {
//         foreach ($this->patterns as $pattern) {
//             for ($i = 0; $pattern[2] > $i; $i++) {
//                 $content = preg_replace('#'.$pattern[0].'#', $pattern[1], $content);
//             }
//         }

        // tags
        if (!$force && !preg_match('#^<\?php\n*#', $content)) {
            return $content;
        }
        $content = $this->removePhpTags($content);

        // operators
        $content = $this->convertTernaireOperator($content);
        $content = $this->convertConcatenationOperator($content);
        $content = $this->convertObjectOperator($content);
        $content = $this->convertScopeResolutionOperator($content);
        $content = $this->convertIdenticalOperator($content);
        $content = $this->convertNotIdenticalOperator($content);
        $content = $this->convertErrorControlOperators($content);
        $content = $this->convertNotOperators($content);
        $content = $this->convertAndOperators($content);
        $content = $this->convertOrOperators($content);
        $content = $this->convertTypeOperators($content);

        // code structures
        $content = $this->convertVariable($content);
        $content = $this->convertBrace($content);

        // keywords
        $content = $this->convertTrue($content);
        $content = $this->convertFalse($content);
        $content = $this->convertNull($content);
        $content = $this->convertFunction($content);
        $content = $this->convertObjectInitialization($content);

        // cast
        $content = $this->convertBoolCasting($content);
        $content = $this->convertIntCasting($content);
        $content = $this->convertStringCasting($content);
        $content = $this->convertNullCasting($content);

        // constants
        $content = $this->convertDirMagicConstants($content);
        $content = $this->convertFileMagicConstants($content);
        $content = $this->convertNamspaceMagicConstants($content);

        // string
        $content = $this->convertSingleQuote($content);
        $content = $this->convertDoubleQuote($content);
        $content = $this->convertBlockQuote($content);

        // comments
        $content = $this->convertOneLineComments($content);
        $content = $this->convertMultiLineComments($content);

        return $content;
    }

    private function removePhpTags($content)
    {
        $content = preg_replace('#^<\?php\n*#', '', $content);

        return $content;
    }

    private function convertOneLineComments($content)
    {
        $content = preg_replace('#//(.*)$#m', '#$1', $content);

        return $content;
    }

    private function convertMultiLineComments($content)
    {
        $pattern = <<<'EOF'
/(?xs:
    \/\*
    \s*\*?\s* # white space before the first line
    (.*?)
    \x20?\*\/
)/
EOF;

        $content = preg_replace_callback(
            $pattern,
            function (array $matches) {
                $content = $matches[1];
                $content = preg_replace('/^(\s*) \* /m', '$1', $content);

                return '"""'.$content.'"""';
            },
            $content
        );

        return $content;
    }

    private function convertVariable($content)
    {
        // ${?${?${?
        return $this->convertCode(<<<'EOF'
(?x:
    (?<!\x5C|\{|\$)
    (?P<rvalue>=\s*)?
    (?P<pre_increment>(?P<pre_increment_sign>[+-]){2})?
    \$
    (\{)?
    (?P<name>[_[:alpha:]][_[:alnum:]]*)
    (?(4)\})
    (?(3)|(?P<post_increment>(?P<post_increment_sign>[+-]){2})?)
    (?(1)(?P<rvalue_increment>\s*(?P<rvalue_increment_sign>[+-])=)?)
)
EOF
            ,
            function (array $matches) {
                $varString = $matches['name'];

                if (!empty($matches['pre_increment'])) {
                    $op = $matches['pre_increment_sign'];
                    $varString .= ' '.$op.' 1';
                    $varString .= '; '.$matches['name'].' '.$op.'= 1';
                }

                if (!empty($matches['post_increment'])) {
                    $op = $matches['post_increment_sign'];
                    $varString = $varString.'; '.$varString;
                    $varString .= ' '.$op.'= 1';
                }

                if (!empty($matches['rvalue'])) {
                    $varString = $matches['rvalue'].$varString;

                    if (!empty($matches['rvalue_increment'])) {
                        $op = $matches['rvalue_increment_sign'];
                        $varString .= ' '.$varString.' '.$op.'';
                    }
                }

                return $varString;
            },
            $content
        );
    }

    private function convertBrace($content)
    {
        // open
        $content = $this->convertCode('\n\{\n', function (array $matches) {
            return ":\n";
        }, $content);

        // close
        return $this->convertCode('\}\n', function (array $matches) {
            return '';
        }, $content);
    }

    private function convertTrue($content)
    {
        return $this->convertCode('(?P<true>(?i:true))', function (array $matches) {
            return 'True';
        }, $content);
    }

    private function convertFalse($content)
    {
        return $this->convertCode('(?P<false>(?i:false))', function (array $matches) {
            return 'False';
        }, $content);
    }

    private function convertNull($content)
    {
        return $this->convertCode('(?P<null>(?i:null))', function (array $matches) {
            return 'None';
        }, $content);
    }

    private function convertFunction($content)
    {
        return $this->convertCode('(?P<code>(?i:function))', function (array $matches) {
            return 'def';
        }, $content);
    }

    private function convertFileMagicConstants($content)
    {
        $this->addImport('os');

        return $this->convertCode('(?P<code>__FILE__)', function (array $matches) {
            return 'os.path.realpath(__file__)';
        }, $content);
    }

    private function convertDirMagicConstants($content)
    {
        $this->addImport('os');

        return $this->convertCode('(?P<code>__DIR__)', function (array $matches) {
            return 'os.path.dirname(__FILE__)';
        }, $content);
    }

    private function convertNamspaceMagicConstants($content)
    {
        return $this->convertCode('(?P<code>__NAMESPACE__)', function (array $matches) {
            return '__name__';
        }, $content);
    }

    private function convertBoolCasting($content)
    {
        return $this->convertCode('(?i:\(bool(?:ean)?\)) ?(?P<stmt>.*);', function (array $matches) {
            return sprintf('bool(%s);', $matches['stmt']);
        }, $content);
    }

    private function convertIntCasting($content)
    {
        return $this->convertCode('(?i:\(int(?:eger)?\)) ?(?P<stmt>.*);', function (array $matches) {
            return sprintf('int(%s);', $matches['stmt']);
        }, $content);
    }

    private function convertStringCasting($content)
    {
        return $this->convertCode('(?i:\(string\)) ?(?P<stmt>.*);', function (array $matches) {
            return sprintf('str(%s);', $matches['stmt']);
        }, $content);
    }

    private function convertNullCasting($content)
    {
        return $this->convertCode('(?i:\(unset\)) ?(?P<stmt>.*);', function (array $matches) {
            return 'None;';
        }, $content);
    }

    private function convertConcatenationOperator($content)
    {
        return $this->convertCode('(?<!\d)\.(?!\d)', function (array $matches) {
            return '+';
        }, $content);
    }

    private function convertObjectOperator($content)
    {
        return $this->convertCode('->', function (array $matches) {
            return '.';
        }, $content);
    }

    private function convertIdenticalOperator($content)
    {
        return $this->convertCode('\s?===\s?', function (array $matches) {
            return ' is ';
        }, $content);
    }

    private function convertNotIdenticalOperator($content)
    {
        return $this->convertCode('\s?!==\s?', function (array $matches) {
            return ' is not ';
        }, $content);
    }

    private function convertNotOperators($content)
    {
        return $this->convertCode('!\s?', function (array $matches) {
            return 'not ';
        }, $content);
    }

    private function convertAndOperators($content)
    {
        return $this->convertCode('\s?&&\s?', function (array $matches) {
            return ' and ';
        }, $content);
    }

    private function convertOrOperators($content)
    {
        return $this->convertCode('\s?\|\|\s?', function (array $matches) {
            return ' or ';
        }, $content);
    }

    private function convertTypeOperators($content)
    {
        return $this->convertCode(
            '(?P<var>\$[_[:alpha:]][_[:alnum:]]*)\s*instanceof\s*(?P<class>[_[:alpha:]][_[:alnum:]]*)',
            function (array $matches) {
                return sprintf('isinstance(%s, %s)', $matches['var'], $matches['class']);
            },
            $content
        );
    }

    private function convertErrorControlOperators($content)
    {
        return $content;
    }

    private function convertObjectInitialization($content)
    {
        return $this->convertCode(
            '(?<=\s)new\s+(?P<class>[_[:alpha:]][_[:alnum:]]*)(?P<args>\()?',
            function (array $matches) {
                $init = $matches['class'];

                if (empty($matches['args'])) {
                    $init .= '()';
                }

                return $init;
            },
            $content
        );
    }

    private function convertScopeResolutionOperator($content)
    {
        return $this->convertCode('::', function (array $matches) {
            return '.';
        }, $content);
    }

    private function convertTernaireOperator($content)
    {
        return $content;
    }

    private function convertSingleQuote($content)
    {
        $content = preg_replace_callback(
            sprintf('/%s|%s|%s|(?P<pattern>%s)/',
                $this->getCommentRegexp(),
                $this->getBlockQuotedRegexp(),
                $this->getDoubleQuotedRegexp(),
                $this->getSingleQuotedRegexp()
            ),
            function (array $matches) {
                if (!isset($matches['pattern'])) {
                    return $matches[0];
                }

                $content = $matches['pattern'];

                if (false !== strpos($content, "\n")) {
                    $content = "''".$content."''";
                }

                $content = preg_replace('#([^\x5C])(\x5C(?:[nrtvfab]|[0-7]{1,3}|x[0-9A-Fa-f]{1,2}))#', '$1\\\\$2', $content);

                return $content;
            },
            $content
        );

        return $content;
    }

    private function convertDoubleQuote($content)
    {
        $self = $this;

        $content = preg_replace_callback(
            sprintf('/%s|%s|%s|(?P<pattern>%s)/',
                $this->getCommentRegexp(),
                $this->getBlockQuotedRegexp(),
                $this->getSingleQuotedRegexp(),
                $this->getDoubleQuotedRegexp()
            ),
            function (array $matches) use ($self) {
                if (!isset($matches['pattern'])) {
                    return $matches[0];
                }

                $content = $matches['pattern'];

                if (false !== strpos($content, "\n")) {
                    $content = '""'.$content.'""';
                }

                $content = preg_replace('#([^\x5C])(\x5C(?:[ab]))#', '$1\\\\$2', $content);
                $content = preg_replace('#([^\x5C])(\x5C)[e]#', '$1$2x1B', $content);

                // Variable parsing
                list($content, $exprs) = $self->variableParssing($content);

                if ($exprs) {
                    $content .= sprintf('.format(%s)', implode(', ', $exprs));
                }

                return $content;
            },
            $content
        );

        return $content;
    }

    private function convertBlockQuote($content)
    {
        $self = $this;

        $content = preg_replace_callback(
            sprintf('/%s|%s|%s|(?P<pattern>%s)/',
                $this->getCommentRegexp(),
                $this->getSingleQuotedRegexp(),
                $this->getDoubleQuotedRegexp(),
                $this->getBlockQuotedRegexp()
            ),
            function (array $matches) use ($self) {
                if (!isset($matches['pattern'])) {
                    return $matches[0];
                }

                $content = $matches['nowdoc_content'];
                $format = '';

                if ("'" === $matches['nowdoc_quote']) {
                    $content = preg_replace('#([^\x5C])(\x5C(?:[nrtvfab]|[0-7]{1,3}|x[0-9A-Fa-f]{1,2}))#', '$1\\\\$2', $content);
                    $content = preg_replace('#([^\x5C])(\x5C)[e]#', '$1\\\\$2x1B', $content);
                } else {
                    $content = preg_replace('#([^\x5C])(\x5C(?:[ab]))#', '$1\\\\$2', $content);
                    $content = preg_replace('#([^\x5C])(\x5C)[e]#', '$1$2x1B', $content);

                    // Variable parsing
                    list($content, $exprs) = $self->variableParssing($content);

                    if ($exprs) {
                        $format = sprintf('.format(%s)', implode(', ', $exprs));
                    }
                }

                return '"""\\'.$content."\\\n".'"""'.$format;
            },
            $content
        );

        return $content;
    }

    private function variableParssing($content)
    {
        $pattern = <<<'EOF'
/(?x:
    (?<!\x5C|\{|\$|>|:)
    (\{{1,2})?
    (?P<expr>
        \$
        (\{{1,2})?
        [_[:alpha:]][_[:alnum:]]*
        (?(3)
            \}{1,2}
            |
            (?(1)
                (?:
                    (?:
                        (?:->|::\$)
                        ([_[:alpha:]][_[:alnum:]]*)
                    )?
                    (?:\[\S*?\])*
                )*
                |
                (?:
                    (?:\[\S*?\])
                    |
                    (?:->[_[:alpha:]][_[:alnum:]]*)
                )?
            )
        )
    )
    (?(1)\}{1,2})
)/
EOF;

        if (preg_match($pattern, $content)) {
            $content = strtr($content, array('{' => '{{', '}' => '}}'));
        }

        $self = $this;
        $exprs = array();
        $id = 0;
        $content = preg_replace_callback(
            $pattern,
            function (array $matches) use ($self, &$exprs, &$id) {
                $expr = $matches['expr'];

                $expr = strtr($expr, array('{{' => '{', '}}' => '}'));
                $expr = $self->convert($expr, true);

                $exprs[] = $expr;

                return sprintf('{%d}', $id++);
            },
            $content
        );

        $content = preg_replace('#([^\x5C])\x5C([$])#', '$1$2', $content);

        return array(
            $content,
            $exprs,
        );
    }

    private function convertCode($pattern, $callback, $content)
    {
        $pattern = preg_replace_callback(
            '/(?<!\x5C)(?<=\(\?\()(\d+)(?=\))/',
            function (array $matches) {
                return 5 + $matches[1];
            },
            $pattern
        );

        $content = preg_replace_callback(
            sprintf('/%s|%s|(?P<pattern>%s)/',
                $this->getCommentRegexp(),
                $this->getQuotedRegexp(),
                $pattern
            ),
            function (array $matches) use ($callback) {
                if (!isset($matches['pattern'])) {
                    return $matches[0];
                }

                return $callback($matches);
            },
            $content
        );

        return $content;
    }

    private function getSingleQuotedRegexp()
    {
        $regex = <<<'EOF'
(?x:
    (?: # single quoted string
        '
            [^\\']*+
            (?:\\.[^\\']*+)*+
        '
    )
)
EOF;

        return $regex;
    }

    private function getDoubleQuotedRegexp()
    {
        $regex = <<<'EOF'
(?x:
    (?: # double quoted string
        "
            [^\\"]*+
            (?:\\.[^\\"]*+)*+
        "
    )
)
EOF;

        return $regex;
    }

    private function getBlockQuotedRegexp()
    {
        $regex = <<<'EOF'
(?x:
    (?: # heredoc and nowdoc
        <<<(?P<nowdoc_quote>["']?) (?P<nowdoc_delimiter>[_[:alpha:]][_[:alnum:]]*) (?P=nowdoc_quote)
           (?P<nowdoc_content>\C*?(?!(?P=nowdoc_end)))
           (?P<nowdoc_end>\n(?P=nowdoc_delimiter))
    )
)
EOF;

        return $regex;
    }

    private function getQuotedRegexp()
    {
        $regex = <<<EOF
(?x:
    {$this->getBlockQuotedRegexp()}
    |{$this->getSingleQuotedRegexp()}
    |{$this->getDoubleQuotedRegexp()}
)
EOF;

        return $regex;
    }

    private function getCommentRegexp()
    {
        $regex = <<<'EOF'
(?x:
    (?s: # bloc
        \/\*
        .*?
        \*\/
    )
    |(?: # inline
        (?:\/\/|\#)
        [^\n]*
    )
)
EOF;

        return $regex;
    }

    private function addImport($name, $from = null)
    {
        // TODO
    }
}
