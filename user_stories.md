# User Stories

## CLI

### Arguments

**Invalid**

1. As a User I want to use the App,
   but don’t enter any arguments after php console.php
   and I get a message informing me that valid arguments are: help, list, price, delete or add user.

2. As a User I want to use the App,
   but I enter 1st argument a string that is not help, list, price, delete or add user
   and I get a message informing me that valid arguments help, list, price, delete or add user.

### Help

**Valid**

1. As a User I want to get instructions how to use this app,
   so I write ‘help’ as a 1st argument
   and I get instructions about required parameters.

### List

**Valid**

1. As a User I want to view a list of all tokens I can choose from and my
   Favourite tokens list,
   so I write ‘list’ as 1st argument
   and I get a list of all tokens and a list of my Favourite tokens
   when prompted if I want to add token to Favourites list
   I choose 'n' as I don't want to add a token to Favourites list
   and no tokens are added to my Favourite tokens list.

2. As a User I want to add one or more tokens to my Favourites list,
   so I write ‘list’ as 1st argument
   when prompted if I want to add token to Favourites list
   I choose 'y' as I want to add a token to Favourites list
   when prompted I add a list of token indexes I wish to mark as favourite
   and I get confirmation message that the tokens were indeed marked as Favourites.

**Invalid**

1. As a User I want to add one or more tokens to my Favourites list,
   so I write ‘list’ as 1st argument
   when prompted if I want to add token to Favourites list
   I choose 'y' as I want to add a token to Favourites list
   but I add a token index that doesn't exist
   and I get message informing me that I entered a wrong number.

### Delete

**Valid**

1. As a User I want to remove one or more tokens from my Favourite tokens list,
   so I write ‘delete’ as 1st argument
   when prompted if I want to remove token from Favourites list
   I choose 'y' as to confirm that I want to delete a token from my Favourites list
   when prompted I add a list of tokens I wish to remove
   and I get confirmation message that the tokens were indeed unmarked as Favourites.

**Invalid**

1. As a User I want to remove one or more tokens from my Favourite tokens list,
   so I write ‘delete’ as 1st argument
   when prompted if I want to remove token from Favourites list
   I choose 'y' as to confirm that I want to delete a token from my Favourites list
   but I add one or more invalid token names that I wish to remove
   and I get message informing me that I entered a wrong token name and token is not removed.

### Currency Pair

**Valid**

1. As a User I want to get price for a currency pair,
   so I write ‘price’ as 1st argument and valid tokens as 2nd and 3rd argument
   and I get values returned in human readable form.

**Invalid**

1. As a User I want to get price for a currency pair,
   but I enter command with missing one or two token names
   and I get a message informing me that the argument cannot be null.

2. As a User I want to get price for a currency pair,
   but I enter less then 3 or more then 10 characters as 2nd or 3rd arg
   and I get message informing me wrong token length.

3. As a User I want to get price for a currency pair,
   but there is something wrong with the fetched data
   and I get message informing me about possible error of unsupported token pair, empty or invalid .json file.

4. As a User I want to get price for a currency pair,
   but I enter a token that is not in the fetched list
   and I get message informing me about invalid token.

5. As a User I want to get price for a currency pair,
   but I enter a 2nd or 3rd arg as integers
   and I get message informing me about invalid token.

6. As a User I want to get price for a currency pair,
   but I enter a 2nd or 3rd arg as lowercase strings
   and I get message informing me about invalid token.

### Add User

**Valid**

1. As an Admin I want to Register a new User,
   so I write ‘add user’ as 1st argument,
   when prompted to enter Username
   I enter valid Username
   when prompted to enter Password
   I enter valid Password
   and New User is registered and the data is stored in Database.

**Invalid**

1. As an Admin I want to Register a new User,
   so I write ‘add user’ as 1st argument,
   when prompted to enter Username
   I write a non-email address as Username,
   and I get a message informing me that Username must be an email.

2. As an Admin I want to Register a new User,
   so I write ‘add user’ as 1st argument,
   when prompted to enter Password
   I don't set a Password,
   and I get a message informing me that Password cannot be an empty string.

3. As an Admin I want to Register a new User,
   so I write ‘add user’ as 1st argument,
   when prompted to enter Username
   I enter an email that is already Registered
   and I get a message informing me that there is something wrong with the User credentials.

## WEB

### Login

**Valid**

1. As a User I want to log in,
   so I click on the Login button on the Home Screen
   and I am redirected to Login Page
   I use valid User credentials - registered Username and Password and click Login button
   and I am redirected to Home Page, where my username and Favourite tokens Section are displayed and Logout button is available.

**Invalid**

1. As a User I want to log in,
   but I use invalid User credentials - unregistered Username and Password and click Login button
   and I remain on Login Page and an error message informs me that the User credentials I entered are incorrect.

2. As a User I want to log in,
   but I use invalid User credentials - empty input field Username and Password and click Login button
   and I remain on Login Page and an error message informs me that the fields are required.

3. As a User I want to log in,
   but I use invalid User credentials - typo with Username and Password and click Login button
   and I remain on Login Page and an error message informs me that the User credentials I entered are incorrect.

**Edge Cases**

1. As a User I want to log in,
   but I use invalid User credentials - typo with username or password or unregistered user more then 3 times in 60s
   and I remain on Login Page and an error message informs me that there were to many failed login attempts and now I need to wait for 60s.

2. As a User I want to log in after more then 3 failed login attempts in 60s,
   but I use valid User credentials to login
   and I successfully login and I am redirected to Home Page, where my username, Favourite tokens Section are displayed and Logout button is available.

3. As a User I want to log in,
   so I use valid User credentials to login and logout more then 3 times in 60s
   and I can do so without being timed out.

### Logout

**_Precondition_**

As a User I am already logged in and I have Logout button available.

**Valid**

1. As a User I want to log out,
   so I click on the Logout button
   and I am logged out and redirected to the Home Page, my username and Favourite tokens section are not displayed anymore.

### Show Price

**_Precondition_**

User is not logged in, Favourite tokens Section is not displayed and there are no tokens marked as Favourite on the list of all tokens in the dropdown menu.

**Valid**

1. As a non-registered User I want to view token price,
   so I select tokens I want to compare from both dropdown menus, I click Show Price button
   and price is displayed in the format 1 {token 1} = current price {token 2}.

2. As a non-registered User I want to view another token price,
   so I select different tokens I want to compare from both dropdown menus, I click Show Price button
   and updated price is displayed in the format 1 {token 1} = current price {token 2}.

**Invalid**

1. As a non-registered User I want to view token price,
   but I don't select tokens from either or both dropdown menus, I click Show Price button
   and an error message informs me that both fields are required.

**_Precondition_**

User is logged in. Favourite tokens Section is displayed and there are tokens marked as Favourite on the top of the list of all tokens in the dropdown menu.

**Valid**

1. As a logged in User I want to view token price,
   so I select tokens I want to compare from both dropdown menus, I click Show Price button
   and price is displayed in the format 1 {token 1} = current price {token 2} along with Favourite token buttons.

2. As a logged in User I want to view another token price,
   so I select different tokens I want to compare from both dropdown menus, I click Show Price button
   and updated price is displayed in the format 1 {token 1} = current price {token 2} along with Favourite token buttons.

**Invalid**

1. As a logged in User I want to view token price,
   but I don't select tokens from either or both dropdown menus, I click Show Price button
   and an error message informs me that both fields are required.

### Mark as Favourite

**_Precondition_**

User is logged in. Favourite token buttons appear when the price is calculated. To mark token as Favourite, token should not be marked as Favourite previously.

**Valid**

1. As a logged in User I want to mark token as Favourite,
   so I click on Favourite token button
   and the button changes, token is displayed in Favourite tokens Section and marked as Favourite on the top of the list of all tokens in the dropdown menu.

### Remove as Favourite

**_Precondition_**

User is logged in. Favourite token buttons appear when the price is calculated. To remove token as Favourite, token should be marked as Favourite previously.

1. As a logged in User I want to remove token as Favourite,
   so I click on Favourite token button
   and the button changes, token is removed from Favourite tokens Section and from the top of the list of all tokens in the dropdown menu.

### User Accounts

1. As a User I log in with my User credentials
   and my favourite tokens are displayed in the Favourite tokens Section and at the top of the dropdown menu.
