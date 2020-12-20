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
 * Time: 08:38
 */
namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class UserPasswordContentValidator
 * @package App\Validator
 * @author Craig Rayner <craig@craigrayner.com>
 */
class UserPasswordContentValidator extends ConstraintValidator
{
    /**
     * validate
     * 19/12/2020 08:49
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (empty($value) || $constraint->minLength === 0) return;

        $valid = true;
        if (\strlen($value) < $constraint->minLength) {
            $valid = false;
        }

        if ($constraint->minLowerCase > 0) {
            \preg_match_all('/[a-z]/', $value,$matches);
            if (count($matches) < $constraint->minLowerCase) {
                $valid = false;
            }
        }

        if ($constraint->minUpperCase > 0) {
            \preg_match_all('/[A-Z]/', $value,$matches);
            if (count($matches) < $constraint->minUpperCase) {
                $valid = false;
            }
        }

        if ($constraint->minNumeric > 0) {
            \preg_match_all('/[0-9]/', $value,$matches);
            if (count($matches) < $constraint->minNumeric) {
                $valid = false;
            }
        }

        if ($constraint->minNumeric > 0) {
            \preg_match_all('/[0-9]/', $value,$matches);
            if (count($matches) < $constraint->minNumeric) {
                $valid = false;
            }
        }

        if ($constraint->minSpecial > 0) {
            if (\strlen($constraint->specialCharacters) > 0) {
                $characters = str_split($constraint->specialCharacters);
                $pattern = '/[';
                foreach ($characters as $char) {
                    $pattern .= '\\' . $char;
                }
                $pattern .= ']/';
            } else {
                $pattern = '/\W/';
            }
            \preg_match_all($pattern, $value,$matches);
            if (count($matches) < $constraint->minSpecial) {
                $valid = false;
            }
        }

        if (!$valid) {
            $this->context->buildViolation($constraint->message)
                ->setCode(UserPasswordContent::INVALID_CONTENT_ERROR)
                ->setTranslationDomain($constraint->translation_domain)
                ->addViolation();
        }
    }
}
