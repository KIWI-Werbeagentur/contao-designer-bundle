<?php

namespace Kiwi\Contao\DesignerBundle\Models;

use Contao\Model;

/**
 * @property string $title
 * @property string $variable
 * @property string $value
 */
class ColorModel extends Model
{
    protected static $strTable = "tl_color";
}
