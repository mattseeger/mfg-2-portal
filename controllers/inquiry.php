<?php
/**
 * Description of inquiry
 *
 * @author mseeger
 */
class inquiry extends Controller{
    //put your code here
    public function __construct() {
        parent::__construct();
        
    }
    public function thankYou(){
        $sfdc = new SFDCConnector();
        $configJSON = $_POST['configurationJSON'];
        //$quoteId = $sfdc->CreateOrphanQuote();
        $leadId = $sfdc->createLead($_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['phone'], $_POST['company'], $_POST['city'], $_POST['state'], $_POST['description'], 'a0l46000001JIReAAO');
        $this->view->renderHeader();
        $this->view->render("thanks", $_POST['first_name']);
        $this->view->renderFooter();
    }
}
