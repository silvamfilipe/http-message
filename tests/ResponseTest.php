<?php

/**
 * This file is part of fsilva/http-message package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage\Tests;

use Fsilva\HttpMessage\Response;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Response test case
 *
 * @package Fsilva\HttpMessage\Tests
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class ResponseTest extends TestCase
{

    /**
     * Invalid status code arguments
     * @return array
     */
    public function invalidStatuses()
    {
        return [
            'array' => [[]],
            'string' => ['100', 'Continue'],
            'object' => [new \stdClass()],
            'float' => [200.2, 'Ok'],
            'invalid' => [295, 'Super Ok']
        ];
    }

    /**
     * Valid status code arguments
     * @return array
     */
    public function validStatuses()
    {
        return [
            'normal' => [203, 'Non-Authoritative Information test'],
            'auto' => [404]
        ];
    }

    /**
     * @dataProvider invalidStatuses
     * @param mixed $code
     * @param mixed $phrase
     */
    public function testCreationWithInvalidCode($code, $phrase = null)
    {
        $response = new Response();
        $this->setExpectedException(
            "Fsilva\\HttpMessage\\Exception\\InvalidArgumentException"
        );
        $response->withStatus($code, $phrase);
    }

    /**
     * @dataProvider validStatuses
     * @param $code
     * @param null $phrase
     */
    public function testCreationWithValidCode($code, $phrase = null)
    {
        $response = new Response();
        $new = $response->withStatus($code, $phrase);
        $this->assertInstanceOf("Fsilva\\HttpMessage\\Response", $new);
        $this->assertNotSame($response, $new);
        $this->assertEquals($new->getStatusCode(), $code);
        if (is_null($phrase)) {
            $this->assertEquals(
                Response::getRecommendedReasonPhrases()[$code],
                $new->getReasonPhrase()
            );
        } else {
            $this->assertEquals($new->getReasonPhrase(), $phrase);
        }
    }
}
