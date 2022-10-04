<?php
/**
 * Copyright © MasMag All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MasMag\ProductQuestions\Model;

use MasMag\ProductQuestions\Api\Data\QuestionsInterface;
use MasMag\ProductQuestions\Api\Data\QuestionsInterface as QuestionsInterfaceApi;
use MasMag\ProductQuestions\Api\Data\QuestionsInterfaceFactory;
use MasMag\ProductQuestions\Api\Data\QuestionsSearchResultsInterfaceFactory;
use MasMag\ProductQuestions\Api\QuestionsRepositoryInterface;
use MasMag\ProductQuestions\Model\ResourceModel\Questions as ResourceQuestions;
use MasMag\ProductQuestions\Model\ResourceModel\Questions\CollectionFactory as QuestionsCollectionFactory;
use Exception;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class QuestionsRepository implements QuestionsRepositoryInterface
{

    /**
     * @var ResourceQuestions
     */
    protected $resource;

    /**
     * @var QuestionsInterfaceFactory
     */
    protected $questionsFactory;

    /**
     * @var Questions
     */
    protected $searchResultsFactory;

    /**
     * @var QuestionsCollectionFactory
     */
    protected $questionsCollectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;


    /**
     * @param ResourceQuestions $resource
     * @param QuestionsInterfaceFactory $questionsFactory
     * @param QuestionsCollectionFactory $questionsCollectionFactory
     * @param QuestionsSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceQuestions                      $resource,
        QuestionsInterfaceFactory              $questionsFactory,
        QuestionsCollectionFactory             $questionsCollectionFactory,
        QuestionsSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface           $collectionProcessor
    )
    {
        $this->resource = $resource;
        $this->questionsFactory = $questionsFactory;
        $this->questionsCollectionFactory = $questionsCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(QuestionsInterface $questions): QuestionsInterfaceApi
    {
        try {
            $this->resource->save($questions);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the questions: %1',
                $exception->getMessage()
            ));
        }
        return $questions;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        SearchCriteriaInterface $criteria
    )
    {
        $collection = $this->questionsCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function deleteById(string $questionsId): bool
    {
        return $this->delete($this->get($questionsId));
    }

    /**
     * @inheritDoc
     */
    public function delete(QuestionsInterface $questions): bool
    {
        try {
            $questionsModel = $this->questionsFactory->create();
            $this->resource->load($questionsModel, $questions->getQuestionsId());
            $this->resource->delete($questionsModel);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Questions: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function get(string $questionsId): QuestionsInterfaceApi
    {
        $questions = $this->questionsFactory->create();
        $this->resource->load($questions, $questionsId);
        if (!$questions->getId()) {
            throw new NoSuchEntityException(__('Questions with id "%1" does not exist.', $questionsId));
        }
        return $questions;
    }
}

