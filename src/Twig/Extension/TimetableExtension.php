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
 * Date: 24/12/2020
 * Time: 11:11
 */
namespace App\Twig\Extension;

use App\Manager\LineManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class TimetableExtension
 * @package App\Twig\Extension
 * @author Craig Rayner <craig@craigrayner.com>
 */
class TimetableExtension extends AbstractExtension
{
    /**
     * @var LineManager
     */
    private LineManager $manager;

    /**
     * TimetableExtension constructor.
     * @param LineManager $manager
     */
    public function __construct(LineManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * getFunctions
     * @return array|TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('getManager', [$this, 'getManager']),
        ];
    }

    /**
     * getManager
     * 7/01/2021 10:18
     * @return LineManager
     */
    public function getManager(): LineManager
    {
        return $this->manager;
    }
}
