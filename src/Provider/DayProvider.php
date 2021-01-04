<?php
/**
 * Created by PhpStorm.
 *
 * Timetable Creator
 * (c) 2020-2021 Craig Rayner <craig@craigrayner.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Licence: MIT
 * User: Craig Rayner
 * Date: 3/01/2021
 * Time: 10:01
 */
namespace App\Provider;

use App\Items\Day;

/**
 * Class DayProvider
 * @package App\Provider
 * @author Craig Rayner <craig@craigrayner.com>
 */
class DayProvider extends AbstractProvider
{
    /**
     * @var string
     */
    protected string $method = 'getDays';

    /**
     * @var string
     */
    protected string $itemName = Day::class;
}
