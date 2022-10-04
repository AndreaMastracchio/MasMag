<?php
/**
 * Copyright © MasMag All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MasMag\ProductQuestions\Model\ResourceModel\Answers;

use MasMag\ProductQuestions\Model\Answers as AnswersModel;
use MasMag\ProductQuestions\Model\ResourceModel\Answers;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'answers_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            AnswersModel::class,
            Answers::class
        );
    }
}
