<?php
$installer = $this;

$installer->startSetup();
$installer->run("
INSERT INTO {$this->getTable('storelocator')} (`name`, `latitude`, `longitude`, `address`, `city`, `state`, `country`, `zipcode`, `phone`, `web_url`, `email`, `hours1`, `status`, `storeimage`, `description`) VALUES
('Sparx It Solutions', '28.56285', '77.40563299999997', 'A-02 sparx it solution ', 'Noida', 'Uttar Pradesh', 'IN', '201307', '06666 555 444', 'sparxitsolutions.com', 'rkrameshraja@gmail.com', '9:30 am To 6:30 pm', '1', 'logo.jpg', 'Sparx It Solutions');
");

$installer->endSetup();
