<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 06/12/14
 * Time: 15:42
 */

namespace ISTI\Image\Tests\Resizer;

use ISTI\Image\Resizer\GregwarResizer;

class  GregwarResizerTest extends BaseResizerTest {
    public function testResize()
    {
        $resizer = new GregwarResizer();
        $this->forAllTypes($resizer);

    }
} 