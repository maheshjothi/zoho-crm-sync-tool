<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use zcrmsdk\crm\setup\restclient\ZCRMRestClient;
use zcrmsdk\crm\exception\ZCRMException;
use zcrmsdk\oauth\ZohoOAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class SyncZohoAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:zoho:accounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $configuration=array("client_id"=>"1000.IBS4UA24VZR646747YM3MJXM1FFJ0H", //client id generated using the URL https://accounts.zoho.com/developerconsole
            "client_secret"=>"daa9a43587c85cd194d339eb5868234bba777cdc79",//client secret generated using the URL https://accounts.zoho.com/developerconsole
            "redirect_uri"=>"http://127.0.0.1:8000/", // redirect uri mentioned the URL https://accounts.zoho.com/developerconsole
            "currentUserEmail"=>"maheshjothi@gmail.com" // user email id
        ); 
        ZCRMRestClient::initialize($configuration);
        $oAuthClient = ZohoOAuth::getClientInstance();
        $userIdentifier = "maheshjothi@gmail.com";
        $refreshToken = "1000.92f3a00dbdf25eb12ffce66d44d0b9d5.388d992ee232c6faba98cdd065f20973";
        $oAuthClient->generateAccessTokenFromRefreshToken($refreshToken,$userIdentifier);
        $zcrmModuleIns = ZCRMRestClient::getInstance()->getModuleInstance("Accounts");
        $response=$zcrmModuleIns->getRecords();
        $records=$response->getData();
        try {
            foreach($records as $record)
            {
                $entityId = $record->getEntityId();
                $phoneNumber = $record->getFieldValue("Phone"); // To get phone number
                $website = $record->getFieldValue("Website"); // To get website
                $accountName = $record->getFieldValue("Account_Name"); // To get account name
                $description = $record->getFieldValue("Description"); // To get account description
                $industry = $record->getFieldValue("Industry"); // To get industry
                $owner=$record->getOwner(); // To get the owner details
                $ownerName = $owner->getName();  //To get record owner name
                $this->recordsInsertOrUpdate($entityId, $phoneNumber, $website, $accountName, $description, $industry, $ownerName);
            }
            echo "Accounts Successfully Imported From Zoho CRM!";
        } catch (ZCRMException $ex) {
            echo $ex->getExceptionDetails();  //To get ZCRMException error details
            echo $ex->getExceptionCode();  //To get ZCRMException error code
        }
    }

    // duplicate check of the records before putting entry into table
    public function recordsInsertOrUpdate($entityId, $phoneNumber, $website, $accountName, $description, $industry, $ownerName) {
        DB::beginTransaction();
        try {
            DB::table('accounts')->updateOrInsert(
                ['entity_id' => $entityId],
                ['account_name' => $accountName,
                'account_description' => $description,
                'account_industry' => $industry,
                'website' => $website,
                'phone_number' => $phoneNumber,
                'account_owner' => $ownerName,
                ]
            );
        } catch(QueryException $e) {
            DB::rollback();
            echo $e->getMessage();
            exit;
        }
    }
}
