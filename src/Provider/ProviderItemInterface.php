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
 * Time: 15:45
 */

namespace App\Provider;

interface ProviderItemInterface
{
    /**
     * getId
     * 1/01/2021 15:45
     * @return string
     */
    public function getId(): string;
}