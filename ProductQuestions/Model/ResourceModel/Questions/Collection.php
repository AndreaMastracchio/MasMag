<?php
/**
 * Copyright © MasMag All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MasMag\ProductQuestions\Model\ResourceModel\Questions;

use MasMag\ProductQuestions\Model\Questions as QuestionsModel;
use MasMag\ProductQuestions\Model\ResourceModel\Questions;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'questions_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            QuestionsModel::class,
            Questions::class
        );
    }
}

