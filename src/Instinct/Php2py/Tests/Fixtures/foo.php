<?php
declare(encoding='ISO-8859-1');
// code here

// This is a one-line c++ style comment

/* This is a multi line comment
   yet another line of comment */

/*
 * This is a pretty multi line comment
 * yet another pretty line of comment
 *  * with a list
 */

function foo()
{
    /*
     * This is an indented pretty multi line comment
     * yet another pretty line of comment
     *  * with a list
     */
}

$a_bool = TRUE;   // a boolean
$a_bool = FALSE;  // a boolean
$a_str  = "foo";  // a string
$a_str2 = 'foo';  // a string
$an_int = 12;     // an integer

$foo = True; // assign the value TRUE to $foo

(bool) "";            // bool(false)
(boolean) 1;          // bool(true)
(Bool) -2;            // bool(true)
(bOol) "foo";         // bool(true)
(boOl) 2.3e5;         // bool(true)
(booL) array(12);     // bool(true)
(BOOL) array();       // bool(false)
(BOOLEAN) "false";    // bool(true)

$a = 1234; // decimal number
$a = -123; // a negative number
$a = 0123; // octal number (equivalent to 83 decimal)
$a = 0x1A; // hexadecimal number (equivalent to 26 decimal)
$a = 0b11111111; // binary number (equivalent to 255 decimal)

(iNt) (25/7); // int(3)
(inteGer) ( (0.1+0.7) * 10 ); // 7

$a = 1.234;
$b = 1.2e3;
$c = 7E-10;

// Simple quote
'this is a simple string';

'You can also have embedded newlines in
strings this way as it is
okay to do';

// Outputs: Arnold once said: "I'll be back"
'Arnold once said: "I\'ll be back"';

// Outputs: You deleted C:\*.*?
'You deleted C:\\*.*?';

// Outputs: You deleted C:\*.*?
'You deleted C:\*.*?';

// Outputs: This will not expand: \n a newline
'This will not expand: \n a newline';

// Outputs: Variables do not $expand $either
'Variables do not $expand $either';

// Escaped characters
'\n';
'\r';
'\t';
'\v';
'\e';
'\\foo\\';
'\x\\';
'\f';
'\0';
'\012';
'\x0';
'\x01';
'\a';
'\b';

// Double quote
"this is a simple string";

"You can also have embedded newlines in
strings this way as it is
okay to do";

// Outputs: Arnold once said: "I'll be back"
"Arnold once said: \"I'll be back\"";

// Outputs: You deleted C:\*.*?
"You deleted C:\\*.*?";

// Outputs: You deleted C:\*.*?
"You deleted C:\*.*?";

// Outputs: This will not expand: \n a newline
"This will not expand: \\n a newline";

// Outputs: Variables do not $expand $either
"Variables do not \$expand \$either";

// Escaped characters
"\n";
"\r";
"\t";
"\v";
"\e";
"\\foo\\";
"\x\\";
"\f";
"\0";
"\012";
"\x0";
"\x01";
"\a";
"\b";
"\$";

// Heredoc
$foo = <<<EOD
Example of string
spanning multiple lines
using heredoc syntax.
EOD;

$foo = <<<EOT
This should print a capital 'A': \x41
EOT;

array(<<<EOD
foobar!
EOD
);

$foo = <<<"FOOBAR"
Hello World!
FOOBAR;

// nowdoc
$foo = <<<'EOD'
Example of string
spanning multiple lines
using nowdoc syntax.
EOD;

$foo = <<<'EOT'
My name is "$name". I am printing some $foo->foo.
Now, I am printing some {$foo->bar[1]}.
This should not print a capital 'A': \x41
EOT;

// Variable parsing
$juice = "apple";
"He drank some $juice juice.";
"He drank some $juice juice. { foo bar }";
$juices = array("apple", "orange", "koolaid1" => "purple");
"He drank some $juices[0] juice.";
"He drank some $juices[1] juice.";
"He drank some $juices[koolaid1] juice.";

class Foo implements ArrayAccess
{
    public $values;
    public $name = 'yes';
    public $bar = 'obj';
    public $foo;

    public function __construct()
    {
        $this->values = new Bar();
        $this->foo = new Bar();
    }

    public function getName()
    {
        return 'obj';
    }

    public function do_foo()
    {
    }

    public function __toString()
    {
        return 'obj';
    }

    public function offsetGet($offset)
    {
        switch ($offset) {
            case 0:
            case 3:
                return $this;
            case 4:
            case 'foo':
                return array(0, 1, 2, $this);
            default:
                break;
        }

        return 'yes';
    }

    public function offsetExists($offset) {}
    public function offsetSet($offset, $value) {}
    public function offsetUnset($offset) {}
}

class Bar extends Foo
{
    public function __construct()
    {
    }
}

class beers
{
    const softdrink = 'obj';

    public static $ale = 'obj';
}

class people {
    public $john = "John Smith";
    public $jane = "Jane Smith";
    public $robert = "Robert Paulsen";

    public $smith = "Smith";
}

$people = new people();

"$people->john drank some $juices[0] juice.";
"$people->john then said hello to $people->jane.";
"$people->john's wife greeted $people->robert.";

$foo = new Foo();
"Now, I am printing some $foo->bar[1].";

<<<EOD
He drank some $juice juice.
He drank some $juice juice. { foo bar }
He drank some $juices[0] juice.
He drank some $juices[1] juice.
He drank some $juices[koolaid1] juice.
$people->john drank some $juices[0] juice.
$people->john then said hello to $people->jane.
$people->john's wife greeted $people->robert.
Now, I am printing some $foo->bar[1].
EOD;

// Complex (curly) syntax
$foo = <<<EOT
Now, I am printing some {$foo->bar[1]}.
EOT;

$great = 'fantastic';

// Won't work, outputs: This is { fantastic}
"This is { $great}";

// Works, outputs: This is fantastic
"This is {$great}";
"This is ${great}";

// Works, outputs: This is fantastic->foo
"This is ${great}->foo";

class square {
    public $width = 42;
}

$square = new square();

// Works
"This square is {$square->width}00 centimeters broad.";

$arr = new Foo();

// Works, quoted keys only work using the curly brace syntax
"This works: {$arr['key']}";
"This works: {$arr->foo['key']}";

// Works
"This works: {$arr[4][3]}";

define('foo', 4);

// This is wrong for the same reason as $foo[bar] is wrong  outside a string.
// In other words, it will still work, but only because PHP first looks for a
// constant named foo; an error of level E_NOTICE (undefined constant) will be
// thrown.
"This is wrong: {$arr[foo][3]}";

// Works. When using multi-dimensional arrays, always use braces around arrays
// when inside of strings
"This works: {$arr['foo'][3]}";

$obj = new Foo();

"This works too: {$obj->values->name}";
"This works too: {$obj->values[3]->name}";

$name = 'obj';

function getName()
{
    return 'obj';
}

$foo = new Foo();
$bar = 'name';
$baz = array(0, 'values');

// not supported yet
"This is the value of the var named $name: {${$name}}";
"This is the value of the var named by the return value of getName(): {${getName()}}";
"This is the value of the var named by the return value of \$object->getName(): {${$obj->getName()}}";
"I'd like an {${beers::softdrink}}\n";
"I'd like an {${beers::$ale}}\n";
"{$foo->$bar}\n";
"{$foo->$baz[1]}\n";

// Won't work, outputs: This is the return value of getName(): {getName()}
"This is the return value of getName(): {getName()}";

$beers = 'beers';

"I'd like an {$beers::$ale}\n";

// Get the first character of a string
$foo = 'This is a test.';
$first = $foo[0];

// Concatenation
"This works: " . $arr['foo'][3];

// String casting
(String) "";            // string("")
(sTring) 1;          // string("1")
(stRing) -2;            // string("-2")
(strIng) "foo";         // string("foo")
(striNg) 2.3e5;         // string("2.3e5")
@(strinG) array(12);     // string("12")
@(STRING) array();       // string("")
(StrinG) "false";    // string("false")

// Object Initialization
$bar = new foo;
$bar->do_foo();

// NULL
$var = NULL;
$var = Null;
$var = nUll;
$var = nuLl;
$var = nulL;
$var = null;

(unset) $foo;

// Variable variables
$a = new Foo();

"$a ${$a}";
"$a ${$a->foo}";
"$a ${$a->foo[0]}";
"$a ${$a->foo[0]->bar}";

// not supported yet
// $$a = 'world';

// MagicConstants
__FILE__;
__DIR__;
__NAMESPACE__;

// Expressions
$second = 's';
$third = 't';
$first ? $second : $third;
$first?$second:$third;
$first?:$third;

function double($i)
{
    return $i*2;
}
$b = $a = 5;        /* assign the value five into the variable $a and $b */
$c = $a++;          /* post-increment, assign original value of $a
                       (5) to $c */
$e = $d = ++$b;     /* pre-increment, assign the incremented value of
                       $b (6) to $d and $e */

/* at this point, both $d and $e are equal to 6 */

$f = double($d++);  /* assign twice the value of $d before
                       the increment, 2*6 = 12 to $f */
$g = double(++$e);  /* assign twice the value of $e after
                       the increment, 2*7 = 14 to $g */
$h = $g += 10;      /* first, $g is incremented by 10 and ends with the
                       value of 24. the value of the assignment (24) is
                       then assigned into $h, and $h ends with the value
                       of 24 as well. */

// Operator Precedence
$a = 3 * 3 % 5; // (3 * 3) % 5 = 4
// ternary operator associativity differs from C/C++
$a = true ? 0 : true ? 1 : 2; // (true ? 0 : true) ? 1 : 2 = 2

$a = 1;
$b = 2;
$a = $b += 3; // $a = ($b += 3) -> $a = 5, $b = 5

// not supported
// $a = ($b = 4) + 5;

$a = 3;
$a += 5; // sets $a to 8, as if we had said: $a = $a + 5;
$b = "Hello ";
$b .= "There!"; // sets $b to "Hello There!", just like $b = $b . "There!";

$a === $b;
$a !== $b;

$a--;
--$a;

$a = (!foo());
$a = (false && foo());
$b = (true  || foo());
$c = (false and foo());
$d = (true  or  foo());
$a instanceof MyClass;


// if
if ($a > $b)
    "a is bigger than b";

if ($a > $b) {
    "a is bigger than b";
    $b = $a;
}


// else
if ($a > $b) {
    "a is greater than b";
} else {
    "a is NOT greater than b";
}


// elseif/else if
if ($a > $b) {
    "a is bigger than b";
} elseif ($a == $b) {
    "a is equal to b";
} else {
    "a is smaller than b";
}

if($a > $b):
    $a." is greater than ".$b;
elseif($a == $b): // Note the combination of the words.
    $a." equals ".$b;
else:
    $a." is neither greater than or equal to ".$b;
endif;

// Alternative syntax for control structures
if ($a == 5):
    "a equals 5";
    "...";
elseif ($a == 6):
    "a equals 6";
    "!!!";
else:
    "a is neither 5 nor 6";
endif;


// while
/* example 1 */

$i = 1;
while ($i <= 10) {
    $i++;  /* the printed value would be
              $i before the increment
              (post-increment) */
}

/* example 2 */

$i = 1;
while ($i <= 10):
    $i;
    $i++;
endwhile;


// do-while
$i = 0;
do {
    $i;
} while ($i > 0);

$factor = 2;
$minimum_limit = 42;
do {
    if ($i < 5) {
        "i is not big enough";
        break;
    }
    $i *= $factor;
    if ($i < $minimum_limit) {
        break;
    }
    "i is ok";

    /* process i */

} while (0);


// for
/* example 1 */

for ($i = 1; $i <= 10; $i++) {
    $i;
}

/* example 2 */

for ($i = 1; ; $i++) {
    if ($i > 10) {
        break;
    }
    $i;
}

/* example 3 */

$i = 1;
for (; ; ) {
    if ($i > 10) {
        break;
    }
    $i;
    $i++;
}

/* example 4 */

for ($i = 1, $j = 0; $i <= 10; $j += $i, $i, $i++);

/*
 * This is an array with some data we want to modify
 * when running through the for loop.
 */
$people = array(
    array('name' => 'Kalle', 'salt' => 856412),
    array('name' => 'Pierre', 'salt' => 215863)
);

for($i = 0; $i < count($people); ++$i) {
    $people[$i]['salt'] = mt_rand(000000, 999999);
}

$people = array(
    array('name' => 'Kalle', 'salt' => 856412),
    array('name' => 'Pierre', 'salt' => 215863)
);

for($i = 0, $size = count($people); $i < $size; ++$i) {
    $people[$i]['salt'] = mt_rand(000000, 999999);
}


// foreach
$arr = array(1, 2, 3, 4);
foreach ($arr as &$value) {
    $value = $value * 2;
}
// $arr is now array(2, 4, 6, 8)
unset($value); // break the reference with the last element

$arr = array("one", "two", "three");
reset($arr);
while (list(, $value) = each($arr)) {
    "Value: $value<br />\n";
}

foreach ($arr as $value) {
    "Value: $value<br />\n";
}

$arr = array("one", "two", "three");
reset($arr);
while (list($key, $value) = each($arr)) {
    "Key: $key; Value: $value<br />\n";
}

foreach ($arr as $key => $value) {
    "Key: $key; Value: $value<br />\n";
}

/* foreach example 1: value only */

$a = array(1, 2, 3, 17);

foreach ($a as $v) {
    "Current value of \$a: $v.\n";
}

/* foreach example 2: value (with its manual access notation printed for illustration) */

$a = array(1, 2, 3, 17);

$i = 0; /* for illustrative purposes only */

foreach ($a as $v) {
    "\$a[$i] => $v.\n";
    $i++;
}

/* foreach example 3: key and value */

$a = array(
    "one" => 1,
    "two" => 2,
    "three" => 3,
    "seventeen" => 17
);

foreach ($a as $k => $v) {
    "\$a[$k] => $v.\n";
}

/* foreach example 4: multi-dimensional arrays */
$a = array();
$a[0][0] = "a";
$a[0][1] = "b";
$a[1][0] = "y";
$a[1][1] = "z";

foreach ($a as $v1) {
    foreach ($v1 as $v2) {
        "$v2\n";
    }
}

/* foreach example 5: dynamic arrays */

foreach (array(1, 2, 3, 4, 5) as $v) {
    "$v\n";
}


// break
$arr = array('one', 'two', 'three', 'four', 'stop', 'five');
while (list(, $val) = each($arr)) {
    if ($val == 'stop') {
        break;    /* You could also write 'break 1;' here. */
    }
    "$val<br />\n";
}

/* Using the optional argument. */

$i = 0;
while (++$i) {
    switch ($i) {
        case 5:
            "At 5<br />\n";
            break 1;  /* Exit only the switch. */
        case 10:
            "At 10; quitting<br />\n";
            break 2;  /* Exit the switch and the while. */
        default:
            break;
    }
}


// continue
function do_something_odd() {}
while (list($key, $value) = each($arr)) {
    if (!($key % 2)) { // skip odd members
        continue;
    }
    do_something_odd($value);
}

$i = 0;
while ($i++ < 5) {
    "Outer<br />\n";
    while (1) {
        "Middle<br />\n";
        while (1) {
            "Inner<br />\n";
            continue 3;
        }
        "This never gets output.<br />\n";
    }
    "Neither does this.<br />\n";
}


// switch
if ($i == 0) {
    "i equals 0";
} elseif ($i == 1) {
    "i equals 1";
} elseif ($i == 2) {
    "i equals 2";
}

switch ($i) {
    case 0:
        "i equals 0";
        break;
    case 1:
        "i equals 1";
        break;
    case 2:
        "i equals 2";
        break;
}

switch ($i) {
    case "apple":
        "i is apple";
        break;
    case "bar":
        "i is bar";
        break;
    case "cake":
        "i is cake";
        break;
}

switch ($i) {
    case 0:
        "i equals 0";
    case 1:
        "i equals 1";
    case 2:
        "i equals 2";
}

switch ($i) {
    case 0:
    case 1:
    case 2:
        "i is less than 3 but not negative";
        break;
    case 3:
        "i is 3";
}

switch ($i) {
    case 0:
        "i equals 0";
        break;
    case 1:
        "i equals 1";
        break;
    case 2:
        "i equals 2";
        break;
    default:
        "i is not equal to 0, 1 or 2";
}

switch ($i):
    case 0:
        "i equals 0";
        break;
    case 1:
        "i equals 1";
        break;
    case 2:
        "i equals 2";
        break;
    default:
        "i is not equal to 0, 1 or 2";
endswitch;

$beer = '';
switch($beer)
{
    case 'tuborg';
    case 'carlsberg';
    case 'heineken';
        'Good choice';
        break;
    default;
        'Please make a new selection...';
    break;
}


// declare
// This is valid:
declare(ticks=1);

// these are the same:

// you can use this:
declare(ticks=1) {
    // entire script here
}

// or you can use this:
declare(ticks=1);
// entire script here

declare(ticks=1);

// A function called on each tick event
function tick_handler()
{
    "tick_handler() called\n";
}

register_tick_function('tick_handler');

$a = 1;

if ($a > 0) {
    $a += 2;
    $a;
}

$a = 1;
tick_handler();

if ($a > 0) {
    $a += 2;
    tick_handler();
    $a;
    tick_handler();
}
tick_handler();


// User-defined functions
function foo2($arg_1, $arg_2, /* ..., */ $arg_n)
{
    $retval = "Example function.\n";
    return $retval;
}

$makefoo = true;

/* We can't call foo() from here
   since it doesn't exist yet,
   but we can call bar() */

bar();

if ($makefoo) {
    function foo3()
    {
        "I don't exist until program execution reaches me.\n";
    }
}

/* Now we can safely call foo3()
   since $makefoo evaluated to true */

if ($makefoo) foo3();

function bar()
{
    "I exist immediately upon program start.\n";
}

function foo4()
{
    function bar2()
    {
        "I don't exist until foo() is called.\n";
    }
}

/* We can't call bar2() yet
   since it doesn't exist. */

foo4();

/* Now we can call bar2(),
   foo()'s processing has
   made it accessible. */

bar2();
function recursion($a)
{
    if ($a < 20) {
        "$a\n";
        recursion($a + 1);
    }
}


// Function arguments
function takes_array($input)
{
    "$input[0] + $input[1] = ". $input[0]+$input[1];
}

function add_some_extra(&$string)
{
    $string .= 'and something extra.';
}
$str = 'This is a string, ';
add_some_extra($str);
$str;    // outputs 'This is a string, and something extra.'

function makecoffee($type = "cappuccino")
{
    return "Making a cup of $type.\n";
}
makecoffee();
makecoffee(null);
makecoffee("espresso");

function makecoffee2($types = array("cappuccino"), $coffeeMaker = NULL)
{
    $device = is_null($coffeeMaker) ? "hands" : $coffeeMaker;
    return "Making a cup of ".join(", ", $types)." with $device.\n";
}
makecoffee2();
makecoffee2(array("cappuccino", "lavazza"), "teapot");

function makeyogurt($flavour, $type = "acidophilus")
{
    return "Making a bowl of $type $flavour.\n";
}

makeyogurt("raspberry");   // works as expected

function sum() {
    $acc = 0;
    foreach (func_get_args() as $n) {
        $acc += $n;
    }
    return $acc;
}

sum(1, 2, 3, 4);


// Returning values
function square($num)
{
    return $num * $num;
}
square(4);   // outputs '16'.

function small_numbers()
{
    return array (0, 1, 2);
}
list ($zero, $one, $two) = small_numbers();

function &returns_reference()
{
    $someref = 'foo';

    return $someref;
}

$newref =& returns_reference();


// Variable functions
function foo5() {
    "In foo5()<br />\n";
}

function bar3($arg = '')
{
    "In bar3(); argument was '$arg'.<br />\n";
}

// This is a wrapper function around echo
function echoit($string)
{
    $string;
}

$func = 'foo';
$func();        // This calls foo()

$func = 'bar';
$func('test');  // This calls bar()

$func = 'echoit';
$func('test');  // This calls echoit()

class Foo2
{
    function Variable()
    {
        $name = 'Bar';
        $this->$name(); // This calls the Bar() method
    }

    function Bar()
    {
        "This is Bar";
    }
}

$foo = new Foo2();
$funcname = "Variable";
$foo->$funcname();  // This calls $foo->Variable()

class Foo3
{
    static $variable = 'static property';
    static function Variable()
    {
        'Method Variable called';
    }
}

Foo3::$variable; // This prints 'static property'. It does need a $variable in this scope.
$variable = "Variable";
Foo3::$variable();  // This calls $foo->Variable() reading $variable in this scope.


// Anonymous functions
preg_replace_callback('~-([a-z])~', function ($match) {
    return strtoupper($match[1]);
}, 'hello-world');
// outputs helloWorld

$greet = function($name)
{
    sprintf("Hello %s\r\n", $name);
};

$greet('World');
$greet('PHP');

// A basic shopping cart which contains a list of added products
// and the quantity of each product. Includes a method which
// calculates the total price of the items in the cart using a
// closure as a callback.
class Cart
{
    const PRICE_BUTTER  = 1.00;
    const PRICE_MILK    = 3.00;
    const PRICE_EGGS    = 6.95;

    protected $products = array();

    public function add($product, $quantity)
    {
        $this->products[$product] = $quantity;
    }

    public function getQuantity($product)
    {
        return isset($this->products[$product]) ? $this->products[$product] :
        FALSE;
    }

    public function getTotal($tax)
    {
        $total = 0.00;

        $callback =
        function ($quantity, $product) use ($tax, &$total)
        {
            $pricePerItem = constant(__CLASS__ . "::PRICE_" .
                strtoupper($product));
            $total += ($pricePerItem * $quantity) * ($tax + 1.0);
        };

        array_walk($this->products, $callback);
        return round($total, 2);
    }
}

$my_cart = new Cart;

// Add some items to the cart
$my_cart->add('butter', 1);
$my_cart->add('milk', 3);
$my_cart->add('eggs', 6);

// Print the total with a 5% sales tax.
$my_cart->getTotal(0.05) . "\n";
// The result is 54.29
