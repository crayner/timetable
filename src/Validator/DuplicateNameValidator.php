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
use App\Items\NameInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class DaysValidator
 * @package App\Validator
 * @author Craig Rayner <craig@craigrayner.com>
 */
class DuplicateNameValidator extends ConstraintValidator
{
    /**
     * validate
     * 14/12/2020 16:11
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof ArrayCollection) return;

        foreach ($value as $q=>$item)
        {
            $duplicates = $value->filter(function(NameInterface $day) use ($item) {
                if ($day !== $item && $item->getName() === $day->getName()) return $day;
            });

            if ($duplicates->count() > 0) {
                $key = $value->indexOf($duplicates->first());
                if ($q < $key) {
                    $this->context->buildViolation($constraint->message)
                        ->setCode(DuplicateName::DUPLICATE_NAME_ERROR)
                        ->setTranslationDomain('messages')
                        ->atPath('[' . $key . '].name')
                        ->addViolation();
                }
            }
        }
    }
}
