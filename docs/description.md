# Social Hours

The goal of *Social Hours* is to make it easy for social organisations to credit volunteers for the hours of
labour they have personally donated.


### Implementation

*Social Hours* uses a distributed architecture based on the complementary currency system [groupcash](http://groupcash.org). 
Each *Coin* represents an amount of hours volunteered for a social organisation. Using encryption and digital signatures,
the *hours* are independent of a central database and therefore can be freely exchanged between compatible systems.


## Roadmap

The first version consists of a web application that provides the following use cases

### Register Organisation
A social Organisation registers with its name and the email address of the administrator for
authentication. A public/private key pair is generated for digitally signing credited *hours*.
The adminstrator is also Creditor of the Organisation.

### Add Member
Creditors can add Members to their Organisation by providing their name and email address. A public/private
key pair is generated to identify the Member.

### Authorize Creditor
The administrator can authorize a Member of the Organisation as *Creditor*. Creditors can credit *hours* to
Members.

### Print Member Card
A member card contains a code with the a URL that identifies the member and thus can be used to credit 
*social hours* to them. Members can print their own card and Creditors can print cards for any added Member.

### Credit Hours
Creditors can credit *hours* to voluteers.

### Check Balance
Members can see the history andof hours they have been credited.

### Show Credited Hours
Creditors can see how many hours an Organisation has credited to what member.

### Log In
Members and Creditors log-in by providing their email address to which a unique link is sent. The user can choose to stay 
logged-in indefinitely or until the browser window is closed.

### Log Out
Teminates the user session.


## Use Case Example

[tba]
