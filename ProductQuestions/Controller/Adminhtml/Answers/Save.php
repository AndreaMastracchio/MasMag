<?php
/**
 * Copyright © MasMag All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MasMag\ProductQuestions\Controller\Adminhtml\Answers;

use MasMag\ProductQuestions\Model\Answers;
use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends Action
{

    protected $dataPersistor;

    /**
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context                $context,
        DataPersistorInterface $dataPersistor
    )
    {
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('answers_id');
            $model = $this->_objectManager->create(Answers::class)->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Answers no longer exists.'));
                return $resultRedirect->setPath('*/*/', $this->getRequest()->getParam('questions_id'));
            }

            $data['questions_id'] = $this->getRequest()->getParam('questions_id');
            $model->setData($data);

            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Answers.'));
                $this->dataPersistor->clear('masmag_productquestions_answers');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['answers_id' => $model->getId(), 'questions_id' => $model->getQuestionsId()]);
                }
                return $resultRedirect->setPath('*/*/', ['questions_id' => $this->getRequest()->getParam('questions_id')]);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Answers.'));
            }

            $this->dataPersistor->set('masmag_productquestions_answers', $data);
            return $resultRedirect->setPath('*/*/edit', ['answers_id' => $this->getRequest()->getParam('answers_id'), 'questions_id' => $model->getQuestionsId()]);
        }
        return $resultRedirect->setPath('*/*/', ['questions_id' => $this->getRequest()->getParam('questions_id')]);
    }
}

