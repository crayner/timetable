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
 * Date: 26/12/2020
 * Time: 08:42
 */
namespace App\Provider;

use App\Manager\DataManager;
use App\Manager\Serialiser;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Yaml\Yaml;

/**
 * Class AbstractProvider
 * @package App\Provider
 * @author Craig Rayner <craig@craigrayner.com>
 */
class AbstractProvider implements ProviderInterface
{
    /**
     * @var ProviderFactory
     */
    private ProviderFactory $factory;

    /**
     * @var ArrayCollection
     */
    private $items;

    /**
     * AbstractProvider constructor.
     * @param ProviderFactory $factory
     */
    public function __construct(ProviderFactory $factory)
    {
        $this->factory = $factory;
        $this->items = new ArrayCollection();
    }

    /**
     * @return ProviderFactory
     */
    public function getFactory(): ProviderFactory
    {
        return $this->factory;
    }

    /**
     * all
     * 26/12/2020 09:21
     * @return ArrayCollection
     */
    public function all(): ArrayCollection
    {
        $method = $this->getMethod();
        $this->getDataManager()->setReadFile(true);
        $this->items = $this->getDataManager()->$method();
        return $this->items;
    }

    /**
     * getDataManager
     * 26/12/2020 09:23
     * @return DataManager
     */
    private function getDataManager(): DataManager
    {
        return $this->getFactory()->getDataManager();
    }

    /**
     * find
     * 26/12/2020 09:32
     * @param string|null $id
     */
    public function find(?string $id = null)
    {
        $items = $this->all()->filter(function (ProviderItemInterface $item) use ($id) {
            if ($id === $item->getId()) return $item;
        });
        if ($items->count() > 1) throw new ProviderException(sprintf('The item "%s" returned an invalid result for a find of "%s".', $this->getItemName(), $id));
        return $items->first() ?: null;
    }

    /**
     * getMethod
     * 26/12/2020 09:32
     * @return string
     */
    protected function getMethod(): string
    {
        return $this->method;
    }

    /**
     * getItemName
     * 26/12/2020 09:33
     * @return string
     */
    public function getItemName(): string
    {
        return $this->itemName;
    }
}
