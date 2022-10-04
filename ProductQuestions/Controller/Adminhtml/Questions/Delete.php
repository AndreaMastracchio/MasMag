<?php
/**
 * Copyright © MasMag All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MasMag\ProductQuestions\Controller\Adminhtml\Questions;

use MasMag\ProductQuestions\Controller\Adminhtml\Questions;
use MasMag\ProductQuestions\Model\Questions as QuestionClass;
use Exception;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;

class Delete extends Questions
{

    /**
     * Delete action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('questions_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create(QuestionClass::class);
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Questions.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['questions_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Questions to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}

