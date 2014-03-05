<?php
/*
    protx vsp form payment module by Chris Bond <chris@logics.co.uk>

    If you want to test this payment provider use the Vendor ID of "testvendor"
    and Crypt Password of "testvendor".

    Test Credit cards are as follows these will all produce a valid transaction.

    VISA        4929 0000 0000 6    No Issue No Any Expiry Date
    MasterCard  5404 0000 0000 0001 No Issue No Any Expiry Date
    DELTA       4462 0000 0000 0001 No Issue No Any Expiry Date
    Switch      5641 8200 0000 0005 Issue No 1  Any Expiry Date
    Solo        6334 9000 0000 0005 Issue No 1  Any Expiry Date
    Amercian Expres 3742 0000 0000 004  No Applicable   Any Expiry Date
*/
include("functions.php");
require_once 'modules/admin/models/GatewayPlugin.php';

/**
* @package Plugins
*/
class PluginProtxform extends GatewayPlugin
{
    function getVariables()
    {
        /* Specification
              itemkey     - used to identify variable in your other functions
              type        - text,textarea,yesno,password
              description - description of the variable, displayed in ClientExec
        */

        $variables = array (
                   /*T*/"Plugin Name"/*/T*/ => array (
                                        "type"          =>"hidden",
                                        "description"   =>/*T*/"How CE sees this plugin (not to be confused with the Signup Name)"/*/T*/,
                                        "value"         =>/*T*/"Protx"/*/T*/
                                       ),
                   /*T*/"Vendor ID"/*/T*/ => array (
                                        "type"          =>"text",
                                        "description"   =>/*T*/"Vendor ID used to identify you to protx.<br>NOTE: This ID is required if you have selected protx as a payment gateway for any of your clients."/*/T*/,
                                        "value"         => '',
                                       ),
                   /*T*/"Crypt Password"/*/T*/ => array (
                                        "type"          =>"password",
                                        "description"   =>/*T*/"Password used to crypt payment information.<br>NOTE: This password has to match the value set by protx."/*/T*/,
                                        "value"         =>""
                                       ),
                    /*T*/"Vendor E-mail"/*/T*/ => array (
                                        "type"          =>"text",
                                        "description"   =>/*T*/"This E-mail is sent from protx to inform the customer of the transaction.  You need to set this to your E-mail address that you want bills to come from."/*/T*/,
                                        'value'         => '',
                                       ),
                   /*T*/"Visa"/*/T*/ => array (
                                        "type"          =>"yesno",
                                        "description"   =>/*T*/"Select YES to allow Visa card acceptance with this plugin.  No will prevent this card type."/*/T*/,
                                        "value"         =>"1"
                                       ),
                   /*T*/"MasterCard"/*/T*/ => array (
                                        "type"          =>"yesno",
                                        "description"   =>/*T*/"Select YES to allow MasterCard acceptance with this plugin. No will prevent this card type."/*/T*/,
                                        "value"         =>"1"
                                       ),
                   /*T*/"AmericanExpress"/*/T*/ => array (
                                        "type"          =>"yesno",
                                        "description"   =>/*T*/"Select YES to allow American Express card acceptance with this plugin. No will prevent this card type."/*/T*/,
                                        "value"         =>"0"
                                       ),
                   /*T*/"Discover"/*/T*/ => array (
                                        "type"          =>"yesno",
                                        "description"   =>/*T*/"Select YES to allow Discover card acceptance with this plugin. No will prevent this card type."/*/T*/,
                                        "value"         =>"0"
                                       ),
                   /*T*/"Invoice After Signup"/*/T*/ => array (
                                        "type"          =>"yesno",
                                        "description"   =>/*T*/"Select YES if you want an invoice sent to the customer after signup is complete."/*/T*/,
                                        "value"         =>"1"
                                       ),
                   /*T*/"Signup Name"/*/T*/ => array (
                                        "type"          =>"text",
                                        "description"   =>/*T*/"Select the name to display in the signup process for this payment type. Example: eCheck or Credit Card."/*/T*/,
                                        "value"         =>"Credit Card"
                                       ),
                   /*T*/"Accept CC Number"/*/T*/ => array (
                                        "type"          =>"hidden",
                                        "description"   =>/*T*/"Selecting YES allows the entering of CC numbers when using this plugin type. No will prevent entering of cc information"/*/T*/,
                                        "value"         =>"0"
                                       ),
                   /*T*/"Dummy Plugin"/*/T*/ => array (
                                        "type"          =>"hidden",
                                        "description"   =>/*T*/"1 = Only used to specify a billing type for a customer. 0 = full fledged plugin requiring complete functions"/*/T*/,
                                        "value"         =>"0"
                                       ),
                   /*T*/"Auto Payment"/*/T*/ => array (
                                        "type"          =>"hidden",
                                        "description"   =>/*T*/"No description"/*/T*/,
                                        "value"         =>"0"
                                       ),
                   /*T*/"30 Day Billing"/*/T*/ => array (
                                        "type"          =>"hidden",
                                        "description"   =>/*T*/"Select YES if you want ClientExec to treat monthly billing by 30 day intervals.  If you select NO then the same day will be used to determine intervals."/*/T*/,
                                        "value"         =>"0"
                                       ),
                   /*T*/"Demo Mode"/*/T*/ => array (
                                        "type"          =>"yesno",
                                        "description"   =>/*T*/"Select YES to send all transactions to the demo form processor"/*/T*/,
                                        "value"         =>"0"
                                       ),
                   /*T*/"Check CVV2"/*/T*/ => array (
                                        "type"          =>"hidden",
                                        "description"   =>/*T*/"Select YES if you want to accept CVV2 for this plugin."/*/T*/,
                                        "value"         =>"0"
                                       )
        );
        return $variables;
    }

    function credit($params)
    {}

    function singlepayment($params)
    {
        //generate post to submit to protx
        $strRet = "<html>\n";
        $strRet .= "<head></head>\n";
        $strRet .= "<body>\n";

        if($params["plugin_protxform_Demo Mode"]==1){
            //Old URL
            //$strRet .= "<form name=\"frmProtx\" action=\"https://ukvpstest.protx.com/vspgateway/service/vspform-register.vsp\" method=\"post\">\n";

            //New URL
            $strRet .= "<form name=\"frmProtx\" action=\"https://test.sagepay.com/gateway/service/vspform-register.vsp\" method=\"post\">\n";
        }else{
            ////Old URL
            //$strRet .= "<form name=\"frmProtx\" action=\"https://ukvps.protx.com/vspgateway/service/vspform-register.vsp\" method=\"post\">\n";

            //New URL
            $strRet .= "<form name=\"frmProtx\" action=\"https://live.sagepay.com/gateway/service/vspform-register.vsp\" method=\"post\">\n";
        }

        $strRet .= "<input type=\"hidden\" name=\"VPSProtocol\" value=\"2.22\">\n";
        $strRet .= "<input type=\"hidden\" name=\"TxType\" value=\"PAYMENT\">\n";
        $strRet .= "<input type=\"hidden\" name=\"Vendor\" value=\"".$params["plugin_protxform_Vendor ID"]."\">\n";

        $sCrypt = "VendorTxCode=".$params['invoiceNumber']."D".date('Ymdhis');
        $sCrypt .= "&Amount=".sprintf("%01.2f", round($params["invoiceTotal"], 2));
        $sCrypt .= "&Currency=".$params["currencytype"];
        $sCrypt .= "&Description=Invoice Number ".$params['invoiceNumber'];
        $sCrypt .= "&SuccessURL=".$params['clientExecURL']."/plugins/gateways/protxform/callback.php?success=1";
        $sCrypt .= "&FailureURL=".$params['clientExecURL']."/plugins/gateways/protxform/callback.php?fail=1";
        $sCrypt .= "&CustomerEMail=".$params["userEmail"];
        $sCrypt .= "&VendorEMail=".$params["plugin_protxform_Vendor E-mail"];
        $sCrypt .= "&CustomerName=".$params['userFirstName']." ".$params['userLastName'];
        $sCrypt .= "&BillingAddress=".$params["userAddress"] . " ".$params["userCity"] ." ".$params["userZipcode"];
        $sCrypt .= "&BillingPostCode=".$params["userZipcode"];
        $sCrypt = base64_encode(SimpleXor($sCrypt, $params["plugin_protxform_Crypt Password"]));
        $strRet .= "<input type=\"hidden\" name=\"Crypt\" value=\"".$sCrypt."\">";
        $strRet .= "<script language=\"JavaScript\">\n";
        $strRet .= "document.forms[0].submit();\n";
        $strRet .= "</script>\n";
        $strRet .= "</form>\n";
        $strRet .= "</body></html>";
        echo $strRet;
        exit;
    }
}
?>
