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
 * Time: 08:35
 */
namespace App\Provider;

use App\Manager\DataManager;
use App\Manager\ItemSerialiser;
use Psr\Container\ContainerInterface;

class ProviderFactory
{
    /**
     * @var array
     */
    private static array $instances = [];

    /**
     * @var ContainerInterface
     */
    private static ContainerInterface $container;

    /**
     * @var DataManager
     */
    private static DataManager $dataManager;

    /**
     * @var ProviderFactoryx
     */
    private static ProviderFactory $factory;

    /**
     * ProviderFactory constructor.
     * @param ContainerInterface $container
     * @param DataManager $dataManager
     */
    public function __construct(
        ContainerInterface $container,
        DataManager $dataManager
    )
    {
        self::$container = $container;
        self::$dataManager = $dataManager;
        self::$factory = $this;
    }

    /**
     * create
     * 26/12/2020 08:40
     * @param string $entityName
     * @return ProviderInterface
     */
    public static function create(string $entityName): ProviderInterface
    {
        // The $entityName could be the plain name or the namespace name of the entity.
        // e.g. App\Modules\System\Entity\Module or Module
        $namespace = dirname($entityName);
        $entityName = basename($entityName);

        if (isset(self::$instances[$entityName])) {
            return self::$instances[$entityName];
        }

        $providerName = str_replace('Items', 'Provider', $namespace) . '\\' . $entityName . 'Provider';

        if (!self::getContainer()->has($providerName)) {
            try {
                self::getContainer()->set($providerName, new $providerName(self::$factory));
                return self::addInstance($entityName, self::getContainer()->get($providerName));
            } catch (\Exception $e) {
                throw new ProviderException(sprintf('The Item Provider for the "%s" item is not available. The namespace used was %s', $entityName, $providerName));
            }
        } else {
            return self::addInstance($entityName, self::getContainer()->get($providerName));
        }
    }

    /**
     * @return ContainerInterface
     */
    public static function getContainer(): ContainerInterface
    {
        return self::$container;
    }

    /**
     * addInstance
     * 26/12/2020 09:02
     * @param string $name
     * @param ProviderInterface $provider
     * @return ProviderInterface
     */
    public static function addInstance(string $name, ProviderInterface $provider): ProviderInterface
    {
        if (!key_exists($name, self::$instances)) self::$instances[$name] = $provider;

        return self::$instances[$name];
    }

    /**
     * @return DataManager
     */
    public static function getDataManager(): DataManager
    {
        return self::$dataManager;
    }
}
