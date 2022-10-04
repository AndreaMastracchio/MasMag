<?php
/**
 * Copyright © MasMag All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MasMag\ProductQuestions\Api\Data;

use MasMag\ProductQuestions\Model\Questions;

interface QuestionsInterface
{

    public const QUESTION = 'question';
    public const CUSTOMER_EMAIL = 'customer_email';
    public const IS_APPROVED = 'is_approved';
    public const QUESTIONS_ID = 'questions_id';
    public const PRODUCT_SKU = 'product_sku';
    public const CUSTOMER_NAME = 'customer_name';

    /**
     * Get questions_id
     * @return string|null
     */
    public function getQuestionsId(): ?string;

    /**
     * Set questions_id
     * @param string $questionsId
     */
    public function setQuestionsId(string $questionsId);

    /**
     * Get customer_name
     * @return string|null
     */
    public function getCustomerName(): ?string;

    /**
     * Set customer_name
     * @param string $customerName
     */
    public function setCustomerName(string $customerName);

    /**
     * Get customer_email
     * @return string|null
     */
    public function getCustomerEmail(): ?string;

    /**
     * Set customer_email
     * @param string $customerEmail
     */
    public function setCustomerEmail(string $customerEmail): Questions;

    /**
     * Get question
     * @return string|null
     */
    public function getQuestion(): ?string;

    /**
     * Set question
     * @param string $question
     */
    public function setQuestion(string $question);

    /**
     * Get is_approved
     * @return string|null
     */
    public function getIsApproved(): ?string;

    /**
     * Set is_approved
     * @param string $isApproved
     */
    public function setIsApproved(string $isApproved);

    /**
     * Get product_sku
     * @return string|null
     */
    public function getProductSku(): ?string;

    /**
     * Set product_sku
     * @param string $productSku
     */
    public function setProductSku(string $productSku);
}

