# RTphp
![Version](https://img.shields.io/badge/version-0.1-blue.svg)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](https://rogertiongdev.github.io/MIT-License/)

**RTphp** is a small PHP library consists multiple PHP classes.

Directory structure
-

```
core/src             php classes source code
core/test            tested / example / demo code
core/load.php        php function to require all php classes
docs/                documentation generated by ApiGen
```

Installation
-
Installation for single class example

```
<?php
    require_once './RTphp/src/RTmysqli.php';
    $obj = new RTdev\RTphp\RTmysqli();
```

Installation for all class example

```
<?php
    require_once './RTphp/load.php';
    use RTdev\RTphp as RTphp;
    
    RTphp\RTphpLoad();
    
    $RTdb = new RTphp\RTmysqli();
    $RTpw = new RTphp\RTpassword();
    $RTsf = new RTphp\RTslugify();
    $RTutil = new RTphp\RTutil();
```

Classes list
-
- RTmysqli
- RTpassword
- RTslugify
- RTutil

Read
-
- [RTphp documentation](https://rawgit.com/rogertiongdev/RTphp/master/docs/index.html)
- [RTphp example code](https://github.com/rogertiongdev/RTphp/tree/master/core/test)

<a><img src="https://scontent-kul1-1.xx.fbcdn.net/v/t1.0-9/15726590_139774699850047_6018651687098820130_n.jpg?oh=ecbb2a94b3e61e32d7cb99ff61762e3a&oe=58E3F2B2" width="99" align="right"/></a>
