<?php

namespace Kiwi\Contao\DesignerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class KiwiDesignerBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
