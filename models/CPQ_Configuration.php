<?php
require_once 'CPQ_Feature.php';
require_once 'CPQ_ProductOption.php';
require_once 'CPQ_Product.php';
require_once 'CPQ_ConfigurationAttribute.php';


/**
 * Represents a configuration in Salesforce CPQ
 *
 * @author mseeger
 */
class CPQ_Configuration {
    /**
     *
     * @var CPQ_Product Object 
     */
    public $product;
    
    /**
     *
     * @var CPQ_Feature[] Object 
     */
    public $features;
    
    /**
     *
     * @var CPQ_ConfigurationAttribute[] 
     */
    public $configurationAttributes;
    
    /**
     *
     * @var JSON 
     */
    public $ConfigJSON;
    
    public function __construct() {
        $this->product = new CPQ_Product();
        $this->features = array();
        $this->configurationAttributes = array();
        
    }
    public function LoadFromId($configurableProductId){
        $sfdc = new SFDCConnector();
        //$model_json = $sfdc->LoadProductByID($configurableProductId);
        $model_json = $sfdc->LoadConfigurationByID($configurableProductId);
        $this->ConfigJSON = $model_json;
        
        $model_object = json_decode($model_json);
        
        /*
         * Pull the Top Level Product Info From the Json Model to the PHP Model
         */
        $this->product->Id = $model_object->record->Id;
        $this->product->Name = $model_object->record->Name;
        $this->product->Description = $model_object->record->Description;
        $this->product->SKU = $model_object->record->ProductCode;
        $this->product->unitPrice = $model_object->record->PricebookEntries->records[0]->UnitPrice;
        //$this->product->AbsoluteImageURL = "https://mfgqtc.my.salesforce.com/servlet/servlet.ImageServer?id=" . $model_object->record->SBQQ__ProductPictureID__c . "&oid=00D460000000MPU";
        
        /*
         * Pull the Features from the JSON Model to the PHP Model
         */
        
        foreach($model_object->record->SBQQ__Features__r->records as $f ){
            $featureToAdd = new CPQ_Feature();
            $featureToAdd->Id = $f->Id;
            $featureToAdd->Name = $f->Name;
            $featureToAdd->MaxOptions = property_exists($f, 'SBQQ__MinOptionCount__c') ? $f->SBQQ__MinOptionCount__c : -1;
            $featureToAdd->MinOptions = property_exists($f, 'SBQQ__MaxOptionCount__c') ? $f->SBQQ__MaxOptionCount__c : -1;
            $featureToAdd->order = $f->SBQQ__Number__c;
            $featureToAdd->isRadio = $featureToAdd->MaxOptions == 1 ? true : false;
            
            $this->features[] = $featureToAdd;
        }
        
        /*
         * Loop over the OPtions
         */
        foreach($model_object->options as $p ){
            $po = new CPQ_ProductOption();
            $po->Id = $p->record->Id;
            $po->product->Id = $p->record->SBQQ__OptionalSKU__c;
            $po->product->Name = $p->record->SBQQ__ProductName__c;
            $po->product->Description = property_exists($p->record, 'SBQQ__ProductDescription__c') ? $p->record->SBQQ__ProductDescription__c : '';
            $po->product->SKU = $p->record->SBQQ__ProductCode__c;
            
            $po->order = $p->record->SBQQ__Number__c;
            $po->quantity = $p->record->SBQQ__Quantity__c;
            $po->quantityEditable = $p->record->SBQQ__QuantityEditable__c;
            $po->required = $p->record->SBQQ__Required__c;
            $po->selected = $p->record->SBQQ__Selected__c;
            $po->unitPrice = $p->record->SBQQ__UnitPrice__c;
            
            $featureId = $p->record->SBQQ__Feature__c;
            
            foreach($this->features as $f){
                if($f->Id == $featureId){
                    $po->featureId = $f->Id;
                    $po->isRadio = $f->isRadio;
                    $f->options[] = $po;
                    break;
                }
            }    
        }
        $productOptionDescribe = $sfdc->describeSObject('SBQQ__ProductOption__c');
        
        foreach($model_object->configurationAttributes as $ca){
            $cao = new CPQ_ConfigurationAttribute();
            $cao->Name = $ca->targetFieldName;
            $cao->DisplayOrder = $ca->displayOrder;
            foreach($productOptionDescribe->result->fields as $field){
                if($ca->targetFieldName == $field->name){
                    $cao->Label = $field->label;
                    $cao->Type = $field->type;
                    if($field->type == 'picklist'){
                        foreach($field->picklistValues as $plv){
                            $cao->PicklistValues[] = $plv->label;
                        }
                    }
                    break;
                }
            }
            $this->configurationAttributes[] = $cao;
        }
    }
}
