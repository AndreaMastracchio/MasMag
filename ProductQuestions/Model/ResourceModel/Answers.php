<?php
/**
 * Copyright © MasMag All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MasMag\ProductQuestions\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Answers extends AbstractDb
{

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('masmaag_productquestions_answers', 'answers_id');
    }
}

