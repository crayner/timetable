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
 * Date: 5/01/2021
 * Time: 07:51
 */
namespace App\Provider;

use App\Items\Room;

/**
 * Class RoomProvider
 * @package App\Provider
 * @author Craig Rayner <craig@craigrayner.com>
 */
class RoomProvider extends AbstractProvider
{
    /**
     * @var string
     */
    protected string $method = 'getRooms';

    /**
     * @var string
     */
    protected string $itemName = Room::class;
}
