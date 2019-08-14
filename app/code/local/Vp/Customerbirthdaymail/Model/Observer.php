<?php
class Vp_Customerbirthdaymail_Model_Observer {

    public function checkbirthday() {
	
        $daymonthNow = date('d').'-'.date('m');
        
        $collection = Mage::getModel("customer/customer")
        ->getCollection()->addAttributeToSelect("*")
        ->addFieldToFilter("dob",array('notnull' => false));

        foreach ($collection as $customer)
            {
                $dob = explode(' ',$customer->getDob());
                $date = explode('-',$dob[0]);
                $daymonthBirthday = $date[2].'-'.$date[1];
         
                if ( $daymonthBirthday  == $daymonthNow ) {

                    $customerName = mb_convert_case($customer->getFirstname(), MB_CASE_TITLE, "UTF-8");
                    $customerEmail = $customer->getEmail();
                    $this->sendBirthdayEmail($customerEmail,$customerName);
         
                }
            }

    }

    public function sendBirthdayEmail($customerEmail,$customerName)
    {  

        $emailTemplate  = Mage::getModel('core/email_template');
 
        $emailTemplate->loadDefault('customerbirthdaymail_email_template');
        $emailTemplate->setTemplateSubject('Happy Birthday');

        $salesData['email'] = Mage::getStoreConfig('trans_email/ident_general/email');
        $salesData['name']  = Mage::getStoreConfig('trans_email/ident_general/name');
 
        $emailTemplate->setSenderName($salesData['name']);
        $emailTemplate->setSenderEmail($salesData['email']);

        $emailTemplateVariables['username']  = $customerName;
        $emailTemplateVariables['store_name'] = Mage::app()->getStore()->getFrontendName();
        $emailTemplateVariables['store_url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        $emailTemplate->send($customerEmail, 'test', $emailTemplateVariables);

    }
}