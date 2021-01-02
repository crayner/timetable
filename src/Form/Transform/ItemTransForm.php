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

use App\Manager\TimetableManager;
use App\Provider\ProviderFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class ItemIdTransForm
 * @package App\Form\Transform
 * @author Craig Rayner <craig@craigrayner.com>
 */
class ItemTransForm implements DataTransformerInterface
{
    /**
     * @var string
     */
    private string $class;

    /**
     * @var bool
     */
    private bool $multiple;

    /**
     * ItemIdTransForm constructor.
     * @param string $class
     * @param bool $multiple
     */
    public function __construct(string $class, bool $multiple = false)
    {
        $this->class = $class;
        $this->multiple = $multiple;
    }

    /**
     * transform
     * 16/12/2020 17:29
     * @param mixed $value
     * @return mixed|void
     */
    public function transform($value)
    {
        if ($this->multiple) {
            if ($value instanceof ArrayCollection) return $value->toArray();
            $items = [];
            foreach ($value as $item) {
                if ($item instanceof $this->class)
                    $items[] = $item->getId();
                else
                    $items[] = $item;
            }

            return $items;
        }

        if ($value instanceof $this->class) {
            return $value->getId();
        }
    }

    /**
     * reverseTransform
     * 31/12/2020 13:41
     * @param mixed $value
     * @return mixed|null
     */
    public function reverseTransform($value)
    {
        if ($this->multiple) {
            $result = new ArrayCollection();
            $provider = ProviderFactory::create($this->class);
            foreach ($value as $id) {
                if (is_object($id) && get_class($id) === $this->class) {
                    $result->add($id);
                } else if (is_string($id)) {
                    $result->add($provider->find($id));
                }
            }
            return $result;
        }

        if (is_string($value)) {
            $provider = ProviderFactory::create($this->class);
            return $provider->find($value);
        }

        if (is_object($value) && get_class($value) === $this->class) return $value;

        if (empty($value)) return null;

        throw new TransformationFailedException();
    }
}
