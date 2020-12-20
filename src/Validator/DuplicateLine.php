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
 * Date: 17/12/2020
 * Time: 10:50
 */

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

class DuplicateLine extends Constraint
{
    const DUPLICATE_LINE_ERROR = '37359744-d060-481f-afef-a03404de64f9';

    protected static $errorNames = [
        self::DUPLICATE_LINE_ERROR => 'DUPLICATE_LINE_ERROR',
    ];

    /**
     * @var string
     */
    public string $message = 'This line is a duplicate.';
}
