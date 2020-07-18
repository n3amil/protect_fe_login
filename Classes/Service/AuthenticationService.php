<?php


namespace Neamil\ProtectFeLogin\Service;

use Exception;
use TYPO3\CMS\Core\Authentication\AuthenticationService as BaseAuthenticationService;

use Neamil\DeviceCookies\Models\Settings;
use Neamil\DeviceCookies\Models\User;
use Neamil\DeviceCookies\Service\CookieService;
use Neamil\DeviceCookies\Service\RequestHandlingService;
use Neamil\DeviceCookies\Service\FailedAuthenticationService;
use Neamil\SecureFeLogin\Domain\Repository\FailedAttemptRepository;
use Neamil\SecureFeLogin\Domain\Repository\LockedOutDeviceCookieRepository;
use Neamil\SecureFeLogin\Domain\Repository\UserRepository;
use Psr\Log\LogLevel;
use TYPO3\CMS\Core\Crypto\PasswordHashing\InvalidPasswordHashException;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class AuthenticationService extends BaseAuthenticationService
{

    /**
     * @param array $userData
     * @return int
     * @throws InvalidPasswordHashException
     */
    public function authUser(array $userData): int
    {
        $status = 100;
        $cookieUser = false;
        $username = (string)$this->login['uname'];
        $settings = new Settings(
            $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['secure_fe_login']['timeout'],
            $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['secure_fe_login']['maxAttempts'],
            $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['secure_fe_login']['deviceCookieName'],
            $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['secure_fe_login']['deviceCookieExpireInDays'],
            $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['secure_fe_login']['secret']
        );

        $userRepository = new UserRepository();
        $failedAttemptsRepository = new FailedAttemptRepository();
        $lockedOutDeviceCookieRepository = new LockedOutDeviceCookieRepository();
        $logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);

        if (!empty($username)) {
            $cookieUser = $userRepository->getUserByName($username);
        }

        if (!($cookieUser instanceof User) && !empty($username)) {
            $cookieUser = $userRepository->createUserByName($username);
        }

        if ($cookieUser instanceof User) {
            $rejectAuthenticationAttempt = RequestHandlingService::handleAuthenticationRequest(
                $settings,
                $cookieUser,
                $lockedOutDeviceCookieRepository
            );
            if ($rejectAuthenticationAttempt === false) {
                $status = parent::authUser($userData);
                if ($status === 200) {
                    try {
                        CookieService::issueNewDeviceCookieToUserClient($cookieUser, $settings);
                    } catch (Exception $e) {
                        $logger->log(LogLevel::ERROR, $e->getMessage());
                    }
                } elseif ($status <= 0) {
                    FailedAuthenticationService::registerFailedAuthenticationAttempt(
                        $cookieUser,
                        $failedAttemptsRepository,
                        $settings,
                        $lockedOutDeviceCookieRepository,
                        $userRepository
                    );
                }
            }
        }

        return $status;
    }
}
