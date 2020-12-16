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
 * Date: 16/12/2020
 * Time: 17:27
 */
namespace App\Form\Transform;

use App\Items\DuplicateNameInterface;
use App\Items\Grade;
use App\Manager\TimetableManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class ItemIdTransForm
 * @package App\Form\Transform
 * @author Craig Rayner <craig@craigrayner.com>
 */
class ItemIdTransForm implements DataTransformerInterface
{
    /**
     * @var string
     */
    private string $class;

    /**
     * @var TimetableManager
     */
    private TimetableManager $manager;

    /**
     * ItemIdTransForm constructor.
     * @param string $class
     * @param TimetableManager|string $manager
     */
    public function __construct(string $class, TimetableManager $manager)
    {
        $this->class = $class;
        $this->manager = $manager;
    }

    /**
     * transform
     * 16/12/2020 17:29
     * @param mixed $value
     * @return mixed|void
     */
    public function transform($value)
    {
        if ($value instanceof $this->class) {
            return $value->getId();
        }
    }

    /**
     * reverseTransform
     * 16/12/2020 17:30
     * @param mixed $value
     * @return mixed|void
     */
    public function reverseTransform($value)
    {
        if (is_string($value)) {
            return $this->findItem($value);
        }

        if (empty($value)) return;

        throw new TransformationFailedException();
    }

    /**
     * findItem
     * 16/12/2020 17:40
     * @param string $value
     * @return mixed
     */
    private function findItem(string $value)
    {
        switch ($this->class) {
            case Grade::class:
                $x = $this->manager->getGrades()->filter(function(Grade $grade) use ($value) {
                    if ($value === $grade->getId()) return $grade;
                });
                if ($x->count() !== 1) throw new TransformationFailedException();
                return $x->first();
                break;
            default:
                throw new TransformationFailedException();
        }
    }
}
