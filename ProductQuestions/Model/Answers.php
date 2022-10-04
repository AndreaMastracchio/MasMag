<?php
/**
 * Copyright © MasMag All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MasMag\ProductQuestions\Model;

use MasMag\ProductQuestions\Answers\Api\Data\AnswersInterface as AnswersInterfaceApi;
use MasMag\ProductQuestions\Api\Data\AnswersInterface;
use Magento\Framework\Model\AbstractModel;

class Answers extends AbstractModel implements AnswersInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(ResourceModel\Answers::class);
    }

    /**
     * @inheritDoc
     */
    public function getAnswersId(): ?string
    {
        return $this->getData(self::ANSWERS_ID);
    }

    /**
     * @inheritDoc
     */
    public function setAnswersId(string $answersId): AnswersInterfaceApi
    {
        return $this->setData(self::ANSWERS_ID, $answersId);
    }

    /**
     * @inheritDoc
     */
    public function getQuestionsId(): ?string
    {
        return $this->getData(self::QUESTIONS_ID);
    }

    /**
     * @inheritDoc
     */
    public function setQuestionsId(string $questionsId): AnswersInterfaceApi
    {
        return $this->setData(self::QUESTIONS_ID, $questionsId);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerName(): ?string
    {
        return $this->getData(self::CUSTOMER_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerName(string $customerName): AnswersInterfaceApi
    {
        return $this->setData(self::CUSTOMER_NAME, $customerName);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerEmail(): ?string
    {
        return $this->getData(self::CUSTOMER_EMAIL);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerEmail(string $customerEmail): AnswersInterfaceApi
    {
        return $this->setData(self::CUSTOMER_EMAIL, $customerEmail);
    }

    /**
     * @inheritDoc
     */
    public function getAnswer(): ?string
    {
        return $this->getData(self::ANSWER);
    }

    /**
     * @inheritDoc
     */
    public function setAnswer(string $answer): AnswersInterfaceApi
    {
        return $this->setData(self::ANSWER, $answer);
    }

    /**
     * @inheritDoc
     */
    public function getIsApproved(): ?string
    {
        return $this->getData(self::IS_APPROVED);
    }

    /**
     * @inheritDoc
     */
    public function setIsApproved(string $isApproved): AnswersInterfaceApi
    {
        return $this->setData(self::IS_APPROVED, $isApproved);
    }
}

