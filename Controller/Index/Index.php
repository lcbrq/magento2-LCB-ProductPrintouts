<?php
/**
 * Magento2 product page pdf printout
 * 
 * @category   LCB
 * @package    LCB_ProductPrintouts
 * @author     Tomasz Gregorczyk <tom@lcbrq.com>
 */
namespace LCB\ProductPrintouts\Controller\Index;

use \Dompdf\Dompdf;
use \Dompdf\Options;

class Index extends \Magento\Framework\App\Action\Action {

    /**
     * @var \Magento\Framework\View\Layout
     */
    protected $_layout;

    /**
     * @var Magento\Catalog\Model\ProductFactory
     */
    protected $_factory;
    
    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    protected $_redirect;
    
      /**
     * @var Magento\Catalog\Model\ProductFactory
     */
    protected $_file;
    
    /**
     * PDF constructor
     * 
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Layout $layout
     * @param \Magento\Catalog\Model\ProductFactory $_productFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context, 
        \Magento\Framework\View\Layout $layout,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    )
    {
        parent::__construct($context);
        $this->_layout = $layout;
        $this->_factory = $productFactory;
        $this->_redirect = $context->getRedirect();
        $this->_file           = $fileFactory;
    }

    /**
     * PDF renderer
     * 
     * @use Dompdf
     * @return void
     */
    public function execute()
    {
        $product = $this->_factory->create()->load($this->getRequest()->getParam('id'));

        if(!$product->getId()){
            $this->_redirect($this->_redirect->getRefererUrl());
        }
        
        $html = $this->_layout->createBlock('Magento\Framework\View\Element\Template')->setProduct($product)
                ->setTemplate('LCB_ProductPrintouts::printout.phtml')
                ->toHtml();
        $fileName = $product->getName();

	$options = new \Dompdf\Options();
	$options->set('isRemoteEnabled', true);
	$domPdf = new Dompdf($options);
        $domPdf->loadHtml($html);
        $domPdf->setPaper('A4', 'portrait');
        $domPdf->render();

        return $this->_file->create("$fileName.pdf", $domPdf->output(), \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR);
    }

}