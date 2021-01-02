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
 * Date: 2/01/2021
 * Time: 13:05
 */

namespace App\Manager;

use App\Provider\ProviderItemInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * Class ItemNormaliser
 * @package App\Manager
 * @author Craig Rayner <craig@craigrayner.com>
 */
class ItemNormaliser implements ContextAwareNormalizerInterface
{
    /**
     * @var ObjectNormalizer
     */
    private ObjectNormalizer $normaliser;

    /**
     * ItemNormaliser constructor.
     */
    public function __construct()
    {
        $this->normaliser = new ObjectNormalizer();
    }

    /**
     * supportsNormalization
     * 2/01/2021 13:08
     * @param mixed $data
     * @param string|null $format
     * @param array $context
     * @return bool
     */
    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return key_exists(ProviderItemInterface::class, class_implements($data));
    }

    /**
     * normalize
     * 2/01/2021 13:08
     * @param mixed $object
     * @param string|null $format
     * @param array $context
     * @return array|\ArrayObject|bool|float|int|string|void|null
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        dump($object,$format,$context);
        $data = $this->normalizer->normalize($object, $format, $context);

        // Here, add, edit, or delete some data:
  //      $data['href']['self'] = $this->router->generate('topic_show', [
    //        'id' => $topic->getId(),
      //  ], UrlGeneratorInterface::ABSOLUTE_URL);

        return $data;

    }
}
