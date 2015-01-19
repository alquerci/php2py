# -*- coding: ISO-8859-1 -*-
# code here

# This is a one-line c++ style comment

"""This is a multi line comment
   yet another line of comment"""

"""This is a pretty multi line comment
yet another pretty line of comment
 * with a list
"""

import os;

def foo():
    """This is an indented pretty multi line comment
    yet another pretty line of comment
     * with a list
    """

a_bool = True;   # a boolean
a_bool = False;  # a boolean
a_str  = "foo";  # a string
a_str2 = 'foo';  # a string
an_int = 12;     # an integer

foo = True; # assign the value TRUE to $foo

bool("");            # bool(false)
bool(1);          # bool(true)
bool(-2);            # bool(true)
bool("foo");         # bool(true)
bool(2.3e5);         # bool(true)
bool(array([(0, 12)]));     # bool(true)
bool(array());       # bool(false)
bool("false");    # bool(true)

a = 1234; # decimal number
a = -123; # a negative number
a = 0123; # octal number (equivalent to 83 decimal)
a = 0x1A; # hexadecimal number (equivalent to 26 decimal)
a = 0b11111111; # binary number (equivalent to 255 decimal)

int((25/7)); # int(3)
int(( (0.1+0.7) * 10 )); # 7

a = 1.234;
b = 1.2e3;
c = 7E-10;

# Simple quote
'this is a simple string';

'''You can also have embedded newlines in
strings this way as it is
okay to do''';

# Outputs: Arnold once said: "I'll be back"
'Arnold once said: "I\'ll be back"';

# Outputs: You deleted C:\*.*?
'You deleted C:\\*.*?';

# Outputs: You deleted C:\*.*?
'You deleted C:\*.*?';

# Outputs: This will not expand: \n a newline
'This will not expand: \\n a newline';

# Outputs: Variables do not $expand $either
'Variables do not $expand $either';

# Escaped characters
'\\n';
'\\r';
'\\t';
'\\v';
'\e';
'\\foo\\';
'\x\\';
'\\f';
'\\0';
'\\012';
'\\x0';
'\\x01';
'\\a';
'\\b';

# Double quote
"this is a simple string";

"""You can also have embedded newlines in
strings this way as it is
okay to do""";

# Outputs: Arnold once said: "I'll be back"
"Arnold once said: \"I'll be back\"";

# Outputs: You deleted C:\*.*?
"You deleted C:\\*.*?";

# Outputs: You deleted C:\*.*?
"You deleted C:\*.*?";

# Outputs: This will not expand: \n a newline
"This will not expand: \\n a newline";

# Outputs: Variables do not $expand $either
"Variables do not $expand $either";

# Escaped characters
"\n";
"\r";
"\t";
"\v";
"\x1B";
"\\foo\\";
"\x\\";
"\f";
"\0";
"\012";
"\x0";
"\x01";
"\\a";
"\\b";
"$";

# Heredoc
foo = """\
Example of string
spanning multiple lines
using heredoc syntax.\
""";

foo = """\
This should print a capital 'A': \x41\
""";

array([(0, """\
foobar!\
"""
)]);

foo = """\
Hello World!\
""";

# nowdoc
foo = """\
Example of string
spanning multiple lines
using nowdoc syntax.\
""";

foo = """\
My name is "$name". I am printing some $foo->foo.
Now, I am printing some {$foo->bar[1]}.
This should not print a capital 'A': \\x41\
""";

# Variable parsing
juice = "apple";
"He drank some "+str(juice)+" juice.";
"He drank some "+str(juice)+" juice. { foo bar }";
juices = array([(0, "apple"), (1, "orange"), ("koolaid1", "purple")]);
"He drank some "+str(juices[0])+" juice.";
"He drank some "+str(juices[1])+" juice.";
"He drank some "+str(juices["koolaid1"])+" juice.";

class Foo(ArrayAccess):
    def __init__(self):
        self.values = None;
        self.name = 'yes';
        self.bar = 'obj';
        self.foo = None;

        self.values = Bar();
        self.foo = Bar();

    def getName(self):
        return 'obj';

    def do_foo(self):
        pass

    def __str__(self):
        return 'obj';

    def offsetGet(self, offset):
        php_break = False;
        if 0 == offset or php_break :
            php_break = True;
        if 3 == offset or php_break :
            return self;
        if 4 == offset or php_break :
            php_break = True;
        if 'foo' == offset or php_break :
            return array([(0, 0), (1, 1), (2, 2), (3, self)]);

        return 'yes';

    def offsetExists(self, offset): pass
    def offsetSet(self, offset, value): pass
    def offsetUnset(self, offset): pass

class Bar(Foo):
    def __init__(self):
        pass

class beers:
    softdrink = 'obj';

    ale = 'obj';

class people():
    john = "John Smith";
    jane = "Jane Smith";
    robert = "Robert Paulsen";

    smith = "Smith";

people = people();

""+str(people.john)+" drank some "+str(juices[0])+" juice.";
""+str(people.john)+" then said hello to "+str(people.jane)+".";
""+str(people.john)+"'s wife greeted "+str(people.robert)+".";

foo = Foo();
"Now, I am printing some "+str(foo.bar)+"[1].";

"""\
He drank some """+str(juice)+""" juice.
He drank some """+str(juice)+""" juice. { foo bar }
He drank some """+str(juices[0])+""" juice.
He drank some """+str(juices[1])+""" juice.
He drank some """+str(juices["koolaid1"])+""" juice.
"""+str(people.john)+""" drank some """+str(juices[0])+""" juice.
"""+str(people.john)+""" then said hello to """+str(people.jane)+""".
"""+str(people.john)+"""'s wife greeted """+str(people.robert)+""".
Now, I am printing some """+str(foo.bar)+"""[1].\
""";

# Complex (curly) syntax
foo = """\
Now, I am printing some """+str(foo.bar[1])+""".\
""";

great = 'fantastic';

# Won't work, outputs: This is { fantastic}
"This is { "+str(great)+"}";

# Works, outputs: This is fantastic
"This is "+str(great)+"";
"This is "+str(vars()["great"])+"";

# Works, outputs: This is fantastic->foo
"This is "+str(vars()["great"])+"->foo";

class square():
    width = 42;

square = square();

# Works
"This square is "+str(square.width)+"00 centimeters broad.";

arr = Foo();

# Works, quoted keys only work using the curly brace syntax
"This works: "+str(arr['key'])+"";
"This works: "+str(arr.foo['key'])+"";

# Works
"This works: "+str(arr[4][3])+"";

# This is wrong for the same reason as $foo[bar] is wrong  outside a string.
# In other words, it will still work, but only because PHP first looks for a
# constant named foo; an error of level E_NOTICE (undefined constant) will be
# thrown.
"This is wrong: "+str(arr[foo][3])+"";

# Works. When using multi-dimensional arrays, always use braces around arrays
# when inside of strings
"This works: "+str(arr['foo'][3])+"";

obj = Foo();

"This works too: "+str(obj.values.name)+"";
"This works too: "+str(obj.values[3].name)+"";

name = 'obj';

def getName():
    return 'obj';

foo = Foo();
bar = 'name';
baz = array([(0, 0), (1, 'values')]);

# not supported yet
"This is the value of the var named "+str(name)+": {${$name}}";
"This is the value of the var named by the return value of getName(): {${getName()}}";
"This is the value of the var named by the return value of $object->getName(): {${$object.getName()}}";
"I'd like an {${beers.softdrink}}\n";
"I'd like an {${beers.$ale}}\n";
"{$foo.$bar}\n";
"{$foo.$baz[1]}\n";

# Won't work, outputs: This is the return value of getName(): {getName()}
"This is the return value of getName(): {getName()}";

beers = 'beers';

"I'd like an "+str(beers.ale)+"\n";

# Get the first character of a string
foo = 'This is a test.';
first = foo[0];

# Concatenation
"This works: " + arr['foo'][3];

# String casting
str("");            # string("")
str(1);          # string("1")
str(-2);            # string("-2")
str("foo");         # string("foo")
str(2.3e5);         # string("2.3e5")
str(array([(0, 12)]));     # string("12")
str(array());       # string("")
str("false");    # string("false")

# Object Initialization
bar = foo();
bar.do_foo();

# NULL
var = None;
var = None;
var = None;
var = None;
var = None;
var = None;

None;

# Variable variables
a = Foo();

""+str(a)+" "+str(vars()[a])+"";
""+str(a)+" "+str(vars()[a.foo])+"";
""+str(a)+" "+str(vars()[a.foo[0]])+"";
""+str(a)+" "+str(vars()[a.foo[0].bar])+"";

# not supported yet
# $$a = 'world';

# MagicConstants
os.path.realpath(__file__);
os.path.dirname(os.path.realpath(__file__));
__name__;

# Expressions
second = 's';
third = 't';
(second if first else third);
(second if first else third);
(first if first else third);

def double(i):
    return i*2;
b = a = 5;        """assign the value five into the variable $a and $b"""
c = a; a += 1;          """post-increment, assign original value of $a
                       (5) to $c"""
e = d = b + 1; b += 1;     """pre-increment, assign the incremented value of
                       $b (6) to $d and $e"""

"""at this point, both $d and $e are equal to 6"""

f = double(d); d += 1;  """assign twice the value of $d before
                       the increment, 2*6 = 12 to $f"""
g = double(e + 1); e += 1;  """assign twice the value of $e after
                       the increment, 2*7 = 14 to $g"""
h = g = g + 10;      """first, $g is incremented by 10 and ends with the
                       value of 24. the value of the assignment (24) is
                       then assigned into $h, and $h ends with the value
                       of 24 as well."""

# Operator Precedence
a = 3 * 3 % 5; # (3 * 3) % 5 = 4
# ternary operator associativity differs from C/C++
a = (1 if (0 if True else True) else 2); # (true ? 0 : true) ? 1 : 2 = 2

a = 1;
b = 2;
a = b = b + 3; # $a = ($b += 3) -> $a = 5, $b = 5

# not supported
# $a = ($b = 4) + 5;

a = 3;
a += 5; # sets $a to 8, as if we had said: $a = $a + 5;
b = "Hello ";
b += "There!"; # sets $b to "Hello There!", just like $b = $b . "There!";

a is b;
a is not b;

a; a -= 1;
a - 1; a -= 1;

a = (not foo());
a = (False and foo());
b = (True  or foo());
c = (False and foo());
d = (True  or  foo());
isinstance(a, MyClass);


# if
if (a > b):
    "a is bigger than b";

if (a > b):
    "a is bigger than b";
    b = a;


# else
if (a > b):
    "a is greater than b";
else:
    "a is NOT greater than b";

# elseif/else if
if (a > b):
    "a is bigger than b";
elif (a == b):
    "a is equal to b";
else:
    "a is smaller than b";

if(a > b):
    a+" is greater than "+b;
elif(a == b): # Note the combination of the words.
    a+" equals "+b;
else:
    a+" is neither greater than or equal to "+b;

# Alternative syntax for control structures
if (a == 5):
    "a equals 5";
    "...";
elif (a == 6):
    "a equals 6";
    "!!!";
else:
    "a is neither 5 nor 6";


# while
""" example 1 """

i = 1;
while (i <= 10):
    i += 1;  """ the printed value would be
                   $i before the increment
                   (post-increment) """

""" example 2 """

i = 1;
while (i <= 10):
    i;
    i += 1;


# do-while
i = 0;
while True:
    i;
    if not i > 0: break;

factor = 2;
minimum_limit = 42;
while True:
    if (i < 5) :
        "i is not big enough";
        break;
    i *= factor;
    if (i < minimum_limit) :
        break;
    "i is ok";

    """ process i """

    if not 0: break;


# for
""" example 1 """

i = 1;
while i <= 10 :
    i;
    i += 1;

""" example 2 """

i = 1;
while True :
    if (i > 10) :
        break;
    i;
    i += 1;

""" example 3 """

i = 1;
while True :
    if (i > 10) :
        break;
    i;
    i += 1;

""" example 4 """

i = 1; j = 0;
while i <= 10 :
    j += i; i; i += 1;

"""This is an array with some data we want to modify
when running through the for loop.
"""
people = array([
    (0, array([('name', 'Kalle'), ('salt', 856412)])),
    (1, array([('name', 'Pierre'), ('salt', 215863)]))
]);

i = 0;
while(i < count(people)) :
    people[i]['salt'] = mt_rand(000000, 999999);
    i + 1;

people = array([
    (0, array([('name', 'Kalle'), ('salt', 856412)])),
    (1, array([('name', 'Pierre'), ('salt', 215863)]))
]);

i = 0; size = count(people);
while(i < size) :
    people[i]['salt'] = mt_rand(000000, 999999);
    i + 1;


# foreach
arr = array([(0, 1), (1, 2), (2, 3), (3, 4)]);
for php_key, value in arr.items() :
    arr[php_key] = arr[php_key] * 2
# $arr is now array(2, 4, 6, 8)
del(value); # break the reference with the last element

arr = array([(0, "one"), (1, "two"), (2, "three")]);
for value in arr :
    "Value: "+str(value)+"<br />\n";

for value in arr :
    "Value: "+str(value)+"<br />\n";

arr = array([(0, "one"), (1, "two"), (2, "three")]);
for key, value in arr.items() :
    "Key: "+str(key)+"; Value: "+str(value)+"<br />\n";

for key, value in arr.items() :
    "Key: "+str(key)+"; Value: "+str(value)+"<br />\n";

""" foreach example 1: value only """

a = array([(0, 1), (1, 2), (2, 3), (3, 17)]);

for v in a :
    "Current value of \$a: "+str(v)+".\n";

""" foreach example 2: value (with its manual access notation printed for illustration) """

a = array([(0, 1), (1, 2), (2, 3), (3, 17)]);

i = 0; """ for illustrative purposes only """

for v in a :
    "\$a[$i] => "+str(v)+".\n";
    i += 1;

""" foreach example 3: key and value """

a = array([
    ("one", 1),
    ("two", 2),
    ("three", 3),
    ("seventeen", 17)
]);

for k, v in a.items() :
    "\$a[$k] => "+str(v)+".\n";

""" foreach example 4: multi-dimensional arrays """
a = array();
a[0][0] = "a";
a[0][1] = "b";
a[1][0] = "y";
a[1][1] = "z";

for v1 in a :
    for v2 in v1 :
        ""+str(v2)+"\n";

""" foreach example 5: dynamic arrays """

for v in array([(0, 1), (1, 2), (2, 3), (3, 4), (4, 5)]) :
    ""+str(v)+"\n";


# break
arr = array([(0, 'one'), (1, 'two'), (2, 'three'), (3, 'four'), (4, 'stop'), (5, 'five')]);
for val in arr :
    if (val == 'stop') :
        break;    """ You could also write 'break 1;' here. """
    ""+str(val)+"<br />\n";

""" Using the optional argument. """

i = 0;
while (i + 1) :
    i += 1;
    php_break = False;
    if 5 == i or php_break :
        "At 5<br />\n";
        php_break = False;  """ Exit only the switch. """
    if 10 == i or php_break :
        "At 10; quitting<br />\n";
        break;  """ Exit the switch and the while. """


# continue
def do_something_odd(): pass
for key, value in arr.items() :
    if (not (key % 2)) : # skip odd members
        continue;
    do_something_odd(value);

i = 0;
while (i < 5) :
    i += 1;
    "Outer<br />\n";
    while (1) :
        "Middle<br />\n";
        while (1) :
            "Inner<br />\n";
            continue;
        continue;
        "This never gets output.<br />\n";
    continue;
    "Neither does this.<br />\n";


# switch
if (i == 0) :
    "i equals 0";
elif (i == 1) :
    "i equals 1";
elif (i == 2) :
    "i equals 2";

php_break = False;
if 0 == i or php_break :
    "i equals 0";
    php_break = False;
if 1 == i or php_break :
    "i equals 1";
    php_break = False;
if 2 == i or php_break :
    "i equals 2";
    php_break = False;

php_break = False;
if "apple" == i or php_break :
    "i is apple";
    php_break = False;
if "bar" == i or php_break :
    "i is bar";
    php_break = False;
if "cake" == i or php_break :
    "i is cake";
    php_break = False;

php_break = False;
if 0 == i or php_break :
    "i equals 0";
    php_break = True;
if 1 == i or php_break :
    "i equals 1";
    php_break = True;
if 2 == i or php_break :
    "i equals 2";
    php_break = True;

php_break = False;
if 0 == i or php_break :
    php_break = True;
if 1 == i or php_break :
    php_break = True;
if 2 == i or php_break :
    "i is less than 3 but not negative";
    php_break = False;
if 3 == i or php_break :
    "i is 3";
    php_break = True;

php_break = False;
if 0 == i or php_break :
    "i equals 0";
    php_break = False;
if 1 == i or php_break :
    "i equals 1";
    php_break = False;
if 2 == i or php_break :
    "i equals 2";
    php_break = False;
if i not in [0, 1, 2] or php_break :
    "i is not equal to 0, 1 or 2";

php_break = False;
if 0 == i or php_break :
    "i equals 0";
    php_break = False;
if 1 == i or php_break :
    "i equals 1";
    php_break = False;
if 2 == i or php_break :
    "i equals 2";
    php_break = False;
if i not in [0, 1, 2] or php_break :
    "i is not equal to 0, 1 or 2";

beer = '';
php_break = False;
if 'tuborg' == beer or php_break :
    php_break = True;
if 'carlsberg' == beer or php_break :
    php_break = True;
if 'heineken' == beer or php_break :
    'Good choice';
    php_break = False;
if beer not in ['tuborg', 'carlsberg', 'heineken'] or php_break :
    'Please make a new selection...';


# declare
# This is valid:

# these are the same:

# you can use this:
# entire script here

# or you can use this:
# entire script here


# A function called on each tick event
def tick_handler():
    "tick_handler() called\n";

register_tick_function('tick_handler');

a = 1;

if (a > 0) :
    a += 2;
    a;

a = 1;
tick_handler();

if (a > 0) :
    a += 2;
    tick_handler();
    a;
    tick_handler();
tick_handler();


# User-defined functions
def foo2(arg_1, arg_2, arg_n):
    retval = "Example function.\n";
    return retval;

makefoo = True;

"""We can't call foo() from here
since it doesn't exist yet,
but we can call bar()"""

bar();

if (makefoo) :
    def foo3():
        "I don't exist until program execution reaches me.\n";

"""Now we can safely call foo3()
since $makefoo evaluated to true"""

if (makefoo): foo3();

def bar():
    "I exist immediately upon program start.\n";

def foo4():
    global bar2;
    def bar2():
        "I don't exist until foo() is called.\n";

"""We can't call bar() yet
since it doesn't exist."""

foo4();

"""Now we can call bar(),
foo()'s processing has
made it accessible."""

bar2();
def recursion(a):
    if (a < 20) :
        "$a\n";
        recursion(a + 1);


# Function arguments
def takes_array(input):
    "$input[0] + $input[1] = "+ input[0]+input[1];

def add_some_extra(string):
    string += 'and something extra.';
str = 'This is a string, ';
add_some_extra(str);
str;    # outputs 'This is a string, and something extra.'

def makecoffee(type = "cappuccino"):
    return "Making a cup of $type.\n";
makecoffee();
makecoffee(None);
makecoffee("espresso");

def makecoffee2(types = array([(0, "cappuccino")]), coffeeMaker = None):
    device = "hands" if is_null(coffeeMaker) else coffeeMaker;
    return "Making a cup of "+join(", ", types)+" with "+str(device)+".\n";
makecoffee2();
makecoffee2(array([(0, "cappuccino"), (1, "lavazza")]), "teapot");

def makeyogurt(flavour, type = "acidophilus"):
    return "Making a bowl of "+str(type)+" "+str(flavour)+".\n";

makeyogurt("raspberry");   # works as expected

def sum(*php_args):
    acc = 0;
    for n in php_args:
        acc += n;
    return acc;

sum(1, 2, 3, 4);


# Returning values
def square(num):
    return num * num;
square(4);   # outputs '16'.

def small_numbers():
    return array ([(0, 0), (1, 1), (2, 2)]);
(zero, one, two) = small_numbers();

def returns_reference():
    someref = 'foo';

    return someref;

newref = returns_reference();


# Variable functions
def foo5():
    "In foo5()<br />\n";

def bar3(arg = ''):
    "In bar3(); argument was '"+str(arg)+"'.<br />\n";

# This is a wrapper function around echo
def echoit(string):
    string;

func = 'foo';
vars()[func]();        # This calls foo()

func = 'bar';
vars()[func]('test');  # This calls bar()

func = 'echoit';
vars()[func]('test');  # This calls echoit()

class Foo2:
    def Variable(self):
        name = 'Bar';
        getattr(self, name)(); # This calls the Bar() method

    def Bar(self):
        "This is Bar";

foo = Foo2();
funcname = "Variable";
getattr(foo, funcname)();  # This calls $foo->Variable()

class Foo3:
    variable = 'static property';
    @classmethod
    def Variable():
        'Method Variable called';

Foo3.variable; # This prints 'static property'. It does need a $variable in this scope.
variable = "Variable";
getattr(Foo3, variable)();  # This calls $foo->Variable() reading $variable in this scope.


# Anonymous functions
def php_closure(match):
    return strtoupper(match[1]);
preg_replace_callback('~-([a-z])~', php_closure, 'hello-world');
# outputs helloWorld

def greet(name):
    sprintf("Hello %s\r\n", name);

greet('World');
greet('PHP');

# A basic shopping cart which contains a list of added products
# and the quantity of each product. Includes a method which
# calculates the total price of the items in the cart using a
# closure as a callback.
class Cart:
    PRICE_BUTTER  = 1.00;
    PRICE_MILK    = 3.00;
    PRICE_EGGS    = 6.95;

    def __init__(self):
        self._products = array();

    def add(self, product, quantity):
        self._products[product] = quantity;

    def getQuantity(self, product):
        return (self._products[product] if isset(self._products[product]) else
        False);

    def getTotal(self, tax):
        total = 0.00;

        def callback(quantity, product):
            pricePerItem = getattr(self.__class__, "PRICE_" +
                strtoupper(product));
            total += (pricePerItem * quantity) * (tax + 1.0);

        array_walk(self._products, callback);
        return round(total, 2);

my_cart = Cart();

# Add some items to the cart
my_cart.add('butter', 1);
my_cart.add('milk', 3);
my_cart.add('eggs', 6);

# Print the total with a 5% sales tax.
my_cart.getTotal(0.05) + "\n";
# The result is 54.29
