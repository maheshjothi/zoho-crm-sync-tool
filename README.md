# zoho-crm-sync-tool
Sync the zoho accounts with the local mysql database  

If required, please have a look into https://github.com/zoho/zcrm-php-sdk on Readme.md file as a reference for creating the clientID, client secret, grant/refresh token. This code has been followed from some of the steps from the same.  

1.For the kind reference, for generating the grant token, we need to use the scope values like aaaserver.profile.READ,ZohoCRM.users.ALL,ZohoCRM.modules.ALL,ZohoCRM.settings.all in the https://accounts.zoho.com/developerconsole and its valid for a single call only.  

2. As already i have used refresh token for my account details in the code, Just in case if new refresh token required for different account please follow the above step 1 and step 2 For generating the refresh token from grant token, we need to have a back up of grant token that has been generatedalready and using postman we need to hit the URL https://accounts.zoho.com/oauth/v2/token?code={grant_token}&redirect_uri={redirect_uri}&client_id={client_id}&client_secret={client_secret}&grant_type=authorization_code with POST request by sending the params in Body Content. So that we will get json resonse like below  
{
    "access_token": "1000.8a23ea443b6a993e94c470ec4b93ccb3.97ea27ff02cd4e71893ee0c76a3cf087",
    "refresh_token": "1000.92f3a00dbdf25eb12ffce66d44d0b9d5.388d992ee232c6faba98cdd065f20973",
    "expires_in_sec": 3600,
    "api_domain": "https://www.zohoapis.com",
    "token_type": "Bearer",
    "expires_in": 3600000
}  

3. We need to use the refresh token in our code to get the API results.  

Configuration Set Up In Local Environment
-----------------------------------------

1.create a DB named zoho-crm-tool and zohooauth  

2.open the command prompt and run the command "php artisan migrate"  

3.As i have created custom artisan command "sync:zoho:accounts", i have used "php artisan make:command sync:zoho:accounts". So that we can able to create any type of custom artisan commands using the same.  

