<?php


namespace Neamil\ProtectFeLogin\Domain\Repository;

use Neamil\DeviceCookies\FailedAttemptRepositoryInterface;
use Neamil\DeviceCookies\Models\FailedAttempt;
use Neamil\DeviceCookies\Models\User;
use PDO;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FailedAttemptRepository implements FailedAttemptRepositoryInterface
{

    protected const TABLE = 'tx_securefelogin_failed_attempts';

    /**
     * @param FailedAttempt $failedAttempt
     */
    public function storeFailedAttempt(FailedAttempt $failedAttempt): void
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::TABLE);
        $queryBuilder->insert(self::TABLE)
            ->values([
                    'user_id' => $failedAttempt->getUser(),
                    'tstamp' => $failedAttempt->getTime(),
                    'device_cookie' =>$failedAttempt->getCookie()
                ]
            )
            ->execute();
    }

    public function countFailedAttemptsOfDeviceCookie($deviceCookie, $getTimePeriod): int
    {
        $periodStart = time() - $getTimePeriod;

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::TABLE);
        return $queryBuilder->count('attempt_id')
            ->from(self::TABLE)
            ->where(
                $queryBuilder->expr()->eq('device_cookie', $queryBuilder->createNamedParameter($deviceCookie, PDO::PARAM_STR))
            )
            ->andWhere(
                $queryBuilder->expr()->gte('tstamp', $periodStart)
            )
            ->execute()
            ->fetchColumn(0);
    }

    public function countFailedAttemptsOfUntrustedClientsForUser(User $cookieUser, $getTimePeriod): int
    {
        $periodStart = time() - $getTimePeriod;

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::TABLE);
        return $queryBuilder->count('attempt_id')
            ->from(self::TABLE)
            ->where(
                $queryBuilder->expr()->eq('device_cookie', "''")
            )
            ->andWhere(
                $queryBuilder->expr()->eq('user_id', $cookieUser->getId())
            )
            ->andWhere(
                $queryBuilder->expr()->gte('tstamp', $periodStart)
            )
            ->execute()
            ->fetchColumn(0);
    }
}
