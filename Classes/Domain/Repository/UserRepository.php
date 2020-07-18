<?php


namespace  Neamil\ProtectFeLogin\Domain\Repository;


use Neamil\DeviceCookies\Models\User;
use Neamil\DeviceCookies\UserRepositoryInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class UserRepository implements UserRepositoryInterface
{
    protected const TABLE = 'tx_protectfelogin_user';

    public function createUserByName(string $username): User
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::TABLE);
        $result = $queryBuilder->insert(self::TABLE)
            ->values([
                    'user_name' => $username,
                ]
            )
            ->execute();

        return $this->getUserByName($username);

    }
    /**
     * @param string $name
     * @return User|null
     */
    public function getUserByName(string $name): ?User
    {
        $user = null;
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::TABLE);
        $users = $queryBuilder->select('user_id', 'user_name', 'lockout_untrusted_clients_until')
            ->from(self::TABLE)
            ->where(
                $queryBuilder->expr()->eq('user_name', $queryBuilder->createNamedParameter($name))
            )
            ->execute()
            ->fetchAll();
        foreach ($users as $userData){
            if($userData['user_id']){
                $user = new User($userData['user_name']);
                $user->setId($userData['user_id']);
                if(empty($userData['lockout_untrusted_clients_until'])){
                    $userData['lockout_untrusted_clients_until'] = 0;
                }
                $user->setUntrustedClientsAreLockedOutUntil($userData['lockout_untrusted_clients_until']);
            }

        }

        return $user;
    }

    public function updateUser(User $cookieUser):void
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::TABLE);
        $queryBuilder->update(self::TABLE)
            ->where(
                $queryBuilder->expr()->eq('user_id', $cookieUser->getId())
            )
            ->set('user_name' , $cookieUser->getName())
            ->set('lockout_untrusted_clients_until' ,$cookieUser->getUntrustedClientsAreLockedOutUntil())
            ->execute();

    }

}
