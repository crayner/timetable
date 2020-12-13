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

use App\Items\Line;
use App\Items\Staff;
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
     * 11/12/2020 13:40
     */
    public function getSerializedData(): array
    {
        return [
            'name' => $this->getName(),
            'created_at' => $this->getCreatedAt()->format('c'),
            'staff' => $this->getStaff(true),
            'lines' => $this->getLines(true),
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
        $this->setLines($this->createLines());

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
    public function getStaff(bool $serialised = false): array
    {
        if ($this->isInjectMissing() && !key_exists('staff', $this->data)) $this->setStaff($this->createStaff());
        if ($serialised && key_exists('staff', $this->data)) {
            $staff = [];
            foreach ($this->data['staff'] as $item) $staff[] = $item->serialise();
            return $staff;
        }
        return key_exists('staff', $this->data) ? $this->data['staff'] : [];
    }

    /**
     * Staff.
     *
     * @param array $staff
     * @return TimetableDataManager
     */
    public function setStaff(array $staff): TimetableDataManager
    {
        $this->data['staff'] = $staff;
        return $this;
    }

    /**
     * createStaff
     * @param int $count
     * @return array
     * 11/12/2020 09:51
     */
    private function createStaff(int $count = 50): array
    {
        $staff = [];
        $existing = $this->getStaff();
        for ($x=0; $x<$count; $x++) {
            $member = key_exists($x, $existing) ? $existing[$x] : new Staff();
            $member->setName('Staff Member ' . strval($x + 1));
            $staff[$x] = $member;
        }
        return $staff;
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
        $this->setStaff($staff);

        $lines = [];
        foreach ($this->getLines() as $item) {
            if ($item instanceof Line) $lines[] = $item;
            if (is_array($item)) {
                $x = new Line();
                $lines[] = $x->deserialise($item);
            }
        }
        $this->setLines($lines);
    }

    /**
     * getStaffCount
     * @return int
     * 13/12/2020 09:22
     */
    public function getStaffCount(): int
    {
        return $this->staffCount = count($this->getStaff());
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
}
