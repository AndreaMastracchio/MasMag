<?php

/**
 *
 * Class Questions
 * @package MasMag\ProductQuestions\Helper
 *
 * @author Andrea Gregorio Mastracchio
 * @email andreamastrachio@live.com
 *
 */

namespace MasMag\ProductQuestions\Helper;

use MasMag\ProductQuestions\Api\AnswersRepositoryInterface as AnswersRepository;
use MasMag\ProductQuestions\Api\QuestionsRepositoryInterface as QuestionsRepository;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;

/**
 * @property Registry $registry
 * @property QuestionsRepository $questionsRepository
 * @property SearchCriteriaBuilder $searchCriteriaBuilder
 * @property AnswersRepository $answersRepository
 */
class Questions extends AbstractHelper
{

    public function __construct(
        Context               $context,
        QuestionsRepository   $questionsRepository,
        AnswersRepository     $answersRepository,
        Registry              $registry,
        SearchCriteriaBuilder $searchCriteriaBuilder
    )
    {
        $this->questionsRepository = $questionsRepository;
        $this->answersRepository = $answersRepository;
        $this->registry = $registry;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        parent::__construct($context);
    }


    /**
     * @throws LocalizedException
     */
    public function getQuestionCollection(): ?array
    {
        $questions = $this->getQuestions();
        foreach ($questions as $question) {
            $answers = $this->getAnswers($question->getId());
            $question->setData('answers', $answers);
        }
        return $questions;
    }

    /**
     * @throws LocalizedException
     */
    public function getQuestions(): array
    {
        return $this->questionsRepository->getList(
            $this->questionSearchCriteria()
        )->getItems();
    }

    public function questionSearchCriteria(): SearchCriteria
    {
        return $this->searchCriteriaBuilder
            ->addFilter('product_sku', $this->getCurrentProduct()->getSku(), 'eq')
            ->addFilter('is_approved', 1, 'eq')
            ->create();
    }

    public function getCurrentProduct(): object
    {
        return $this->registry->registry('current_product');
    }

    /**
     * @throws LocalizedException
     */
    public function getAnswers($question_id): array
    {
        return $this->answersRepository->getList(
            $this->answersSearchCriteria($question_id)
        )->getItems();
    }

    public function answersSearchCriteria($question_id): SearchCriteria
    {
        return $this->searchCriteriaBuilder
            ->addFilter('questions_id', $question_id, 'eq')
            ->addFilter('is_approved', 1, 'eq')
            ->create();
    }

    public function getAnswersArray($question): array
    {
        return $question->getData('answers');
    }

    /**
     * @throws LocalizedException
     */
    public function haveQuestions(): bool
    {
        return (bool)$this->getQuestions();
    }

    /**
     * @throws LocalizedException
     */
    public function haveAnswers($question_id): bool
    {
        return (bool)$this->getAnswers($question_id);
    }

}
