<?php
/**
 * Description of SFDCConnector
 *
 * @author mseeger
 */
class SFDCConnector {
    /**
     *
     * @var SoapClient 
     */
    public $soap;
    
    /**
     *
     * @var String 
     */
    public $serverURL;
    
    /**
     *
     * @var String 
     */
    public $sessionId;
    
    /**
     *
     * @var SOAPClient 
     */
    public $cpqSoap;
    public function __construct() {
        $this->soap = new SoapClient('lib/sfdc_enterprise_v40.wsdl', array('trace' => 1));
        $this->cpqSoap = new SoapClient('lib/SalesforceCPQAPI.wsdl', array('trace' => 1));
        $this->login();
    }
    
    public function login(){
        $soap_data = new stdClass();
        $soap_data->username = SFDC_API_USER;
        $soap_data->password = SFDC_API_PASSWORD.SFDC_API_TOKEN;
        try{
            $login_response = $this->soap->login($soap_data);
        }
        catch(Exception $e){
            var_dump($this->soap->__getLastRequestHeaders());
            var_dump($this->soap->__getLastRequest());
            
            var_dump($this->soap->__getLastResponseHeaders());
            var_dump($this->soap->__getLastResponse());
        }
        $this->serverURL = $login_response->result->serverUrl;
        $this->sessionId = $login_response->result->sessionId;
        $this->soap->__setLocation($this->serverURL);
        
        $sessionData = new stdClass();
        $sessionData->sessionId = $this->sessionId;
        $sessionHeader = new SoapHeader("urn:enterprise.soap.sforce.com", "SessionHeader", $sessionData);
        
        $packageVersions = new stdClass();
        $packageVersions->PackageVersion = new stdClass();
        $packageVersions->PackageVersion->majorNumber = 28;
        $packageVersions->PackageVersion->minorNumber = 0;
        $packageVersions->PackageVersion->namespace = "SBQQ";
        $packageVersionsHeader = new SoapHeader("urn:enterprise.soap.sforce.com","PackageVersions", $packageVersions);
        
        $headers = array($sessionHeader/*, $packageVersionsHeader*/);
        $this->cpqSoap->__setSoapHeaders($headers);
        $this->soap->__setSoapHeaders($headers);
   
    }
    
    public function query($queryString){
        $soap_data = new stdClass();
        $soap_data->queryString = $queryString;
        try{
            $query_response = $this->soap->query($soap_data);
        }
        catch(SoapFault $e){
            echo $e->getMessage();
        }
        return $query_response;
    }
    
    public function createLead($first, $last, $email, $phone, $company, $city, $state, $description='Web Lead', $quoteId = NULL){
        $reqXML = new DOMDocument();
	$s_Envelope = $reqXML->createElementNS ("http://schemas.xmlsoap.org/soap/envelope/" , "s:Envelope");
	$reqXML->appendChild($s_Envelope);

	$s_Header = $reqXML->createElementNS ("http://schemas.xmlsoap.org/soap/envelope/" , "s:Header");
	$s_Envelope->appendChild($s_Header);

	$s_Body = $reqXML->createElementNS ("http://schemas.xmlsoap.org/soap/envelope/" , "s:Body");
	$s_Envelope->appendChild($s_Body);

	$sf_SeassionHeader = $reqXML->createElementNS ("urn:enterprise.soap.sforce.com" , "sf:SessionHeader");
	$s_Header->appendChild($sf_SeassionHeader);

	$sf_sessionId = $reqXML->createElementNS ("urn:enterprise.soap.sforce.com" , "sf:sessionId", $this->sessionId);
	$sf_SeassionHeader->appendChild($sf_sessionId);

	$sf_create = $reqXML->createElementNS ("urn:enterprise.soap.sforce.com" , "sf:create");
	$s_Body->appendChild($sf_create);

	$sf_sObjects = $reqXML->createElementNS ("urn:enterprise.soap.sforce.com" , "sf:sObjects");
	$sf_sObjects->setAttributeNS ("http://www.w3.org/2001/XMLSchema-instance" , "xsi:type" , "Lead");
	$sf_create->appendChild($sf_sObjects);
        
        $leadFirst = $reqXML->createElement('FirstName', $first);
        $sf_sObjects->appendChild($leadFirst);
        
        $leadLast = $reqXML->createElement('LastName', $last);
        $sf_sObjects->appendChild($leadLast);
        
        $leadEmail = $reqXML->createElement('Email', $email);
        $sf_sObjects->appendChild($leadEmail);
        
        $leadPhone = $reqXML->createElement('Phone', $phone);
        $sf_sObjects->appendChild($leadPhone);
        
        $leadCompany = $reqXML->createElement('Company', $company);
        $sf_sObjects->appendChild($leadCompany);
        
        $leadCity = $reqXML->createElement('City', $city);
        $sf_sObjects->appendChild($leadCity);
        
        $leadState = $reqXML->createElement('State', $state);
        $sf_sObjects->appendChild($leadState);
        
        $leadSource = $reqXML->createElement('LeadSource', 'Web');
        $sf_sObjects->appendChild($leadSource);
        
        $leadDescription = $reqXML->createElement('Description', $description);
        $sf_sObjects->appendChild($leadDescription);
        
        $leadWebLead = $reqXML->createElement('Web_Lead__c', true);
        $sf_sObjects->appendChild($leadWebLead);
        
        if($quoteId !== NULL){
            $leadQuote = $reqXML->createElement('Web_Quote__c', $quoteId);
            $sf_sObjects->appendChild($leadQuote);
        }

        $ch = curl_init($this->serverURL);
	curl_setopt( $ch, CURLOPT_POST, true );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml', 'SOAPAction: ""'));
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $reqXML->saveXML());
	$result = curl_exec($ch);
	curl_close($ch);

	$resXML = new DOMDocument();
	$resXML->loadXML($result);
        $leadId = $resXML->getElementsByTagName("id")->item(0)->textContent;
        return $leadId;       
    }
    
    /**
     * 
     * @param String $productID
     * @return JSON ProductModel
     */
    public function LoadProductByID($productID){
        $LoadProductByIDResponse = NULL;
        $soap_data = new stdClass();
        $soap_data->productId = $productID;
        
        try{
            $LoadProductByIDResponse = $this->cpqSoap->LoadProductByID($soap_data);
        } 
        
        catch (Exception $ex) {
            //var_dump($ex);
        }
        
        return $LoadProductByIDResponse->result;
    }
    
    public function LoadConfigurationByID($productID){
        $LoadConfigurationByIDResponse = NULL;
        $soap_data = new stdClass();
        $soap_data->productId = $productID;
        
        try{
            $LoadConfigurationByIDResponse = $this->cpqSoap->ConfigLoader($soap_data);
        } catch (Exception $ex) {

        }
        return $LoadConfigurationByIDResponse->result;
    }
}
