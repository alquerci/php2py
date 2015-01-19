<?php

/*
 * (c) Alexandre Quercia <alquerci@email.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Instinct\Php2py\Tests;

use Instinct\Php2py\Converter;

/**
 * @author Alexandre Quercia <alquerci@email.com>
 */
class ConverterTest extends \PHPUnit_Framework_TestCase
{
    public function testConvert()
    {
        $code = file_get_contents(__DIR__.'/Fixtures/foo.php');

        $expected = file_get_contents(__DIR__.'/Fixtures/foo.py');

        $converter = new Converter();

        $this->assertEquals($expected, $converted = $converter->convert($code));
        $this->assertEquals($expected, $converter->convert($converted));
    }

    public function _testConvert()
    {
        $code = <<<'EOF'
<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * use Foo;
 * don't class Foo;
 * class Bar;
 * namespace Foo;
 * __DIR__
 */

namespace Baz\Booz;

use Foo\Bar;
use Foo\Baz;
use Bar\Baz;
use Foo\Bar\Booz as BaseBooz;

class FooBar extends Bar
{
    public $foo;
    proteced $bar = 'bar';
    static private $baz;

    /**
     * use Foo;
     * don't class Foo;
     * don"t class Bar;
     * namespace Foo;
     * @covers Bar\Foo::setClass
     * @covers \Bar\Foo::BaseBooz
     * @covers \Bar\Foo::Foo
     * __DIR__
     *
     * @return Foo
     */
    public function Foo(Foo $Foo = "\'",Foo $Foo = '\'')
    {
        // use Foo;
        // class Foo;
        // class Bar;
        // namespace Foo;
        // __DIR__
        $Bar = new \Bar('Bar\Foo');
        $Bar = new Bar('Bar\Foo');
        $Bar = Bar::FOO;Bar::foo();
        $Bar = FooBar::FOO;FooBar::foo();
        echo (FooBar::FOO),FooBar::BAR;
        $interface = __DIR__;
        $class = __NAMESPACE__;
        $Bar->class = 'foo';
        $Bar->interface = 'foo';

        if ($Bar instanceof \Bar) {
            $Bar->Foo = $Foo->Bar;
        }

        return new Foo();
    }
}

interface FooInterface
{
}

class Foo extends BaseBooz implements FooInterface
{
    /**
     * @api
     */
    public function Bar()
    {
    }
}

\$b = <<<'TEXT'
use Bar\Foo;
namespace Bar\Foo;
Foo TEXT
Bar
TEXT;
EOF;

        $result = <<<'EOF'

# For the full copyright and license information, please view the LICENSE
# file that was distributed with this source code.
# use Foo;
# don't class Foo;
# class Bar;
# namespace Foo;
# __DIR__

from __future__ import absolute_import
from __future__ import print_function

import os

from pymfony.component.system import Object
from pymfony.component.system.oop import interface
from Foo import Bar as _Bar
from Foo import Baz
from Bar import Baz
from Foo.Bar import Booz as BaseBooz

class FooBar(Bar):
    __baz = None

    def __init__(self):
        self.foo = None
        self._bar = 'bar'

    def Foo(self, Foo = "\'",Foo = '\''):
        """use Foo;
        don't class Foo;
        don"t class Bar;
        namespace Foo;
        @covers Bar\Foo::setClass
        @covers \Bar\Foo::BaseBooz
        @covers \Bar\Foo::Foo

        @return Foo
        """
        assert isinstance(Foo, Foo)
        assert isinstance(Foo, Foo)

        # use Foo;
        # class Foo;
        # class Bar;
        # namespace Foo;
        # __DIR__
        Bar = Bar('Bar\Foo')
        Bar = _Bar('Bar\Foo')
        Bar = Bar.FOO;Bar.foo()
        Bar = FooBar.FOO;FooBar.foo()
        print(FooBar.FOO+FooBar.BAR, end="")
        interface = os.path.dirname(os.path.realpath(os.path.abspath(__file__)))
        _class = __name__
        Bar._class = 'foo'
        Bar.interface = 'foo'

        if isinstance(Bar, Bar) :
            Bar.Foo = Foo.Bar

        return Foo()

@interface
class FooInterface(Object):
    pass

class Foo(BaseBooz, FooInterface):
    def Bar(self):
        """@api
        """

b = r"""
use Bar\Foo;
namespace Bar\Foo;
Foo TEXT
Bar
"""
EOF;

        $converter = new Converter();

        $this->assertEquals($result, $converted = $converter->convert($code));
        $this->assertEquals($result, $converter->convert($converted));
    }
}
