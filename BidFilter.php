<?php

/**
 * Class AddressForm
 * 
 * Will be used for form fields and validations
 * 
 */

namespace CsnCms\Form;

use Zend\InputFilter\InputFilter;

class BidFilter extends InputFilter {

    /**
     * Constructor function
     *
     * @param array
     * @return void
     */
    public function __construct() {
        $this->add(array(
            'name' => 'renewalnumber',
            'required' => false,
        ));
        $this->add(array(
            'name' => 'levytype',
            'required' => false,
        ));
        $this->add(array(
            'name' => 'renewaldate',
            'required' => false,
        ));
        $this->add(array(
            'name' => 'startdate',
            'required' => false,
        ));
        $this->add(array(
            'name' => 'projectarea',
            'required' => false,
        ));
        $this->add(array(
            'name' => 'term',
            'required' => false,
        ));
        $this->add(array(
            'name' => 'bidmap',
            'required' => false,
        ));
        $this->add(array(
            'name' => 'biddata',
            'required' => false,
        ));
    }

}

?>