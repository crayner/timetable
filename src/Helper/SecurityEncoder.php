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
 * Date: 19/12/2020
 * Time: 08:12
 */
namespace App\Helper;

/**
 * Class SecurityEncoder
 * @package App\Helper
 * @author Craig Rayner <craig@craigrayner.com>
 * @author Elnur Abdurrakhimov <elnur@elnur.pro>
 * @author Terje Bråten <terje@braten.be>
 * @author Nicolas Grekas <p@tchwork.com>
 */
class SecurityEncoder
{
    const MAX_PASSWORD_LENGTH = 4096;

    /**
     * @var string
     */
    private string $algo;

    /**
     * @var array
     */
    private array $options;

    /**
     * @param string|null $algo An algorithm supported by password_hash() or null to use the stronger available algorithm
     */
    public function setAlgorithm(int $opsLimit = null, int $memLimit = null, int $cost = null, string $algo = null)
    {
        if (isset($this->algo) && isset($this->options)) return;
        $cost = $cost ?? 15;
        $opsLimit = $opsLimit ?? max(4, \defined('SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE') ? \SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE : 4);
        $memLimit = $memLimit ?? max(64 * 1024 * 1024, \defined('SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE') ? \SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE : 64 * 1024 * 1024);

        if (3 > $opsLimit) {
            throw new \InvalidArgumentException('$opsLimit must be 3 or greater.');
        }

        if (10 * 1024 > $memLimit) {
            throw new \InvalidArgumentException('$memLimit must be 10k or greater.');
        }

        if ($cost < 4 || 31 < $cost) {
            throw new \InvalidArgumentException('$cost must be in the range of 4-31.');
        }

        $algos = [1 => \PASSWORD_BCRYPT, '2y' => \PASSWORD_BCRYPT];

        if (\defined('PASSWORD_ARGON2I')) {
            $this->algo = $algos[2] = $algos['argon2i'] = (string) \PASSWORD_ARGON2I;
        }

        if (\defined('PASSWORD_ARGON2ID')) {
            $this->algo = $algos[3] = $algos['argon2id'] = (string) \PASSWORD_ARGON2ID;
        }

        if (null !== $algo) {
            $this->algo = $algos[$algo] ?? $algo;
        }

        $this->options = [
            'cost' => $cost,
            'time_cost' => $opsLimit,
            'memory_cost' => $memLimit >> 10,
            'threads' => 1,
        ];
    }

    /**
     * isPasswordValid
     * 19/12/2020 08:16
     * @param string $encoded
     * @param string $raw
     * @return bool
     */
    public function isPasswordValid(string $encoded, string $raw): bool
    {
        self::setAlgorithm();
        if ('' === $raw) {
            return false;
        }

        if (\strlen($raw) > self::MAX_PASSWORD_LENGTH) {
            return false;
        }

        if (0 !== strpos($encoded, '$argon')) {
            // BCrypt encodes only the first 72 chars
            return (72 >= \strlen($raw) || 0 !== strpos($encoded, '$2')) && password_verify($raw, $encoded);
        }

        if (\extension_loaded('sodium') && version_compare(\SODIUM_LIBRARY_VERSION, '1.0.14', '>=')) {
            return sodium_crypto_pwhash_str_verify($encoded, $raw);
        }

        if (\extension_loaded('libsodium') && version_compare(phpversion('libsodium'), '1.0.14', '>=')) {
            return \Sodium\crypto_pwhash_str_verify($encoded, $raw);
        }

        return password_verify($raw, $encoded);
    }


    /**
     * {@inheritdoc}
     */
    public function encodePassword(string $raw): string
    {
        self::setAlgorithm();
        if (\strlen($raw) > self::MAX_PASSWORD_LENGTH || ((string) \PASSWORD_BCRYPT === $this->algo && 72 < \strlen($raw))) {
            throw new \BadMethodCallException('Invalid password.');
        }

        // Ignore $salt, the auto-generated one is always the best

        return password_hash($raw, $this->algo, $this->options);
    }

    /**
     * {@inheritdoc}
     */
    public function needsRehash(string $encoded): bool
    {
        self::setAlgorithm();
        return password_needs_rehash($encoded, $this->algo, $this->options);
    }

    /**
     * getSelf
     * 7/01/2021 10:24
     * @return $this
     */
    public function getSelf(): SecurityEncoder
    {
        return $this;
    }
}
