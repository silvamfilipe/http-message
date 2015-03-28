<?php

/**
 * This file is part of HttpMessage package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage\Tests\Validator;

use Fsilva\HttpMessage\Validator;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * KeyValueArrayTest test case
 *
 * @package Fsilva\HttpMessage\Tests\Validator
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class KeyValueArrayTest extends TestCase 
{

    public function testArrayValueValidator()
    {
        $this->assertFalse(Validator::isValid('keyValueArray', 'test'));
    }
}
