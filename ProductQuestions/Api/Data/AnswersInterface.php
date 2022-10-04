<?php
/**
 * Copyright © MasMag All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MasMag\ProductQuestions\Api\Data;

use MasMag\ProductQuestions\Answers\Api\Data\AnswersInterface as AnswersInterfaceApi;

interface AnswersInterface
{

    public const ANSWERS_ID = 'answers_id';
    public const CUSTOMER_EMAIL = 'customer_email';
    public const IS_APPROVED = 'is_approved';
    public const ANSWER = 'answer';
    public const CUSTOMER_NAME = 'customer_name';
    public const QUESTIONS_ID = 'questions_id';

    /**
     * Get answers_id
     * @return string|null
     */
    public function getAnswersId(): ?string;

    /**
     * Set answers_id
     * @param string $answersId
     * @return AnswersInterfaceApi
     */
    public function setAnswersId(string $answersId): AnswersInterfaceApi;

    /**
     * Set answers_id
     * @return string|null
     */
    public function getQuestionsId(): ?string;

    /**
     * Set answers_id
     * @param string $questionsId
     * @return AnswersInterfaceApi
     */
    public function setQuestionsId(string $questionsId): AnswersInterfaceApi;

    /**
     * Get customer_name
     * @return string|null
     */
    public function getCustomerName(): ?string;

    /**
     * Set customer_name
     * @param string $customerName
     * @return AnswersInterfaceApi
     */
    public function setCustomerName(string $customerName): AnswersInterfaceApi;

    /**
     * Get customer_email
     * @return string|null
     */
    public function getCustomerEmail(): ?string;

    /**
     * Set customer_email
     * @param string $customerEmail
     * @return AnswersInterfaceApi
     */
    public function setCustomerEmail(string $customerEmail): AnswersInterfaceApi;

    /**
     * Get answer
     * @return string|null
     */
    public function getAnswer(): ?string;

    /**
     * Set answer
     * @param string $answer
     * @return AnswersInterfaceApi
     */
    public function setAnswer(string $answer): AnswersInterfaceApi;

    /**
     * Get is_approved
     * @return string|null
     */
    public function getIsApproved(): ?string;

    /**
     * Set is_approved
     * @param string $isApproved
     * @return AnswersInterfaceApi
     */
    public function setIsApproved(string $isApproved): AnswersInterfaceApi;
}

