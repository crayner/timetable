<?php
/**
 * Created by PhpStorm.
 *
 * Timetable Creator
 * (c) 2020-2021 Craig Rayner <craig@craigrayner.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Licence: MIT
 * User: Craig Rayner
 * Date: 3/01/2021
 * Time: 16:34
 */
namespace App\Manager;

use App\Provider\ProviderException;
use App\Provider\ProviderFactory;
use App\Provider\ProviderItemInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class ItemSerialiser
 * @package App\Manager
 * @author Craig Rayner <craig@craigrayner.com>
 */
class ItemSerialiser
{
    /**
     * serialise
     * 3/01/2021 16:39
     * @param $data
     * @return array|string
     */
    public static function serialise($data)
    {
        if (is_null($data)) {
            return null;
        }

        if (is_iterable($data)) {
            $result = [];
            foreach ($data as $datum) {
                if (is_object($datum) && key_exists(ProviderItemInterface::class, class_implements($datum, false))) {
                    $result[] = $datum->getId();
                } else {
                    if (is_object($datum)) {
                        throw new ProviderException(sprintf('The item "%s" provided was not valid! It must implement "%s"', get_class($datum), ProviderItemInterface::class));
                    } else {
                        throw new ProviderException('The item provided was not valid! It was not an object!');

                    }
                }
            }
            return $result;
        }

        if (class_implements(ProviderItemInterface::class, $data)) {
            return $data->getId();
        }

        throw new ProviderException('The item provided was not valid!');
    }

    /**
     * deserialise
     * 3/01/2021 16:40
     * @param string $className
     * @param $data
     * @return ArrayCollection|Object|null
     */
    public static function deserialise(string $className, $data)
    {
        if (is_iterable($data)) {
            $result = new ArrayCollection();
            foreach ($data as $id) {
                if (is_string($id) && !empty($id)) {
                    $result->add(ProviderFactory::create($className)->find($id));
                } else {
                    throw new ProviderException('The item provided was not valid!');
                }
            }
            return $result;
        }

        if (is_string($data)) {
            return ProviderFactory::create($className)->find($data);
        }

        if (is_null($data)) {
            return null;
        }

        throw new ProviderException('The item provided was not valid!');
    }
}