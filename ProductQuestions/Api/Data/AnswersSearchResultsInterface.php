<?php
/**
 * Copyright © MasMag All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MasMag\ProductQuestions\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface AnswersSearchResultsInterface extends SearchResultsInterface
{

    /**
     * Get Answers list.
     * @return AnswersInterface[]
     */
    public function getItems(): array;

    /**
     * Set customer_name list.
     * @param AnswersInterface[] $items
     * @return $this
     */
    public function setItems(array $items): AnswersSearchResultsInterface;
}

