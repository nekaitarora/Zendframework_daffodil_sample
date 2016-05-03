<?php

/**
 * Class BidForm
 * 
 * Will be used for form fields and validations
 * 
 */

namespace CsnCms\Form;

use Zend\Form\Form;

class BidForm extends Form {

    /**
     * Constructor function
     *
     * @param array
     * @return void
     */
    public function __construct($entityManager) {
        // we want to ignore the name passed
        parent::__construct('bid');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        $renewalNumber = array();
        for ($i = 0; $i <= 20; $i++) {
            $renewalNumber[$i] = $i;
        }
        $bidTerm = array();
        for ($i = 1; $i <= 5; $i++) {
            $bidTerm[$i.' Year'] = $i.' Year';
        }
        // Add form fields
        $this->add(array(
            'name' => 'name',
            'type' => 'Text',
            'attributes' => array(
                'required' => 'required',
                'class' => 'form-control'
            )
        ));
        $this->add(array(
            'name' => 'bidproposer',
            'type' => 'Text',
            'attributes' => array(
                'required' => 'required',
                'class' => 'form-control'
            )
        ));

        $this->add(array(
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'name' => 'locationid',
            'attributes' => array(
                'id' => 'locationid',
                'class' => 'selectpicker form-control',
                'required' => 'required',
            ),
            'options' => array(
                'class' => 'select-label',
                'object_manager' => $entityManager,
                'target_class' => 'CsnCms\Entity\Location',
                'property' => 'name'
            )
        ));

        $this->add(array(
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'name' => 'companytypeid',
            'attributes' => array(
                'id' => 'companytypeid',
                'class' => 'selectpicker form-control',
                'required' => 'required',
            ),
            'options' => array(
                'class' => 'select-label',
                'object_manager' => $entityManager,
                'target_class' => 'CsnCms\Entity\CompanyType',
                'property' => 'name'
            )
        ));

        $this->add(array(
            'name' => 'businessesnumber',
            'type' => 'Text',
            'attributes' => array(
                'required' => 'required',
                'class' => 'form-control'
            )
        ));
        $this->add(array(
            'name' => 'rvsize',
            'type' => 'Text',
            'attributes' => array(
                'required' => 'required',
                'class' => 'form-control'
            )
        ));
        $this->add(array(
            'name' => 'streetsnumber',
            'type' => 'Text',
            'attributes' => array(
                'required' => 'required',
                'class' => 'form-control'
            )
        ));
        $this->add(array(
            'name' => 'sectornumber',
            'type' => 'Text',
            'attributes' => array(
                'required' => 'required',
                'class' => 'form-control'
            )
        ));
        $this->add(array(
            'name' => 'renewaldate',
            'type' => 'Date',
            'attributes' => array(
                'class' => 'datepicker form-control',
                'required' => false,
            )
        ));

        $this->add(array(
            'name' => 'startdate',
            'type' => 'Date',
            'attributes' => array(
                'class' => 'datepicker form-control',
                'required' => false,
            )
        ));

        $this->add(array(
            'name' => 'bidmap',
            'type' => 'file',
            'attributes' => array(
                'required' => false
            )
        ));
        $this->add(array(
            'name' => 'biddata',
            'type' => 'file',
            'attributes' => array(
                'required' => false
            )
        ));
        $this->add(array(
            'name' => 'results',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control'
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'term',
            'attributes' => array(
                'id' => 'term',
                'class' => 'selectpicker form-control',
                'options' => $bidTerm
            ),
            'options' => array(
                'label_attributes' => array(
                    'class' => 'select-label'
                ),
            ),
        ));


        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'renewalnumber',
            'attributes' => array(
                'id' => 'renewalnumber',
                'class' => 'selectpicker form-control',
                'options' => $renewalNumber
            ),
            'options' => array(
                'label_attributes' => array(
                    'class' => 'select-label'
                ),
            ),
        ));

        $this->add(array(
            'name' => 'levytype',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control'
            )
        ));
        
        $this->add(array(
            'name' => 'percentagelevy',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control',
                'id'=>'percentagelevy'
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'levytype',
            'attributes' => array(
                'id' => 'levytype',
                'class' => 'selectpicker form-control',
                'options' => array('' => 'Select Levy Type','Percentage Rate' => 'Percentage Rate', 'Banded' => 'Banded')
            ),
            'options' => array(
                'label_attributes' => array(
                    'class' => 'select-label'
                ),
            ),
        ));

        $this->add(array(
            'name' => 'lavyraisedannually',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control',
                'id'=>'lavyraisedannually',
                'value'=>'0'
            )
        ));
        $this->add(array(
            'name' => 'aditionalincomeannually',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control',
                'id'=>'aditionalincomeannually',
                'value'=>'0'
            )
        ));
        $this->add(array(
            'name' => 'totalbudget',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control',
                'id'=>'totalbudget',
                'readonly'=>'readonly',
                'value'=>'0'
            )
        ));

        $this->add(array(
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'name' => 'projectarea',
            'attributes' => array(
                'id' => 'projectarea',
                'multiple' => 'multiple',
                'class' => 'selectpicker form-control',
            ),
            'options' => array(
                'class' => 'select-label',
                'object_manager' => $entityManager,
                'target_class' => 'CsnCms\Entity\KeyProjectArea',
                'property' => 'name',
            )
        ));
        
        $this->add(array(
            'name' => 'staffcount',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control'
            )
        ));
        $this->add(array(
            'name' => 'sizeofboard',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'form-control'
            )
        ));
        $this->add(array(
            'name' => 'usercreated',
            'type' => 'hidden',
            'attributes' => array(
                'class' => 'form-control'
            )
        ));



        $this->add(array(
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => array(
                'value' => 'Save',
                'id' => 'add',
                'class' => 'btn btn-success btn-lg'
            ),
        ));



        $this->add(array(
            'type' => 'hidden',
            'name' => 'id',
        ));
    }

}

?>