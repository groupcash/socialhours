<?php
namespace groupcash\socialhours\model;

use groupcash\php\Groupcash;
use groupcash\php\model\signing\Binary;
use groupcash\socialhours\AuthorizeCreditor;
use groupcash\socialhours\CreateAccount;
use groupcash\socialhours\CreditHours;
use groupcash\socialhours\events\AccountCreated;
use groupcash\socialhours\events\CreditorAuthorized;
use groupcash\socialhours\events\HoursCredited;
use groupcash\socialhours\events\OrganisationRegistered;
use groupcash\socialhours\events\TokenDestroyed;
use groupcash\socialhours\events\TokenGenerated;
use groupcash\socialhours\LogIn;
use groupcash\socialhours\LogOut;
use groupcash\socialhours\RegisterOrganisation;

class SocialHours {

    /** @var Groupcash */
    private $groupcash;
    /** @var Binary[] indexed by emails */
    private $accounts = [];
    /** @var Binary[] indexed by names */
    private $organisations = [];
    /** @var Binary[] indexed by tokens */
    private $activeTokens = [];
    /** @var Binary[][] grouped by organisation address */
    private $creditors = [];

    public function __construct(Groupcash $groupcash) {
        $this->groupcash = $groupcash;
    }

    public static function tokenFromBinary(Binary $binary) {
        return new Token(substr(str_replace(['=', '/', '+'], '', (string)$binary), -16));
    }

    public function handleCreateAccount(CreateAccount $c) {
        $this->guardUniqueEmail($c->getEmail());

        $key = $this->groupcash->generateKey();
        $address = $this->groupcash->getAddress($key);

        return new AccountCreated(
            Time::now(), $address, $key, $c->getEmail(), $c->getName()
        );
    }

    public function applyAccountCreated(AccountCreated $e) {
        $this->accounts[$e->getEmail()] = $e->getAddress();
    }

    public function handleRegisterOrganisation(RegisterOrganisation $c) {
        $this->guardUniqueEmail($c->getAdminEmail());
        $this->guardUniqueName($c->getName());

        $key = $this->groupcash->generateKey();
        $address = $this->groupcash->getAddress($key);

        return new OrganisationRegistered(
            Time::now(), $address, $key, $c->getAdminEmail(), $c->getName()
        );
    }

    public function applyOrganisationRegistered(OrganisationRegistered $e) {
        $this->accounts[$e->getAdminEmail()] = $e->getAddress();
        $this->organisations[$e->getName()] = $e->getAddress();
        $this->creditors[(string)$e->getAddress()][] = $e->getAddress();
    }

    private function guardUniqueEmail($email) {
        if (isset($this->accounts[$email])) {
            throw new \Exception('An account with this email address already exists.');
        }
    }

    private function guardUniqueName($name) {
        if (in_array(strtolower($name), array_map('strtolower', array_keys($this->organisations)))) {
            throw new \Exception('An organisation with this name is already registered.');
        }
    }

    public function handleLogIn(LogIn $c) {
        if (!isset($this->accounts[$c->getEmail()])) {
            throw new \Exception('No account with this email address exists.');
        }

        return new TokenGenerated(
            Time::now(),
            self::tokenFromBinary($this->groupcash->generateKey()),
            $this->accounts[$c->getEmail()],
            $c->getEmail()
        );
    }

    public function applyTokenGenerated(TokenGenerated $e) {
        $this->activeTokens[(string)$e->getToken()] = $e->getAddress();
    }

    public function handleLogOut(LogOut $c) {
        $this->guardValidToken($c->getToken());

        return new TokenDestroyed(
            Time::now(),
            $c->getToken()
        );
    }

    public function applyTokenDestroyed(TokenDestroyed $e) {
        unset($this->activeTokens[(string)$e->getToken()]);
    }

    public function handleAuthorizeCreditor(AuthorizeCreditor $c) {
        $organisation = $this->guardValidToken($c->getToken());

        if (!in_array($organisation, $this->organisations)) {
            throw new \Exception('Only administrators of organisations can authorize creditors.');
        }

        $this->guardExistingAccount($c->getCreditor());
        $this->guardNotAdministrator($c->getCreditor());

        return new CreditorAuthorized(
            Time::now(),
            $organisation,
            $this->getAddressOfAccount($c->getCreditor())
        );
    }

    public function applyCreditorAuthorized(CreditorAuthorized $e) {
        $this->creditors[(string)$e->getOrganisation()][] = $e->getCreditor();
    }

    private function guardValidToken(Token $token) {
        if (!isset($this->activeTokens[(string)$token])) {
            throw new \Exception('Invalid token.');
        }

        return $this->activeTokens[(string)$token];
    }

    public function handleCreditHours(CreditHours $c) {
        $creditor = $this->guardValidToken($c->getToken());

        $this->guardCanCreditHours($creditor, $c->getOrganisation());

        $events = [];
        foreach ($c->getVolunteers() as $volunteer) {
            $this->guardExistingAccount($volunteer);
            $this->guardIsNotSelf($creditor, $volunteer);

            $events[] = new HoursCredited(
                Time::now(),
                $creditor,
                $this->getAddressOfOrganisation($c->getOrganisation()),
                $this->getAddressOfAccount($volunteer),
                $c->getDescription(),
                $c->getMinutes()
            );
        }
        return $events;
    }

    private function guardCanCreditHours($creditor, $organisation) {
        if (!$this->canCreditHours($creditor, $organisation)) {
            throw new \Exception('Only creditors and administrators can credit hours.');
        }
    }

    private function guardIsNotSelf(Binary $creditor, AccountIdentifier $volunteer) {
        if ($creditor == $this->getAddressOfAccount($volunteer)) {
            throw new \Exception('Creditors cannot credit hours to themselves.');
        }
    }

    private function canCreditHours(Binary $address, OrganisationIdentifier $organisation) {
        $organisationAddress = (string)$this->getAddressOfOrganisation($organisation);
        return isset($this->creditors[$organisationAddress]) && in_array($address, $this->creditors[$organisationAddress]);
    }

    private function getAddressOfAccount(AccountIdentifier $account) {
        return $this->accounts[$account->getEmail()];
    }

    private function getAddressOfOrganisation(OrganisationIdentifier $organisation) {
        return $this->organisations[$organisation->getName()];
    }

    private function guardExistingAccount(AccountIdentifier $account) {
        if (!isset($this->accounts[$account->getEmail()])) {
            throw new \Exception('No account was created with this email address.');
        }
    }

    private function guardNotAdministrator(AccountIdentifier $accountIdentifier) {
        if (in_array($this->getAddressOfAccount($accountIdentifier), $this->organisations)) {
            throw new \Exception('Cannot authorize administrators as creditors.');
        }
    }
}