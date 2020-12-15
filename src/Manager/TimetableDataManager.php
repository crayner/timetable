<?php
/**
 * Created by PhpStorm.
 *
 * timetable
 * (c) 2020 Craig Rayner <craig@craigrayner.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * User: craig
 * Date: 10/12/2020
 * Time: 14:52
 */
namespace App\Manager;

use App\Items\Day;
use App\Items\Grade;
use App\Items\Line;
use App\Items\Room;
use App\Items\Staff;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Yaml\Yaml;

/**
 * Class TimetableDataManager
 * @package App\Manager
 * @author Craig Rayner <craig@craigrayner.com>
 */
class TimetableDataManager
{
    /**
     * @var array
     */
    private array $data = [];

    /**
     * @var bool
     */
    private bool $injectMissing = false;

    /**
     * @var int
     */
    private int $staffCount = 0;

    /**
     * @var int
     */
    private int $roomCount = 0;

    /**
     * @var int
     */
    private int $gradeCount = 0;

    /**
     * @var int
     */
    private int $dayCount = 5;

    /**
     * @var int
     */
    private int $periods;

    /**
     * @var int
     */
    private int $studentsPerGrade;

    /**
     * @var int
     */
    private int $roomCapacity;

    /**
     * @var array
     */
    private array $messages = [];

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Data.
     *
     * @param array $data
     * @return TimetableDataManager
     */
    public function setData(array $data): TimetableDataManager
    {
        $this->data = $data;
        $this->deserialise();
        return $this;
    }

    /**
     * getSerializedData
     * @return array
     * 14/12/2020 10:22
     */
    public function getSerializedData(): array
    {
        return [
            'name' => $this->getName(),
            'created_at' => $this->getCreatedAt()->format('c'),
            'staff' => $this->getStaff(true)->toArray(),
            'grades' => $this->getGrades(true)->toArray(),
            'rooms' => $this->getRooms(true)->toArray(),
            'days' => $this->getDays(true)->toArray(),
            'periods' => $this->getPeriods(),
            'studentsPerGrade' => $this->getStudentsPerGrade(),
        ];
    }

    /**
     * getName
     * @return string|null
     * 11/12/2020 12:45
     */
    public function getName(): ?string
    {
        return key_exists('name', $this->data) ? $this->data['name'] : null;
    }

    /**
     * Name.
     *
     * @param string $name
     * @return TimetableDataManager
     */
    public function setName(string $name): TimetableDataManager
    {
        $this->data['name'] = $name;
        return $this;
    }

    /**
     * createFile
     * 11/12/2020 09:13
     * @param string $name
     * @return bool
     */
    public function createFile(string $name): bool
    {
        $data = [];
        $data['name'] = $name;
        $this->setData($data);

        $this->setStaff($this->createStaff());
        $this->setRooms($this->createRooms());
        $this->setGrades($this->createGrades());
        $this->setDays($this->createDays());

        return true;
    }

    /**
     * getFilePath
     * @return string
     * 10/12/2020 14:48
     */
    private function getFilePath(): string
    {
        return __DIR__ .'/../../config/data/'.$this->getName().'.yaml';
    }

    /**
     * writeFile
     * @return bool
     * 11/12/2020 09:19
     */
    public function writeFile(): bool
    {
        if ($this->getName()) {
            $this->data['created_at'] = date('c');
            return file_put_contents($this->getFilePath(), Yaml::dump($this->getSerializedData(), 8)) !== false;
        }
        return false;
    }

    /**
     * unlink
     * 11/12/2020 10:52
     */
    public function unlink()
    {
        if (realpath($this->getFilePath()) !== false) unlink($this->getFilePath());
    }

    /**
     * getStaff
     * @param bool $serialised
     * @return array
     * 11/12/2020 12:47
     */
    public function getStaff(bool $serialised = false): ArrayCollection
    {
        if ($this->isInjectMissing() && !key_exists('staff', $this->data)) $this->setStaff($this->createStaff());
        if ($serialised && key_exists('staff', $this->data)) {
            $staff = [];
            foreach ($this->data['staff'] as $item) $staff[] = $item->serialise();
            return new ArrayCollection($staff);
        }
        return $this->data['staff'] instanceof ArrayCollection ? $this->data['staff'] : new ArrayCollection($this->data['staff'] ?: []);
    }

    /**
     * setStaff
     * 15/12/2020 11:40
     * @param ArrayCollection $staff
     * @return TimetableDataManager
     */
    public function setStaff(ArrayCollection $staff): TimetableDataManager
    {
        $this->data['staff'] = $staff;
        return $this;
    }

    /**
     * createStaff
     * 15/12/2020 11:40
     * @param int $count
     * @return ArrayCollection
     */
    private function createStaff(int $count = 50): ArrayCollection
    {
        $staff = new ArrayCollection();
        $existing = $this->getStaff();
        for ($x=0; $x<$count; $x++) {
            if ($existing->containsKey($x)) {
                $member = $existing->get($x);
            } else {
                $member = new Staff();
                $member->setName('Staff Member ' . strval($x + 1));
            }
            $staff->set($x, $member);
        }
        return $staff;
    }

    /**
     * sortStaff
     * 15/12/2020 13:20
     * @return TimetableDataManager
     */
    public function sortStaff(): TimetableDataManager
    {
        try {
            $iterator = $this->getStaff()->getIterator();
        } catch (\Exception $e) {
            return $this;
        }

        $iterator->uasort(
            function (Staff $a, Staff $b) {
                return $a->getName() > $b->getName() ? 1 : -1 ;
            }
        );
        return $this->setStaff(new ArrayCollection(iterator_to_array($iterator, false)));
    }

    /**
     * getLines
     * @param bool $serialised
     * @return array
     * 11/12/2020 12:47
     */
    public function getLines(bool $serialised = false): array
    {
        if ($this->isInjectMissing() && !key_exists('lines', $this->data)) $this->setLines($this->createLines());
        if ($serialised && key_exists('lines', $this->data)) {
            $lines = [];
            foreach ($this->data['lines'] as $item) $lines[] = $item->serialise();
            return $lines;
        }
        return key_exists('lines', $this->data) ? $this->data['lines'] : [];
    }

    /**
     * Lines.
     *
     * @param array $lines
     * @return TimetableDataManager
     */
    public function setLines(array $lines): TimetableDataManager
    {
        $this->data['lines'] = $lines;
        return $this;
    }

    /**
     * createLines
     * @return array
     * 11/12/2020 10:17
     */
    private function createLines(): array
    {
        $lines = [];
        for ($x=1; $x<=26; $x++) {
            $member = new Line();
            $member->setName('Line ' . chr(64 + $x));
            $lines[] = $member;
        }
        return $lines;
    }

    /**
     * @return bool
     */
    public function isInjectMissing(): bool
    {
        return $this->injectMissing;
    }

    /**
     * InjectMissing.
     *
     * @param bool $injectMissing
     * @return TimetableDataManager
     */
    public function setInjectMissing(bool $injectMissing = true): TimetableDataManager
    {
        $this->injectMissing = $injectMissing;
        return $this;
    }

    /**
     * getCreatedAt
     * @return \DateTimeImmutable|null
     * 11/12/2020 12:58
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return key_exists('created_at', $this->data) ? new \DateTimeImmutable($this->data['created_at']) : null;
    }

    /**
     * deserialise
     * 11/12/2020 13:49
     */
    private function deserialise()
    {
        $staff = [];
        foreach ($this->getStaff() as $item) {
            if ($item instanceof Staff) $staff[] = $item;
            if (is_array($item)) {
                $x = new Staff();
                $staff[] = $x->deserialise($item);
            }
        }
        $this->setStaff(new ArrayCollection($staff));

        $grades = [];
        foreach ($this->getGrades() as $item) {
            if ($item instanceof Grade) $grades[] = $item;
            if (is_array($item)) {
                $x = new Grade();
                $grades[] = $x->deserialise($item);
            }
        }
        $this->setGrades(new ArrayCollection($grades));

        $rooms = [];
        foreach ($this->getRooms() as $item) {
            if ($item instanceof Room) $rooms[] = $item;
            if (is_array($item)) {
                $x = new Room();
                $rooms[] = $x->deserialise($item);
            }
        }
        $this->setRooms(new ArrayCollection($rooms));

        $days = [];
        foreach ($this->getDays() as $item) {
            if ($item instanceof Day) $days[] = $item;
            if (is_array($item)) {
                $x = new Day();
                $days[] = $x->deserialise($item);
            }
        }
        $this->setDays(new ArrayCollection($days));
    }

    /**
     * getStaffCount
     * 15/12/2020 11:37
     * @return int
     */
    public function getStaffCount(): int
    {
        return $this->staffCount = $this->getStaff()->count();
    }

    /**
     * setStaffCount
     * @param int $staffCount
     * 13/12/2020 09:22
     */
    public function setStaffCount(int $staffCount): TimetableDataManager
    {
        $this->staffCount = $staffCount;
        if ($staffCount > ($existing = $this->getStaffCount())) {
            $this->addMessage('warning', ['basic_settings.staff', ['changed' => 'additional', 'count' => abs($existing - $staffCount)]]);
        } elseif ($staffCount < ($existing = $this->getStaffCount())) {
            $this->addMessage('warning', ['basic_settings.staff', ['changed' => 'removed', 'count' => abs($staffCount - $existing)]]);
        }
        $this->setStaff($this->createStaff($staffCount));
        return $this;
    }

    /**
     * getMessages
     * @return array
     * 13/12/2020 15:47
     */
    public function getMessages(): array
    {
        return $this->messages ?: [];
    }

    /**
     * addMessage
     * @param string $type
     * @param $message
     * @return $this
     * 13/12/2020 15:50
     */
    public function addMessage(string $type, $message): TimetableDataManager
    {
        $message = ['message' => $message];
        $message['type'] = $type;
        $this->messages[] = $message;
        return $this;
    }

    /**
     * getRooms
     * 15/12/2020 13:43
     * @param bool $serialised
     * @return ArrayCollection
     */
    public function getRooms(bool $serialised = false): ArrayCollection
    {
        if ($this->isInjectMissing() && !key_exists('rooms', $this->data)) $this->setRooms($this->createRooms());
        if ($serialised && key_exists('rooms', $this->data)) {
            $rooms = [];
            foreach ($this->data['rooms'] as $item) $rooms[] = $item->serialise();
            return new ArrayCollection($rooms);
        }
        return $this->data['rooms'] instanceof ArrayCollection ? $this->data['rooms'] : new ArrayCollection($this->data['rooms'] ?: []);
    }

    /**
     * setRooms
     * 15/12/2020 13:38
     * @param ArrayCollection $rooms
     * @return TimetableDataManager
     */
    public function setRooms(ArrayCollection $rooms): TimetableDataManager
    {
        $this->data['rooms'] = $rooms;
        return $this;
    }

    /**
     * createRooms
     * 15/12/2020 13:43
     * @param int $count
     * @return ArrayCollection
     */
    private function createRooms(int $count = 30): ArrayCollection
    {
        $rooms = new ArrayCollection();
        $existing = $this->getRooms();
        for ($x=0; $x<$count; $x++) {
            if ($existing->containsKey($x)) {
                $member = $existing->get($x);
            } else {
                $member = new Room();
                $member->setName('Room ' . strval($x + 1));
            }
            $rooms->set($x, $member);
        }
        return $rooms;
    }

    /**
     * sortRooms
     * 15/12/2020 14:25
     * @return TimetableDataManager
     */
    public function sortRooms(): TimetableDataManager
    {
        try {
            $iterator = $this->getRooms()->getIterator();
        } catch (\Exception $e) {
            return $this;
        }

        $iterator->uasort(
            function (Room $a, Room $b) {
                return $a->getName() > $b->getName() ? 1 : -1 ;
            }
        );
        return $this->setRooms(new ArrayCollection(iterator_to_array($iterator, false)));
    }

    /**
     * removeRoom
     * 15/12/2020 14:44
     * @param string $name
     * @return TimetableDataManager
     */
    public function removeRoom(string $name): TimetableDataManager
    {
        $rooms = $this->getRooms();
        $delete = $rooms->filter(function($room) use ($name) {
            if ($name === $room->getName())
            return $room;
        });
        foreach ($delete as $room)
        {
            $key = $rooms->indexOf($room);
            if ($rooms->containsKey($key)) $rooms->remove($key);
        }
        return $this;
    }

    /**
     * getRoomCount
     * @return int
     * 14/12/2020 08:56
     */
    public function getRoomCount(): int
    {
        return $this->roomCount = $this->getRooms()->count();
    }

    /**
     * setRoomCount
     * @param int $roomCount
     * @return $this
     * 14/12/2020 08:56
     */
    public function setRoomCount(int $roomCount): TimetableDataManager
    {
        $this->roomCount = $roomCount;
        if ($roomCount > ($existing = $this->getRoomCount())) {
            $this->addMessage('warning', ['basic_settings.room', ['changed' => 'additional', 'count' => abs($existing - $roomCount)]]);
        } elseif ($roomCount < ($existing = $this->getRoomCount())) {
            $this->addMessage('warning', ['basic_settings.room', ['changed' => 'removed', 'count' => abs($roomCount - $existing)]]);
        }
        $this->setRooms($this->createRooms($roomCount));
        return $this;
    }

    /**
     * getGrades
     * 15/12/2020 08:23
     * @param bool $serialised
     * @return ArrayCollection
     */
    public function getGrades(bool $serialised = false): ArrayCollection
    {
        if ($this->isInjectMissing() && !key_exists('grades', $this->data)) $this->setGrades($this->createGrades());
        if ($serialised && key_exists('grades', $this->data)) {
            $grades = [];
            foreach ($this->data['grades'] as $item) $grades[] = $item->serialise();
            return new ArrayCollection($grades);
        }
        return $this->data['grades'] instanceof ArrayCollection ? $this->data['grades'] : new ArrayCollection($this->data['grades'] ?: []);
    }

    /**
     * setGrades
     * 15/12/2020 08:24
     * @param \Doctrine\Common\Collections\ArrayCollection $grades
     * @return $this
     */
    public function setGrades(ArrayCollection $grades): TimetableDataManager
    {
        $this->data['grades'] = $grades;
        return $this;
    }

    /**
     * createGrades
     * 15/12/2020 08:55
     * @param int $count
     * @return ArrayCollection
     */
    private function createGrades(int $count = 6): ArrayCollection
    {
        $grades = new ArrayCollection();
        $existing = $this->getGrades();
        for ($x=0; $x<$count; $x++) {
            if ($existing->containsKey($x)) {
                $member = $existing->get($x);
            } else {
                $member = new Grade();
                $member->setName('Grade ' . strval($x + 1));
            }
            $grades->set($x, $member);
        }
        return $grades;
    }

    /**
     * getGradeCount
     * 15/12/2020 08:56
     * @return int
     */
    public function getGradeCount(): int
    {
        return $this->gradeCount = $this->getGrades()->count();
    }

    /**
     * removeGrade
     * 15/12/2020 10:16
     * @param int $key
     * @return $this
     */
    public function removeGrade(int $key): TimetableDataManager
    {
        $grades = $this->getGrades();
        if ($grades->containsKey($key)) {
            $grades->remove($key);
        }
        return $this;
    }

    /**
     * setGradeCount
     * 15/12/2020 08:26
     * @param int $count
     * @return TimetableDataManager
     */
    public function setGradeCount(int $count): TimetableDataManager
    {
        $this->gradeCount = $count;
        if ($count > ($existing = $this->getGradeCount())) {
            $this->addMessage('warning', ['basic_settings.grade', ['changed' => 'additional', 'count' => abs($existing - $count)]]);
        } elseif ($count < ($existing = $this->getGradeCount())) {
            $this->addMessage('warning', ['basic_settings.grade', ['changed' => 'removed', 'count' => abs($count - $existing)]]);
        }
        $this->setGrades($this->createGrades($count));
        return $this;
    }

    /**
     * @param bool $serialised
     * @return ArrayCollection
     */
    public function getDays(bool $serialised = false): ArrayCollection
    {
        if ($this->isInjectMissing() && !key_exists('days', $this->data)) $this->setDays($this->createDays());
        if ($serialised && key_exists('days', $this->data)) {
            $days = [];
            foreach ($this->data['days'] as $item) $days[] = $item->serialise();
            return new ArrayCollection($days);
        }
        return $this->data['days'] instanceof ArrayCollection ? $this->data['days'] : new ArrayCollection($this->data['days']);
    }

    /**
     * setDays
     * 14/12/2020 15:11
     * @param ArrayCollection $days
     * @return $this
     */
    public function setDays(ArrayCollection $days): TimetableDataManager
    {
        $this->data['days'] = $days;
        return $this;
    }

    /**
     * createDays
     * 14/12/2020 15:45
     * @param int $count
     * @return ArrayCollection
     */
    private function createDays(int $count = 5): ArrayCollection
    {
        $days = new ArrayCollection();
        $existing = $this->getDays();
        for ($x=0; $x<$count; $x++) {
            if ($existing->containsKey($x)) {
                $member = $existing->get($x);
            } else {
                $member = new Day();
                $member->setName('Day ' . strval($x + 1));
            }
            $days->set($x, $member);
        }
        return $days;
    }

    /**
     * removeDay
     * 14/12/2020 15:55
     * @param int $key
     * @return TimetableDataManager
     */
    public function removeDay(int $key): TimetableDataManager
    {
        $days = $this->getDays();
        if ($days->containsKey($key)) {
            $days->remove($key);
        }
        return $this;
    }

    /**
     * getDayCount
     * @return int
     * 14/12/2020 10:19
     */
    public function getDayCount(): int
    {
        return $this->dayCount = count($this->getDays());
    }

    /**
     * setDayCount
     * @param int $dayCount
     * @return $this
     * 14/12/2020 10:19
     */
    public function setDayCount(int $dayCount): TimetableDataManager
    {
        $this->dayCount = $dayCount;
        if ($dayCount > ($existing = $this->getDayCount())) {
            $this->addMessage('warning', ['basic_settings.day', ['changed' => 'additional', 'count' => abs($existing - $dayCount)]]);
        } elseif ($dayCount < ($existing = $this->getDayCount())) {
            $this->addMessage('warning', ['basic_settings.day', ['changed' => 'removed', 'count' => abs($dayCount - $existing)]]);
        }
        $this->setDays($this->createDays($dayCount));
        return $this;
    }

    /**
     * getPeriods
     * @return int
     * 14/12/2020 10:41
     */
    public function getPeriods(): int
    {
        return $this->periods = isset($this->periods) ? $this->periods : (key_exists('periods', $this->data) ? $this->data['periods'] : 6) ;
    }

    /**
     * setPeriods
     * @param int $periods
     * @return TimetableDataManager
     * 14/12/2020 10:41
     */
    public function setPeriods(int $periods): TimetableDataManager
    {
        if ($this->getPeriods() !== $periods && $periods > 0) {
            $this->periods = $periods;
            foreach ($this->getDays() as $day) {
                $day->setPeriods($periods);
            }
            $this->addMessage('warning', ['basic_settings.periods', ['count' => $periods]]);
        }
        return $this;
    }

    /**
     * getStudentsPerGrade
     * 15/12/2020 10:23
     * @return int
     */
    public function getStudentsPerGrade(): int
    {
        return $this->studentsPerGrade = isset($this->studentsPerGrade) ? $this->studentsPerGrade : (key_exists('studentsPerGrade', $this->data) ? $this->data['studentsPerGrade'] : 0) ;
    }

    /**
     * setStudentsPerGrade
     * 15/12/2020 10:23
     * @param int $studentsPerGrade
     * @return TimetableDataManager
     */
    public function setStudentsPerGrade(int $studentsPerGrade): TimetableDataManager
    {
        if ($this->getStudentsPerGrade() !== $studentsPerGrade && $studentsPerGrade >= 0) {
            $this->studentsPerGrade = $studentsPerGrade;
            foreach ($this->getGrades() as $grade) {
                $grade->setStudentCount($studentsPerGrade);
            }
            $this->addMessage('warning', ['basic_settings.studentsPerGrade', ['count' => $studentsPerGrade]]);
        }
        return $this;
    }

    /**
     * getRoomCapacity
     * 15/12/2020 13:56
     * @return int
     */
    public function getRoomCapacity(): int
    {
        return $this->roomCapacity = isset($this->roomCapacity) ? $this->roomCapacity : (key_exists('roomCapacity', $this->data) ? $this->data['roomCapacity'] : 0) ;
    }

    /**
     * setRoomCapacity
     * 15/12/2020 13:56
     * @param int $roomCapacity
     * @return TimetableDataManager
     */
    public function setRoomCapacity(int $roomCapacity): TimetableDataManager
    {
        if ($this->getRoomCapacity() !== $roomCapacity && $roomCapacity >= 0) {
            $this->roomCapacity = $roomCapacity;
            foreach ($this->getRooms() as $room) {
                $room->setSize($roomCapacity);
            }
            $this->addMessage('warning', ['basic_settings.roomCapacity', ['count' => $roomCapacity]]);
        }
        return $this;
    }
}
