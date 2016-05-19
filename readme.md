# Social Hours [![Build Status](https://travis-ci.org/groupcash/socialhours.png?branch=master)](https://travis-ci.org/groupcash/socialhours)

The goal of *Social Hours* is to make it easy for social organisations to credit volunteers for the hours of
labour they have donated.


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

### Credit Hours
Creditors can credit a number of *social hours* to volunteers by providing a description of the work done and the name or email address the account was created with.

### Check Balance
Account holders can see the history and current balance of *social hours* credited to them.

### Check Credited Hours
Creditors can see how many *social hours* an organisation has credited to which accounts.

### Log In
Users of the application (administrator, creditors, accounts) can log-in by providing their email address to which a unique link is sent. The user can choose to stay logged-in indefinitely or until the browser window is closed.

### Log Out
Teminates the user session.


## Use Case Example

**Dorji** and **Sonam** just founded a social organisation to support tree planting in their community. They install *Social Hours* on <http://hours.greenerthimphu.bt>.

As a first step they choose *Register Organisation*, enter `Greener Thimphu` as the name and `dorji@greenerthimphu.bt` as the email address of the administrator. After registration, **Dorji** receives an email containing a link to log-in.

On her computer, **Sonam** selects *Create Account* and enters her email address `sonam@greenerthimphu.bt` to which a log-in link is sent as well.

After logging-in, **Dorji** can grant **Sonam** the right to credit hours by selecting *Authorize Creditor* and selecting **Sonam's** name or email address.

**Karma** hears about "Greener Thimphu" and wants to volunteer at the tree planting day they are organising, so she goes to <http://hours.greenerthimphu.bt> and selects *Create Account*.

**Karma** brings her friend **Sangay** along with her to the tree planting day. After four hours of planting trees, they go to **Sonam** and ask her for four *social hours*. **Sonam** logs into the application, selects *Credit Hours*, enters `4` hours as well as `Tree Planting` as the description and selects **Karma's** email address. Since **Sangay** does not have an account yet, **Karma** can create one for her on the spot and credits four *social hours* to her as well.

Both are notified about the credited *social hours* via email and go home with the good feeling of having done something useful.
