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
 * Date: 1/01/2021
 * Time: 11:15
 */
namespace App\Provider;

use App\Items\Staff;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class StaffProvider
 * @package App\Provider
 * @author Craig Rayner <craig@craigrayner.com>
 */
class StaffProvider extends AbstractProvider
{
    /**
     * @var string
     */
    protected string $method = 'getStaff';

    /**
     * @var string
     */
    protected string $itemName = Staff::class;

    /**
     * getStaffChoices
     * 1/01/2021 12:03
     * @return array
     */
    public function getStaffChoices(): array
    {
        try {
            $iterator = $this->all()->getIterator();
        } catch (\Exception $e) {
            return $this->all()->toArray();
        }

        $iterator->uasort(
            function (Staff $a, Staff $b) {
                return $a->getName() > $b->getName() ? 1 : -1 ;
            }
        );

        return iterator_to_array($iterator, false);
    }
}
