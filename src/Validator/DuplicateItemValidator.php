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
use App\Items\DuplicateNameInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class DaysValidator
 * @package App\Validator
 * @author Craig Rayner <craig@craigrayner.com>
 */
class DuplicateItemValidator extends ConstraintValidator
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

        $fields = $constraint->fields;
        foreach ($value as $q=>$item)
        {
            $duplicates = $value->filter(function(DuplicateNameInterface $datum) use ($item, $fields) {
                if ($datum->getId() !== $item->getId()) {
                    $duplicate = true;
                    foreach ($fields as $name) {
                        $method = 'is' . ucfirst($name);
                        if (!method_exists($datum, $method)) {
                            $method = 'get' . ucfirst($name);
                        }
                        if (!method_exists($datum, $method)) throw new \InvalidArgumentException(sprintf('The property "%s" does not have a valid getter in "%s".', $name, get_class($datum)));
                        if ($item->$method() !== $datum->$method()) $duplicate = false;
                    }
                    if ($duplicate) return $datum;
                }
            });

            if ($duplicates->count() > 0) {
                $key = $value->indexOf($duplicates->first());
                if ($q < $key) {
                    $this->context->buildViolation($constraint->message)
                        ->setCode(DuplicateItem::DUPLICATE_NAME_ERROR)
                        ->setTranslationDomain('messages')
                        ->atPath('[' . $key . '].'.$constraint->errorPath)
                        ->addViolation();
                }
            }
        }
    }
}
