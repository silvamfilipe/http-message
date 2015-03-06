<?php

/**
 * This file is part of HttpMessage package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage\Tests;

use Fsilva\HttpMessage\Validator;

/**
 * Class Validator Test case
 *
 * @package Fsilva\HttpMessage\Tests
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class ValidatorTest extends \PHPUnit_Framework_TestCase
{

    public function testNonexistentValidator()
    {
        $this->setExpectedException(
            "Fsilva\\HttpMessage\\Exception\\InvalidArgumentException"
        );
        Validator::isValid('foo', 'bar');
    }

    public function testNotAnValidator()
    {
        $this->setExpectedException(
            "Fsilva\\HttpMessage\\Exception\\InvalidArgumentException"
        );
        Validator::isValid('\\StdClass', 'bar');
    }
}
