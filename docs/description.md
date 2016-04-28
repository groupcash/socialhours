# Social Hours

The goal of *Social Hours* is to make it easy for social organisations to credit volunteers for the hours of
labour they have personally donated.


### Implementation

*Social Hours* uses a distributed architecture based on the complementary currency system [groupcash](http://groupcash.org). 
Each *coin* represents an amount of hours volunteered for a social organisation. Using encryption and digital signatures,
the *hours* are independent of a central database and therefore can be freely exchanged between compatible systems.


## Capabilities

The first version consists of a web application that provides the following capabilities

### Create Account
An account is created by providing an email address for authentication and optionally a name. A public/private key pair is generated to identify the account.

### Register Organisation
A social Organisation registers with its name and the email address of the administrator for authentication. A public/private key pair is generated for digital signatures.

### Authorize Creditor
The administrator of an organisation can authorize an account to be able to credit *social hours* in the name of the organisation.

### Encode Account
An account address can be visually encoded to easily identify it on the field.

### Credit Hours
Creditors can credit a number of *social hours* to voluteers by providing a description of the work done and the account address of the volunteer (by scanning the visual code) or the name/email address the account was created with.

### Check Balance
Account holders can see the history and current balance of *social hours* credited to them.

### Check Credited Hours
Creditors can see how many *social hours* an organisation has credited to which accounts.

### Log In
Users of the application (administrator, creditors, accounts) can log-in by providing their email address to which a unique link is sent. The user can choose to stay logged-in indefinitely or until the browser window is closed.

### Log Out
Teminates the user session.


## Use Case Example

[tba]
