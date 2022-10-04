<?php
/**
 * Copyright © MasMag All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MasMag\ProductQuestions\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface QuestionsSearchResultsInterface extends SearchResultsInterface
{

    /**
     * Get Questions list.
     * @return QuestionsInterface[]
     */
    public function getItems(): array;

    /**
     * Set customer_name list.
     * @param QuestionsInterface[] $items
     * @return $this
     */
    public function setItems(array $items): QuestionsSearchResultsInterface;
}

