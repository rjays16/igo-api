<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class SystemSetting extends Enum
{

    const AllowedPageMinValue = 10;     //10 These sets the global minimum limit for all page size.
    const AllowedPageMaxValue = 100000; //100K These sets the global maximum limit for all page size.

}
