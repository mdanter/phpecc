## Pure PHP Elliptic Curve DSA and DH

[![Build Status](https://magnum.travis-ci.com/aztech-digital/php-coins.svg?token=2PviB9phybzQwMnuzpZy&branch=master)](https://magnum.travis-ci.com/aztech-digital/php-coins)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/aztech-digital/php-ecc/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/aztech-digital/php-ecc/?branch=master)

### Foreword

This library is a rewrite/update of Matys Danter's ECC library. All credit goes to him.

### Information

For more information on Elliptic Curve Cryptography please read http://matejdanter.com/2010/12/elliptic-curve-php-oop-dsa-and-diffie-hellman/

### License

This package is released under the MIT license.

### Requirements

* PHP 5.3+
* composer
* ext-bcmath or ext-gmp. GMP math is highly recommended due to performance considerations (10x faster than BCMath)

### Installation

You can install this library via Composer :

`composer require mdanter/ecc:0.1`


