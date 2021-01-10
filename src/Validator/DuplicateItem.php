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
 * Date: 14/12/2020
 * Time: 16:10
 */
namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Class Days
 * @package App\Validator
 * @author Craig Rayner <craig@craigrayner.com>
 */
class DuplicateItem extends Constraint
{
    const DUPLICATE_NAME_ERROR = '3e398a9b-6d9a-4a93-b824-032eb91bb82a';

    protected static $errorNames = [
        self::DUPLICATE_NAME_ERROR => 'DUPLICATE_NAME_ERROR',
    ];

    /**
     * @var string
     */
    public string $message = 'This name is a duplicate.';

    /**
     * @var array|string[]
     */
    public array $fields = ['name'];

    public string $errorPath = 'name';
}