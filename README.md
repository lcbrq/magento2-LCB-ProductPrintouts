# Skeleton for Magento2 product printing

## Usage

Specify your custom printout file in your theme LCB_ProductPrintouts/templates/printout.phtml  

Call   

```php
<a href="<?php echo $block->getUrl('productprintouts', ['id' => $_product->getId()]); ?>"><?php echo __('Print'); ?></a>
```  

On your product page
