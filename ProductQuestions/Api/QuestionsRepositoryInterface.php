<?php
/**
 * Copyright © MasMag All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MasMag\ProductQuestions\Api;

use MasMag\ProductQuestions\Api\Data\QuestionsInterface;
use MasMag\ProductQuestions\Api\Data\QuestionsSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

interface QuestionsRepositoryInterface
{

    /**
     * Save Questions
     * @param QuestionsInterface $questions
     * @return QuestionsInterface
     * @throws LocalizedException
     */
    public function save(
        QuestionsInterface $questions
    ): QuestionsInterface;

    /**
     * Retrieve Questions
     * @param string $questionsId
     * @return QuestionsInterface
     * @throws LocalizedException
     */
    public function get(string $questionsId): QuestionsInterface;

    /**
     * Retrieve Questions matching the specified criteria.
     * @param SearchCriteriaInterface $searchCriteria
     * @return QuestionsSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Questions
     * @param QuestionsInterface $questions
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(
        QuestionsInterface $questions
    ): bool;

    /**
     * Delete Questions by ID
     * @param string $questionsId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById(string $questionsId): bool;
}

