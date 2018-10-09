<?php
/**
 * This file is part of the JuvImg package.
 *
 * (c) Erik Theoboldt <erik@theoboldt.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class DefaultController
{

    /**
     * Test service availability
     *
     * @return Response
     */
    public function index()
    {
        return new Response('OK');
    }

}