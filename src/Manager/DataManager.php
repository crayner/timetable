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
    private function getFileName(): string
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
     * @return ArrayCollection
     */
    public function getGrades(): ArrayCollection
    {
        return $this->grades;
    }

    /**
     * @param ArrayCollection $grades
     * @return DataManager
     */
    public function setGrades(ArrayCollection $grades): DataManager
    {
        $this->grades = $grades;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getRooms(): ArrayCollection
    {
        return $this->rooms;
    }

    /**
     * @param ArrayCollection $rooms
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
     * @return ArrayCollection
     */
    public function getDays(): ArrayCollection
    {
        return $this->days;
    }

    /**
     * @param ArrayCollection $days
     * @return DataManager
     */
    public function setDays(ArrayCollection $days): DataManager
    {
        $this->days = $days;
        return $this;
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
     * 21/12/2020 08:16
     * @return bool
     */
    public function readFile(): bool
    {
        if ($this->isFileAvailable()) {
            $data = Yaml::parse(file_get_contents($this->getFileName()));
            $this->deSerialise($data);
            return true;
        }
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
        return [
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
//            ->setLines(new ArrayCollection($data['lines']), true)
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
            for ($i=$this->getRoomCount(); $i<=$count; $i++) {
                $room = new Room();
                $room->setName('Room ' . strval($i))
                    ->setSize($this->getRoomCapacity())
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
            for ($i=$this->getGradeCount(); $i<=$count; $i++) {
                $grade = new Grade();
                $grade->setName('Grade/Year/Form ' . strval($i))
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
            for ($i=$this->getDayCount(); $i<=$count; $i++) {
                $day = new Day();
                $day->setName('Day ' . strval($i))
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
     * @param string $secret
     * @return DataManager
     */
    public function setSecret(string $secret): DataManager
    {
        if ($this->getName() !== '') {
            $secret = $this->getEncoder()->encodePassword($this->getName() . $secret);
            $this->secret = $secret;
        }
        return $this;
    }


}
