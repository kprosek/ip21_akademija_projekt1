# User Stories

## Valid

***

### 1 - Get help
As a User I want to get instructions how to use this app,
so I write ‘help’ as a 1st argument
and I get instructions about required parameters.

### 2 - Get list
As a User I want to get a list of crypto tokens I can choose from,
so I write ‘list’ as 1st argument
and I get a list of crypto tokens.

### 3 - Get currency pair
As a User I want to get information for a currency pair,
so I write ‘price’ as 1st argument, crypto token as 2nd and currency token as 3rd argument
and I get values returned in human readable form.


## Invalid

### 1 - No arguments
As a User I don’t enter any arguments
and I get a message “Wrong first argument - valid arguments: help, price, list”.

### 2 - 1st argument is not help, price or list
As a User I enter 1st argument that is not help, price or list
and I get a message “Wrong first argument - valid arguments: help, price, list”.

### 3 - Price: Missing one or more arguments
As a User I enter command with missing on or two arguments
and I get a message “Argument cannot be null”.

### 4 - Price: Incorrect crypto token value
As a User I enter less then 3 or more then 10 characters as 2nd arg
and I get message “Wrong crypto token length”.

### 5 - Price: Incorrect currency token value
As a User I enter less or more then 3 characters as 3rd arg
and I get message “Wrong currency token length”.

### 6 - Empty or invalid json for currencyPair, currenciesList or cryptoList
Something is wrong with the fetched data 
and I get message “Error message: Unsupported token pair, empty or invalid .json file for currencies or crypto token list”.

### 7 - Incorrect crypto or currency token
As a User I enter crypto or currency token that is not in the fetched list
and I get message “Invalid crypto or currency token”.


### 8 - Empty or invalid json for currencyPair
Something is wrong with the fetched data 
and I get message “Error message: Unsupported token pair, empty or invalid .json file for currencies pair”.

## Edge cases

### 1 - Price: Wrong order of tokens
As a User I enter switched order of crypto and currency tokens
and I get message “Invalid crypto or currency token”.

### 2 - Price: Huge number of characters
As a User I enter a 2nd or 3rd arg as 100+ characters
and I get message “Wrong crypto token length”.

### 3 - Price: 2nd or 3rd arg integers instead of char
As a User I enter a 2nd or 3rd arg as integers
and I get message “Invalid crypto or currency token”

### 4 - Price: 2nd or 3rd arg string
As a User I enter a 2nd or 3rd arg as ‘USD’ and ‘BTC’
and I get values returned in human readable form.

### 5 - Price: 2nd or 3rd arg lowercase
As a User I enter a 2nd or 3rd arg with lowercase
and I get message “ Invalid crypto or currency token.”

### 6 - Help + 2nd and 3rd arg
As a User I enter ‘help’ as 1st argument and tokens as 2nd or 3rd arg
and I get instructions about required parameters.

### 7 - List + 2nd and 3rd arg
As a User I enter ‘help’ as 1st argument and tokens as 2nd or 3rd arg
and I get a list of crypto tokens.