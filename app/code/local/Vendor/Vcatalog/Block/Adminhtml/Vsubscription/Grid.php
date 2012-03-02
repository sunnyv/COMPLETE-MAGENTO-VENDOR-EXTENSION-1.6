<?php

class Vendor_Vcatalog_Block_Adminhtml_Vsubscription_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('subscriptionGrid');
      $this->setDefaultSort('id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('vcatalog/vsubscription')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('id', array(
          'header'    => Mage::helper('vcatalog')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'id',
      ));

      $this->addColumn('subsname', array(
          'header'    => Mage::helper('vcatalog')->__('Subscription Name'),
          'align'     =>'left',
          'index'     => 'subsname',
      ));
	
	
	  $this->addColumn('duration', array(
          'header'    => Mage::helper('vcatalog')->__('Duration'),
          'align'     =>'left',
          'index'     => 'duration',
      ));
	  
	  $this->addColumn('amount', array(
          'header'    => Mage::helper('vcatalog')->__('Amount'),
          'align'     =>'left',
          'index'     => 'amout',
      ));
	  
      $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('vcatalog')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('vcatalog')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
    
		  /*
      $this->addColumn('content', array(
			'header'    => Mage::helper('vcatalog')->__('Item Content'),
			'width'     => '150px',
			'index'     => 'content',
      ));
	  */

    
     	$this->addExportType('*/*/exportCsv', Mage::helper('vcatalog')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('vcatalog')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
       $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('vcatalog');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('vcatalog')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('vcatalog')->__('Are you sure?')
        ));

 /*comment to block the status section from admin*/
      //$statuses = Mage::getSingleton('vcatalog/status')->getOptionArray();
        //array_unshift($statuses, array('label'=>'', 'value'=>''));
        //$this->getMassactionBlock()->addItem('status', array(
          //   'label'=> Mage::helper('vcatalog')->__('Change status'),
            // 'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             //'additional' => array(
               //     'visibility' => array(
                 //        'name' => 'status',
                   //      'type' => 'select',
                     //    'class' => 'required-entry',
                       //  'label' => Mage::helper('vcatalog')->__('Status'),
                         //'values' => $statuses
                     //)
             //)
        //));
   /* end of section */    
	    return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}