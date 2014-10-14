<?php

$installer = $this;
/* @var $installer \Inchoo\SocialConnect\Model\Resource\Setup */

$installer->startSetup();

$installer->setCustomerAttributes(
    array(
        'inchoo_socialconnect_fid' => array(
            'type' => 'text',
            'visible' => false,
            'required' => false,
            'user_defined' => false,
            'system' => false   // Must be non system, else customer service can not update it
        ),
        'inchoo_socialconnect_ftoken' => array(
            'type' => 'text',
            'visible' => false,
            'required' => false,
            'user_defined' => false,
            'system' => false   // Must be non system, else customer service can not update it
        )
    )
);

// Install our custom attributes
$installer->installCustomerAttributes();

// Remove our custom attributes (for testing)
//$installer->removeCustomerAttributes();

$installer->endSetup();