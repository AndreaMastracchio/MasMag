<?php
/**
 * Copyright © MasMag All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MasMag\ProductQuestions\Api;

use MasMag\ProductQuestions\Api\Data\AnswersInterface;
use MasMag\ProductQuestions\Api\Data\AnswersSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

interface AnswersRepositoryInterface
{

    /**
     * Save Answers
     * @param AnswersInterface $answers
     * @return AnswersInterface
     * @throws LocalizedException
     */
    public function save(
        AnswersInterface $answers
    ): AnswersInterface;

    /**
     * Retrieve Answers
     * @param string $answersId
     * @return AnswersInterface
     * @throws LocalizedException
     */
    public function get(string $answersId): AnswersInterface;

    /**
     * Retrieve Answers matching the specified criteria.
     * @param SearchCriteriaInterface $searchCriteria
     * @return AnswersSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Answers
     * @param AnswersInterface $answers
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(
        AnswersInterface $answers
    ): bool;

    /**
     * Delete Answers by ID
     * @param string $answersId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById(string $answersId): bool;
}

