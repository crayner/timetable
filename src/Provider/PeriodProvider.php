<?php
/**
 * Created by PhpStorm.
 *
 * timetable
 * (c) 2021 Craig Rayner <craig@craigrayner.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * User: craig
 * Date: 8/01/2021
 * Time: 09:53
 */
namespace App\Provider;

use App\Items\Period;

/**
 * Class PeriodProvider
 *
 * @package App\Provider
 * @author Craig Rayner <craig@craigrayner.com>
 * 8/01/2021 09:54
 */
class PeriodProvider extends AbstractProvider
{
    /**
     * @var string
     */
    protected string $method = 'getPeriods';

    /**
     * @var string
     */
    protected string $itemName = Period::class;
}
