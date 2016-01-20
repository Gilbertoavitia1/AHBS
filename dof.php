<?php
    require_once ('app/Mage.php');
    Mage::app();
    $resource = Mage::getSingleton('core/resource');
    $readConnection = $resource->getConnection('core_read');
    $writeConnection = $resource->getConnection('core_write');
     
    $id = '100001813';
    $query = "SELECT * FROM sales_flat_order WHERE increment_id={$id}";
    $results = $readConnection->fetchAll($query);

    $orderId = $results[0]['entity_id'];
    $quoteId = $results[0]['quote_id'];
    $customerId = $results[0]['customer_id'];

    $query = "DELETE FROM sales_flat_creditmemo_comment WHERE parent_id IN (SELECT entity_id FROM sales_flat_creditmemo WHERE order_id={$orderId})";
    $writeConnection->query($query);
    $query = "DELETE FROM sales_flat_creditmemo_comment WHERE parent_id IN (SELECT entity_id FROM sales_flat_creditmemo WHERE order_id={$orderId})";
    $writeConnection->query($query);
    $query = "DELETE FROM sales_flat_creditmemo_item WHERE parent_id IN (SELECT entity_id FROM sales_flat_creditmemo WHERE order_id={$orderId})";
    $writeConnection->query($query);
    $query = "DELETE FROM sales_flat_creditmemo WHERE order_id={$orderId}";
    $writeConnection->query($query);
    $query = "DELETE FROM sales_flat_creditmemo_grid WHERE order_id={$orderId}";
    $writeConnection->query($query);

    $query = "DELETE FROM sales_flat_invoice_comment WHERE parent_id IN (SELECT entity_id FROM sales_flat_invoice WHERE order_id={$orderId})";
    echo $query.';<br>';
    $writeConnection->query($query);
    $query = "DELETE FROM sales_flat_invoice_item WHERE parent_id IN (SELECT entity_id FROM sales_flat_invoice WHERE order_id={$orderId})";
    echo $query.';<br>';
    $writeConnection->query($query);
    $query = "DELETE FROM sales_flat_invoice WHERE order_id={$orderId}";
    echo $query.';<br>';
    $writeConnection->query($query);
    $query = "DELETE FROM sales_flat_invoice_grid WHERE order_id={$orderId}";
    echo $query.';<br>';
    $writeConnection->query($query);
    
    $query = "DELETE FROM sales_flat_quote_address_item WHERE parent_item_id IN (SELECT address_id FROM sales_flat_quote_address WHERE quote_id={$orderId})";
    echo $query.';<br>';
    $writeConnection->query($query);
    $query = "DELETE FROM sales_flat_quote_shipping_rate WHERE address_id IN (SELECT address_id FROM sales_flat_quote_address WHERE quote_id={$orderId})";
    echo $query.';<br>';
    $writeConnection->query($query);
    $query = "DELETE FROM sales_flat_quote_item_option WHERE item_id IN (SELECT item_id FROM sales_flat_quote_item WHERE quote_id={$orderId})";
    echo $query.';<br>';
    $writeConnection->query($query);
    $query = "DELETE FROM sales_flat_quote WHERE entity_id={$orderId}";
    echo $query.';<br>';
    $writeConnection->query($query);
    $query = "DELETE FROM sales_flat_quote_address WHERE quote_id={$orderId}";
    echo $query.';<br>';
    $writeConnection->query($query);
    $query = "DELETE FROM sales_flat_quote_item WHERE quote_id={$orderId}";
    echo $query.';<br>';
    $writeConnection->query($query);
    $query = "DELETE FROM sales_flat_quote_payment WHERE quote_id={$orderId}";
    echo $query.';<br>';
    $writeConnection->query($query);
    
    $query = "DELETE FROM sales_flat_shipment_comment WHERE parent_id IN (SELECT entity_id FROM sales_flat_shipment WHERE order_id={$orderId})";
    echo $query.';<br>';
    $writeConnection->query($query);
    $query = "DELETE FROM sales_flat_shipment_item WHERE parent_id IN (SELECT entity_id FROM sales_flat_shipment WHERE order_id={$orderId})";
    echo $query.';<br>';
    $writeConnection->query($query);
    $query = "DELETE FROM sales_flat_shipment_track WHERE order_id  IN (SELECT entity_id FROM sales_flat_shipment WHERE order_id={$orderId})";
    echo $query.';<br>';
    $writeConnection->query($query);
    $query = "DELETE FROM sales_flat_shipment WHERE order_id={$orderId}";
    echo $query.';<br>';
    $writeConnection->query($query);
    $query = "DELETE FROM sales_flat_shipment_grid WHERE order_id={$orderId}";
    echo $query.';<br>';
    $writeConnection->query($query);
     

    $query = "DELETE FROM sales_flat_order WHERE entity_id={$orderId}";
    echo $query.';<br>';
    $writeConnection->query($query);
    $query = "DELETE FROM sales_flat_order_address WHERE parent_id={$orderId}";
    echo $query.';<br>';
    $writeConnection->query($query);
    $query = "DELETE FROM sales_flat_order_item WHERE order_id={$orderId}";
    echo $query.';<br>';
    $writeConnection->query($query);
    $query = "DELETE FROM sales_flat_order_payment WHERE parent_id={$orderId}";
    echo $query.';<br>';
    $writeConnection->query($query);
    $query = "DELETE FROM sales_flat_order_status_history WHERE parent_id={$orderId}";
    echo $query.';<br>';
    $writeConnection->query($query);
    $query = "DELETE FROM sales_flat_order_grid WHERE increment_id={$orderId}";
    echo $query.';<br>';
    $writeConnection->query($query);


    $query = "DELETE FROM log_quote WHERE quote_id={$orderId}";
    echo $query.';<br>';
    $writeConnection->query($query);
?>
