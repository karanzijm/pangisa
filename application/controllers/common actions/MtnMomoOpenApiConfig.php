<?php
/**
 * Created by IntelliJ IDEA.
 * User: Akankwasa Brian
 * Date: 10/18/2019
 * Time: 6:43 PM
 */

class MtnMomoOpenApiConfig
{
    private $requestToPayUrl = "https://ericssonbasicapi2.azure-api.net/collection/v1_0/requesttopay";
    private $requestToPayUrlProduction = "https://ericssonbasicapi1.azure-api.net/collection/v1_0/requesttopay";

    //api user
    private $CreateApiUserUrl = "https://ericssonbasicapi2.azure-api.net/v1_0/apiuser";
    private $CreateApiUserUrlProduction = "https://ericssonbasicapi1.azure-api.net/v1_0/apiuser";

    //api key
    private $CreateAPiKeyUrl = "https://ericssonbasicapi2.azure-api.net/v1_0/apiuser/67251c89-0e98-4843-b45f-60ec3caae7e6/apikey";
    private $CreateAPiKeyUrlProduction = "https://ericssonbasicapi1.azure-api.net/v1_0/apiuser/67251c89-0e98-4843-b45f-60ec3caae7e6/apikey";

    private $accesstokenUrl = "https://ericssonbasicapi2.azure-api.net/collection/token/";
    private $accesstokenUrlProduction = "https://ericssonbasicapi1.azure-api.net/collection/token/";


    private $getaccountbalanceurlgetUrl = "https://ericssonbasicapi2.azure-api.net/collection/v1_0/account/balance";
    private $getaccountbalanceurlgetUrlProduction = "https://ericssonbasicapi1.azure-api.net/collection/v1_0/account/balance";

    private $OcpApimSubscriptionKey = '7a142fad542646edb7276845c18e1494';
    private $OcpApimSubscriptionKeyProduction = 'e7ac1adae673408a9aa38fe9d92be9df';

    private $OcpApimSubscriptionKeyCollections = '7a142fad542646edb7276845c18e1494';
    private $OcpApimSubscriptionKeyCollectionsProduction = '5f46ed89029d4eab94e163d10cab17cc';

    private $OcpApimSubscriptionKeyDisbursements = '7a142fad542646edb7276845c18e1494';
    private $OcpApimSubscriptionKeyDisbursementsProduction = 'e7ac1adae673408a9aa38fe9d92be9df';

    private $accessToken = null;
    private $apiKey = "afc62804a31e4c148c2068a6dd36cc6a";

    //production
    private $apiKeyCollectionProduction = "1053a74512cd473f93975f984e73c217";
    private $apiKeyDisbursementsProduction = "afc62804a31e4c148c2068a6dd36cc6a";

    private $apiuserProduction = "2773d68e-001e-4739-ac28-e9d0b344e93c";
    private $apiuser = "2773d68e-001e-4739-ac28-e9d0b344e93c";

    //use these only in production
    private $collections_user_name = 'PANGI2.sp5';
    private $apiuserForCollectionProduction = '43bb6534-72d5-4fc4-9658-7143eab6d60c';
    private $apiuserForDisbursementsProduction = 'PANGI3.sp5';
    private $apiuserForLiquidationProduction = 'Pangisa';

    //collections api key
    private $collectionsApiKeyProduction = '';

    private $is_live_service = true;
    private $target_environment = 'mtnuganda';
    private $currency = 'EUR';

    //production details collections
    //Primary key aaa985675d5247bdb8ce12e9487b27ce
    //Secondary key 0b1516f90ff844eba05055c684dcacb2

    //deposits/disbursements
    //Primary key e7ac1adae673408a9aa38fe9d92be9df
    //Secondary key 4eb90ee7deee489dab273696523055e9

    //Balllistics helper
    private $helper = null;


    //collections -- same as debit subscriber account request to pay
    //Primary key 5f46ed89029d4eab94e163d10cab17cc
    //Secondary key 819d898335ca4a9e967be24141a90193

    //sptransfer -- move money from your account to another -- disbursements
    //Primary key53abb869144a4dc2a5d64385984bcc8d
    //Secondary key138a26c106fa461194f802fad37cc0e5

    //remittt money from out to local
    //Primary key92023f1900d24b69a5fec40720b06e04
    //Secondary keyaf656c70f1f541e5952fa15a0fdc7301
}
