<?php

namespace Kiwi\Contao\DesignerBundle\Models;

use Contao\Model;
use Contao\StringUtil;

/**
 * @property string $title
 * @property string $variable
 * @property string $value
 */
class ColorSchemeModel extends Model
{
    protected static $strTable = "tl_color_scheme";
}
