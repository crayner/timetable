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
 * Date: 19/12/2020
 * Time: 08:37
 */
namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Class UserPasswordContent
 * @package App\Validator
 * @author Craig Rayner <craig@craigrayner.com>
 *
 * Set any min value to zero (0) disable that option.
 */
class UserPasswordContent extends Constraint
{

    const INVALID_CONTENT_ERROR = 'f19d32a7-4e2b-46f5-bddd-b8faae0f765f';

    protected static $errorNames = [
        self::INVALID_CONTENT_ERROR => 'INVALID_CONTENT_ERROR',
    ];

    /**
     * Minimum length of the password in characters.
     * A value of zero (0) turns the validator off.
     * @var int
     */
    public int $minLength = 8;

    /**
     * Minimum number of lowercase characters.
     * @var int
     */
    public int $minLowerCase = 1;

    /**
     * Minimum number of uppercase characters.
     * @var int
     */
    public int $minUpperCase = 1;

    /**
     * Minimum number of numeric characters.
     * @var int
     */
    public int $minNumeric = 1;

    /**
     * Minimum number of special characters.
     * @var int
     */
    public int $minSpecial = 1;

    /**
     * @var string
     * If blank, then use preg_match all special characters.
     */
    public string $specialCharacters = '';

    /**
     * @var string
     */
    public string $message = 'The password content is not valid.';

    public string $translation_domain = 'validators';
}
