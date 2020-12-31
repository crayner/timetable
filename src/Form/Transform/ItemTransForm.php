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
     * ItemIdTransForm constructor.
     * @param string $class
     */
    public function __construct(string $class)
    {
        $this->class = $class;
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
     * 31/12/2020 13:41
     * @param mixed $value
     * @return mixed|null
     */
    public function reverseTransform($value)
    {
        if (is_string($value)) {
            $provider = ProviderFactory::create($this->class);
            dump($provider,$provider->find($value));
            return $provider->find($value);
        }

        if (is_object($value) && get_class($value) === $this->class) return $value;

        if (empty($value)) return null;


        throw new TransformationFailedException();
    }
}
