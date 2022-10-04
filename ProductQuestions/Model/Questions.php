<?php
/**
 * Copyright © MasMag All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MasMag\ProductQuestions\Model;

use MasMag\ProductQuestions\Api\Data\QuestionsInterface;
use Magento\Framework\Model\AbstractModel;

class Questions extends AbstractModel implements QuestionsInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(ResourceModel\Questions::class);
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
    public function setQuestionsId(string $questionsId)
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
    public function setCustomerName(string $customerName)
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
    public function setCustomerEmail(string $customerEmail): Questions
    {
        return $this->setData(self::CUSTOMER_EMAIL, $customerEmail);
    }

    /**
     * @inheritDoc
     */
    public function getQuestion(): ?string
    {
        return $this->getData(self::QUESTION);
    }

    /**
     * @inheritDoc
     */
    public function setQuestion(string $question)
    {
        return $this->setData(self::QUESTION, $question);
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
    public function setIsApproved(string $isApproved)
    {
        return $this->setData(self::IS_APPROVED, $isApproved);
    }

    /**
     * @inheritDoc
     */
    public function getProductSku(): ?string
    {
        return $this->getData(self::PRODUCT_SKU);
    }

    /**
     * @inheritDoc
     */
    public function setProductSku(string $productSku)
    {
        return $this->setData(self::PRODUCT_SKU, $productSku);
    }
}

