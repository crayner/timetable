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
 * Date: 18/12/2020
 * Time: 07:51
 */
namespace App\Manager;

use App\Helper\SecurityEncoder;
use App\Items\Day;
use App\Items\Grade;
use App\Items\Room;
use App\Items\Staff;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Yaml\Yaml;

/**
 * Class DataManager
 * @package App\Manager
 * @author Craig Rayner <craig@craigrayner.com>
 */
class DataManager
{
    /**
     * @var string
     */
    private string $name;

    /**
     * @var string
     */
    private string $password;

    /**
     * @var \DateTimeImmutable
     */
    private \DateTimeImmutable $createdOn;

    /**
     * @var int
     */
    private int $studentsPerGrade = 90;

    /**
     * @var int
     */
    private int $roomCapacity = 30;

    /**
     * @var int
     */
    private int $periods = 6;

    /**
     * @var ArrayCollection
     */
    private ArrayCollection $staff;

    /**
     * @var ArrayCollection
     */
    private ArrayCollection $grades;

    /**
     * @var ArrayCollection
     */
    private ArrayCollection $rooms;

    /**
     * @var ArrayCollection
     */
    private ArrayCollection $days;

    /**
     * @var ArrayCollection
     */
    private ArrayCollection $lines;

    /**
     * @var SecurityEncoder
     */
    private SecurityEncoder $encoder;

    /**
     * @var string
     */
    private string $secret;

    /**
     * @var bool
     */
    private bool $readFile = true;

    /**
     * DataManager constructor.
     * @param string $name
     */
    public function __construct(string $name = '')
    {
        $this->setName($name)
            ->setStaff(new ArrayCollection())
            ->setGrades(new ArrayCollection())
            ->setRooms(new ArrayCollection())
            ->setDays(new ArrayCollection())
            ->setLines(new ArrayCollection())
        ;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return isset($this->name) ? $this->name : '';
    }

    /**
     * @param string $name
     * @return DataManager
     */
    public function setName(string $name): DataManager
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return isset($this->password) ? $this->password : '';
    }

    /**
     * @param string $password
     * @return DataManager
     */
    public function setPassword(string $password): DataManager
    {
        $this->password = $password;
        return $this;
    }

    /**
     * encodePassword
     * 20/12/2020 07:54
     * @param string $raw
     * @return bool
     */
    public function encodePassword(string $raw): bool
    {
        $this->setPassword($this->getEncoder()->encodePassword($raw));

        return $this->getEncoder()->isPasswordValid($this->getPassword(), $raw);
    }

    /**
     * isPasswordValid
     * 20/12/2020 07:52
     * @param string $raw
     * @return bool
     */
    public function isPasswordValid(string $raw): bool
    {
        return $this->getEncoder()->isPasswordValid($this->getPassword(), $raw);
    }

    /**
     * isFileAvailable
     * 18/12/2020 08:11
     * @return bool
     */
    public function isFileAvailable(): bool
    {
        return realpath($this->getFileName());
    }

    /**
     * getFileName
     * 18/12/2020 08:15
     * @return string
     */
    public function getFileName(): string
    {
        return __DIR__ . '/../../config/data/' . $this->getName() . '.yaml';
    }

    /**
     * @return int
     */
    public function getStudentsPerGrade(): int
    {
        return $this->studentsPerGrade;
    }

    /**
     * @param int $studentsPerGrade
     * @return DataManager
     */
    public function setStudentsPerGrade(int $studentsPerGrade): DataManager
    {
        $this->studentsPerGrade = $studentsPerGrade;
        return $this;
    }

    /**
     * @return int
     */
    public function getRoomCapacity(): int
    {
        return $this->roomCapacity;
    }

    /**
     * @param int $roomCapacity
     * @return DataManager
     */
    public function setRoomCapacity(int $roomCapacity): DataManager
    {
        $this->roomCapacity = $roomCapacity;
        return $this;
    }

    /**
     * @return int
     */
    public function getPeriods(): int
    {
        return $this->periods;
    }

    /**
     * @param int $periods
     * @return DataManager
     */
    public function setPeriods(int $periods): DataManager
    {
        $this->periods = $periods;
        return $this;
    }

    /**
     * @param bool $serialise
     * @return ArrayCollection
     */
    public function getStaff(bool $serialise = false): ArrayCollection
    {
        if ($this->staff->count() === 0) {
            $this->readFile();
        }
        if ($serialise) {
            $list = new ArrayCollection();
            foreach ($this->staff as $staff) {
                $list->add($staff->serialise());
            }
            return $list;
        }
        return $this->staff;
    }

    /**
     * @param ArrayCollection $staff
     * @return DataManager
     */
    public function setStaff(ArrayCollection $staff): DataManager
    {
        $this->staff = $staff;
        return $this;
    }

    /**
     * getgrades
     * 22/12/2020 08:07
     * @param bool $serialise
     * @return ArrayCollection
     */
    public function getgrades(bool $serialise = false): ArrayCollection
    {
        if ($this->grades->count() === 0) {
            $this->readFile();
        }
        if ($serialise) {
            $list = new ArrayCollection();
            foreach ($this->grades as $grade) {
                $list->add($grade->serialise());
            }
            return $list;
        }
        return $this->grades;
    }

    /**
     * @param ArrayCollection $grades
     * @param bool $deSerialise
     * @return DataManager
     */
    public function setgrades(ArrayCollection $grades, bool $deSerialise = false): DataManager
    {
        if ($deSerialise) {
            $new = new ArrayCollection();
            foreach ($grades as $item) {
                $grade = new grade();
                $grade->deserialise($item);
                $new->add($grade);
            }
            $grades = $new;
        }
        $this->grades = $grades;
        return $this;
    }

    /**
     * removeGrade
     * 22/12/2020 08:34
     * @param string $id
     * @return DataManager
     */
    public function removeGrade(string $id): DataManager
    {
        $grades = $this->getGrades()->filter(function(Grade $grade) use ($id) {
            if ($id !== $grade->getId()) return $grade;
        });

        return $this->setGrades($grades);
    }

    /**
     * getRooms
     * 22/12/2020 08:07
     * @param bool $serialise
     * @return ArrayCollection
     */
    public function getRooms(bool $serialise = false): ArrayCollection
    {
        if ($this->rooms->count() === 0) {
            $this->readFile();
        }
        if ($serialise) {
            $list = new ArrayCollection();
            foreach ($this->rooms as $room) {
                $list->add($room->serialise());
            }
            return $list;
        }
        return $this->rooms;
    }

    /**
     * @param ArrayCollection $rooms
     * @param bool $deSerialise
     * @return DataManager
     */
    public function setRooms(ArrayCollection $rooms, bool $deSerialise = false): DataManager
    {
        if ($deSerialise) {
            $new = new ArrayCollection();
            foreach ($rooms as $item) {
                $room = new Room();
                $room->deserialise($item);
                $new->add($room);
            }
            $rooms = $new;
        }
        $this->rooms = $rooms;
        return $this;
    }

    /**
     * removeRoom
     * 22/12/2020 08:34
     * @param string $id
     * @return DataManager
     */
    public function removeRoom(string $id): DataManager
    {
        $rooms = $this->getRooms()->filter(function(Room $room) use ($id) {
            if ($id !== $room->getId()) return $room;
        });

        return $this->setRooms($rooms);
    }

    /**
     * sortRooms
     * 22/12/2020 08:44
     * @return DataManager
     */
    public function sortRooms(): DataManager
    {
        $iterator = $this->getRooms()->getIterator();

        $iterator->uasort(
            function (Room $a, Room $b) {
                return $a->getName() > $b->getName() ? 1 : -1 ;
            }
        );
        return $this->setRooms(new ArrayCollection(iterator_to_array($iterator, false)));
    }

    /**
     * getDays
     * 22/12/2020 09:01
     * @param bool $serialise
     * @return ArrayCollection
     */
    public function getDays(bool $serialise = false): ArrayCollection
    {
        if ($this->days->count() === 0) {
            $this->readFile();
        }
        if ($serialise) {
            $list = new ArrayCollection();
            foreach ($this->days as $day) {
                $list->add($day->serialise());
            }
            return $list;
        }
        return $this->days;
    }

    /**
     * setDays
     * 22/12/2020 09:05
     * @param ArrayCollection $days
     * @param bool $deSerialise
     * @return DataManager
     */
    public function setDays(ArrayCollection $days, bool $deSerialise = false): DataManager
    {
        if ($deSerialise) {
            $new = new ArrayCollection();
            foreach ($days as $item) {
                $day = new Day();
                $day->deserialise($item);
                $new->add($day);
            }
            $days = $new;
        }
        $this->days = $days;
        return $this;
    }

    /**
     * removeDay
     * 22/12/2020 08:34
     * @param string $id
     * @return DataManager
     */
    public function removeDay(string $id): DataManager
    {
        $days = $this->getDays()->filter(function(Day $day) use ($id) {
            if ($id !== $day->getId()) return $day;
        });

        return $this->setDays($days);
    }

    /**
     * @return ArrayCollection
     */
    public function getLines(): ArrayCollection
    {
        return $this->lines;
    }

    /**
     * @param ArrayCollection $lines
     * @return DataManager
     */
    public function setLines(ArrayCollection $lines): DataManager
    {
        $this->lines = $lines;
        return $this;
    }

    /**
     * readFile
     * 22/12/2020 08:14
     * @return bool
     */
    public function readFile(): bool
    {
        if ($this->isFileAvailable() && $this->readFile) {
            $data = Yaml::parse(file_get_contents($this->getFileName()));
            $this->deSerialise($data);
            $this->readFile = false;
            return true;
        }
        if (!$this->readFile) return true;
        return false;
    }

    /**
     * writeFile
     * 18/12/2020 08:42
     * @return bool
     */
    public function writeFile(): bool
    {
        if ($this->getName() !== '' and $this->getPassword() !== '') {
            $data = $this->serialise();
            if (file_put_contents($this->getFileName(), Yaml::dump($data, 8)) === false) return false;
            return true;
        }
        return false;
    }

    /**
     * serialise
     * 18/12/2020 08:45
     */
    public function serialise(): array
    {
        $result = [
            'name' => $this->getName(),
            'password' => $this->getPassword(),
            'secret' => $this->getSecret(),
            'created_on' => date('c'),
            'staff' => $this->getStaff(true)->toArray(),
            'studentsPerGrade' => $this->getStudentsPerGrade(),
            'roomCapacity' => $this->getRoomCapacity(),
            'rooms' => $this->getRooms(true)->toArray(),
            'periods' => $this->getPeriods(),
            'days' => $this->getDays(true)->toArray(),
            'grades' => $this->getGrades(true)->toArray(),
            'lines' => $this->getLines(true)->toArray(),
        ];
        return $result;
    }

    /**
     * deSerialise
     * 18/12/2020 08:45
     * @param array $data
     * @return DataManager
     */
    public function deSerialise(array $data): DataManager
    {
        return $this->setName($data['name'])
            ->setPassword($data['password'])
            ->setCreatedOn(new \DateTimeImmutable($data['created_on']))
            ->setStudentsPerGrade($data['studentsPerGrade'])
            ->setRoomCapacity($data['roomCapacity'])
            ->setPeriods($data['periods'])
            ->setRooms(new ArrayCollection($data['rooms']), true)
            ->setDays(new ArrayCollection($data['days']), true)
            ->setGrades(new ArrayCollection($data['grades']), true)
            ->setLines(new ArrayCollection($data['lines']), true)
            ->setSecret($data['secret'], false)
            ->setStaff(new ArrayCollection($data['staff']), true);
    }

    /**
     * @return SecurityEncoder
     */
    public function getEncoder(): SecurityEncoder
    {
        return $this->encoder = isset($this->encoder) ? $this->encoder : new SecurityEncoder();
    }

    /**
     * getStaffCount
     * 20/12/2020 08:32
     * @return int
     */
    public function getStaffCount(): int
    {
        return $this->getStaff()->count();
    }

    /**
     * setStaffCount
     * 20/12/2020 08:33
     * @param int $count
     * @return DataManager
     */
    public function setStaffCount(int $count): DataManager
    {
        if ($count > $this->getStaffCount()) {
            for ($i=$this->getStaffCount(); $i<=$count; $i++) {
                $staff = new Staff();
                $staff->setName('Staff Member ' . strval($i))->getId();
                $this->staff->add($staff);
            }
        }
        return $this;
    }

    /**
     * getRoomCount
     * 20/12/2020 08:32
     * @return int
     */
    public function getRoomCount(): int
    {
        return $this->getRooms()->count();
    }

    /**
     * setRoomCount
     * 20/12/2020 08:33
     * @param int $count
     * @return DataManager
     */
    public function setRoomCount(int $count): DataManager
    {
        if ($count > $this->getRoomCount()) {
            for ($i=$this->getRoomCount(); $i<$count; $i++) {
                $room = new Room();
                $room->setName('Room ' . strval($i + 1))
                    ->setCapacity($this->getRoomCapacity())
                    ->getId();
                $this->rooms->add($room);
            }
        }
        return $this;
    }

    /**
     * getGradeCount
     * 20/12/2020 08:32
     * @return int
     */
    public function getGradeCount(): int
    {
        return $this->getGrades()->count();
    }

    /**
     * setGradeCount
     * 20/12/2020 08:33
     * @param int $count
     * @return DataManager
     */
    public function setGradeCount(int $count): DataManager
    {
        if ($count > $this->getGradeCount()) {
            for ($i=$this->getGradeCount(); $i<$count; $i++) {
                $grade = new Grade();
                $grade->setName('Grade/Year/Form ' . strval($i + 1))
                    ->setStudentCount($this->getStudentsPerGrade())
                    ->getId();
                $this->grades->add($grade);
            }
        }
        return $this;
    }

    /**
     * getDayCount
     * 20/12/2020 08:32
     * @return int
     */
    public function getDayCount(): int
    {
        return $this->getDays()->count();
    }

    /**
     * setDayCount
     * 20/12/2020 08:33
     * @param int $count
     * @return DataManager
     */
    public function setDayCount(int $count): DataManager
    {
        if ($count > $this->getDayCount()) {
            for ($i=$this->getDayCount(); $i<$count; $i++) {
                $day = new Day();
                $day->setName('Day ' . strval($i + 1))
                    ->setPeriods($this->getPeriods())
                    ->getId();
                $this->days->add($day);
            }
        }
        return $this;
    }

    /**
     * getLineCount
     * 20/12/2020 08:32
     * @return int
     */
    public function getLineCount(): int
    {
        return $this->getLines()->count();
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedOn(): \DateTimeImmutable
    {
        return $this->createdOn = isset($this->createdOn) ? $this->createdOn : new \DateTimeImmutable('-25 Hours');
    }

    /**
     * @param \DateTimeImmutable $createdOn
     * @return DataManager
     */
    public function setCreatedOn(\DateTimeImmutable $createdOn): DataManager
    {
        $this->createdOn = $createdOn;
        return $this;
    }

    /**
     * unlink
     * 21/12/2020 08:35
     * @return bool
     */
    public function unlink(): bool
    {
        if ($this->isFileAvailable()) {
            unlink($this->getFileName());
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public function getSecret(): string
    {
        return isset($this->secret) ? $this->secret : '';
    }

    /**
     * setSecret
     * 21/12/2020 16:06
     * @param string $secret
     * @param bool $createSecret
     * @return DataManager
     */
    public function setSecret(string $secret, bool $createSecret = true): DataManager
    {
        if ($createSecret && $this->getName() !== '') {
            $secret = $this->getEncoder()->encodePassword($this->getName() . $secret);
            $this->secret = $secret;
        } else if (!$createSecret) {
            $this->secret = $secret;
        }
        return $this;
    }
}
