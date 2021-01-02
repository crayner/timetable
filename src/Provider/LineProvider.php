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
 * Date: 2/01/2021
 * Time: 12:27
 */
namespace App\Provider;

use App\Items\Line;

/**
 * Class LineProvider
 * @package App\Provider
 * @author Craig Rayner <craig@craigrayner.com>
 */
class LineProvider extends AbstractProvider
{
    /**
     * @var string
     */
    protected string $method = 'getLines';

    /**
     * @var string
     */
    protected string $itemName = Line::class;
}
