<?php


namespace  Neamil\SecureFeLogin\Domain\Repository;;


use Neamil\DeviceCookies\LockedOutDeviceCookieRepositoryInterface;
use Neamil\DeviceCookies\Models\LockedOutDeviceCookie;
use PDO;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class LockedOutDeviceCookieRepository implements LockedOutDeviceCookieRepositoryInterface
{
    protected const TABLE = 'tx_securefelogin_cookie_lockout';


    public function isCookieLockedOut($cookie): bool
    {
        $lockedOut = true;
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::TABLE);
        $count = $queryBuilder->count('lockout_id')
            ->from(self::TABLE)
            ->where(
                $queryBuilder->expr()->eq('device_cookie', $queryBuilder->createNamedParameter($cookie, PDO::PARAM_STR))
            )
            ->andWhere(
                $queryBuilder->expr()->gt('lockout_until', time())
            )
            ->execute()
            ->fetchColumn(0);
        if ($count < 1) {
            $lockedOut = false;
        }
        return $lockedOut;

    }

    /**
     * @param LockedOutDeviceCookie $cookie
     * @return void
     */
    public function storeLockedOutCookie(LockedOutDeviceCookie $cookie): void
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::TABLE);
        $queryBuilder->insert(self::TABLE)
            ->values([
                    'lockout_until' => $cookie->getLockedUntil(),
                    'device_cookie' => $cookie->getCookie(),
                ]
            )
            ->execute();
    }
}
