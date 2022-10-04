<?php

/**
 *
 * Class QuestionFactory
 * @package MasMag\ProductQuestions\Model\ResourceModel\Questions
 *
 * @author Andrea Gregorio Mastracchio
 * @email andreamastrachio@live.com
 *
 */

namespace MasMag\ProductQuestions\Model\ResourceModel\Questions;

use MasMag\ProductQuestions\Model\Questions;
use Magento\Framework\ObjectManagerInterface;

class QuestionFactory
{
    /**
     * Object Manager instance
     *
     * @var ObjectManagerInterface
     */
    protected $_objectManager = null;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $_instanceName = null;

    /**
     * Factory constructor
     *
     * @param ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        string                 $instanceName = Questions::class)
    {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return Questions
     */
    public function create(array $data = array()): Questions
    {
        return $this->_objectManager->create($this->_instanceName, $data);
    }
}
