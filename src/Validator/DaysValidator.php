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

use App\Items\Day;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class DaysValidator
 * @package App\Validator
 * @author Craig Rayner <craig@craigrayner.com>
 */
class DaysValidator extends ConstraintValidator
{
    /**
     * validate
     * 14/12/2020 16:11
     * @param mixed $value
     * @param \Symfony\Component\Validator\Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        foreach ($value as $q=>$item)
        {
            $duplicates = $value->filter(function(Day $day) use ($item) {
                if ($day !== $item && $item->getName() === $day->getName()) return $day;
            });

            if ($duplicates->count() > 0) {
                $key = $value->indexOf($duplicates->first());
                if ($q < $key) {
                    $this->context->buildViolation($constraint->message)
                        ->setCode(Days::DUPLICATE_NAME_ERROR)
                        ->setTranslationDomain('messages')
                        ->atPath('[' . $key . '].name')
                        ->addViolation();
                }
            }
        }
    }
}
