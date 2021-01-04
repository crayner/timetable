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
 * Date: 4/01/2021
 * Time: 08:24
 */
namespace App\Provider;

use App\Items\ClassDetail;

/**
 * Class ClassDetailProvider
 * @package App\Provider
 * @author Craig Rayner <craig@craigrayner.com>
 */
class ClassDetailProvider extends AbstractProvider
{
    /**
     * @var string
     */
    protected string $method = 'getClasses';

    /**
     * @var string
     */
    protected string $itemName = ClassDetail::class;
}
