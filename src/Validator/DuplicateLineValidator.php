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

use App\Items\Line;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class DuplicateLineValidator
 * @package App\Validator
 * @author Craig Rayner <craig@craigrayner.com>
 */
class DuplicateLineValidator extends ConstraintValidator
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
            $duplicates = $value->filter(function(Line $line) use ($item) {
                if ($line->getId() !== $item->getId() && $line->isEqualTo($item)) return $line;
            });

            if ($duplicates->count() > 0) {
                $key = $value->indexOf($duplicates->first());
                if ($q < $key) {
                    $this->context->buildViolation($constraint->message)
                        ->setCode(DuplicateLine::DUPLICATE_LINE_ERROR)
                        ->setTranslationDomain('messages')
                        ->atPath('[' . $key . '].name')
                        ->addViolation();
                }
            }
        }
    }
}
