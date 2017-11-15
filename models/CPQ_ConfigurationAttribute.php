<?php
/**
 * Description of CPQ_ConfigurationAttribute
 *
 * @author mseeger
 */
class CPQ_ConfigurationAttribute {
    /**
     *
     * @var String 
     */
    public $Type;
    
    /**
     *
     * @var String 
     */
    public $Name;
    
    /**
     *
     * @var String 
     */
    public $Label;
    
    /**
     *
     * @var String[] 
     */
    public $PicklistValues;
    
    /**
     *
     * @var Integer 
     */
    public $DisplayOrder;
    
    /**
     *
     * @var type 
     */
    public $ApplyImmediately;
    
    public function __construct() {
        $this->PicklistValues = array();
    }
            
}
