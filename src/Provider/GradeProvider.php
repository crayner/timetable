<?php
/**
 * Created by PhpStorm.
 *
 * Timetable Creator
 * (c) 2020-2020 Craig Rayner <craig@craigrayner.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Licence: MIT
 * User: Craig Rayner
 * Date: 26/12/2020
 * Time: 08:33
 */
namespace App\Provider;

use App\Items\Grade;

/**
 * Class GradeProvider
 * @package App\Provider
 * @author Craig Rayner <craig@craigrayner.com>
 */
class GradeProvider extends AbstractProvider
{
    /**
     * @var string
     */
    protected string $method = 'getGrades';

    /**
     * @var string
     */
    protected string $itemName = Grade::class;
}
