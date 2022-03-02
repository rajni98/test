<?php

namespace Appcoders\Profitplus\Controller\Index;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;

abstract class ApiController extends \Magento\Framework\App\Action\Action implements \Magento\Framework\App\CsrfAwareActionInterface {
	protected $_helper;

	public function __construct(\Magento\Framework\App\Action\Context $context) {
		parent::__construct($context);
	}
/** * @inheritDoc */
	public function createCsrfValidationException(RequestInterface $request):  ? InvalidRequestException {
		return null;
	}
/** * @inheritDoc */
	public function validateForCsrf(RequestInterface $request) :  ? bool {
		return true;
	}
}
