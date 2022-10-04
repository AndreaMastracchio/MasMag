<?php
/**
 * Copyright © MasMag All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MasMag\ProductQuestions\Model;

use MasMag\ProductQuestions\Api\AnswersRepositoryInterface;
use MasMag\ProductQuestions\Api\Data\AnswersInterface;
use MasMag\ProductQuestions\Api\Data\AnswersInterfaceFactory;
use MasMag\ProductQuestions\Api\Data\AnswersSearchResultsInterfaceFactory;
use MasMag\ProductQuestions\Model\ResourceModel\Answers as ResourceAnswers;
use MasMag\ProductQuestions\Model\ResourceModel\Answers\CollectionFactory as AnswersCollectionFactory;
use Exception;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class AnswersRepository implements AnswersRepositoryInterface
{

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var AnswersCollectionFactory
     */
    protected $answersCollectionFactory;

    /**
     * @var ResourceAnswers
     */
    protected $resource;

    /**
     * @var Answers
     */
    protected $searchResultsFactory;

    /**
     * @var AnswersInterfaceFactory
     */
    protected $answersFactory;


    public function __construct(
        ResourceAnswers                      $resource,
        AnswersInterfaceFactory              $answersFactory,
        AnswersCollectionFactory             $answersCollectionFactory,
        AnswersSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface         $collectionProcessor
    )
    {
        $this->resource = $resource;
        $this->answersFactory = $answersFactory;
        $this->answersCollectionFactory = $answersCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(AnswersInterface $answers): AnswersInterface
    {
        try {
            $this->resource->save($answers);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the answers: %1',
                $exception->getMessage()
            ));
        }
        return $answers;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        SearchCriteriaInterface $criteria
    )
    {
        $collection = $this->answersCollectionFactory->create();

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
    public function deleteById(string $answersId): bool
    {
        return $this->delete($this->get($answersId));
    }

    /**
     * @inheritDoc
     */
    public function delete(AnswersInterface $answers): bool
    {
        try {
            $answersModel = $this->answersFactory->create();
            $this->resource->load($answersModel, $answers->getAnswersId());
            $this->resource->delete($answersModel);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Answers: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function get(string $answersId): AnswersInterface
    {
        $answers = $this->answersFactory->create();
        $this->resource->load($answers, $answersId);
        if (!$answers->getId()) {
            throw new NoSuchEntityException(__('Answers with id "%1" does not exist.', $answersId));
        }
        return $answers;
    }
}

