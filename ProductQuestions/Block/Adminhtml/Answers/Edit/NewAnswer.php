<?php
/**
 * Copyright © MasMag All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MasMag\ProductQuestions\Block\Adminhtml\Answers\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class NewAnswer extends GenericButton implements ButtonProviderInterface
{
    /**
     * @var Http
     */
    private $request;

    public function __construct(
        Context $context,
        Http    $request
    )
    {
        $this->request = $request;
        parent::__construct($context);
    }

    /**
     * @return array
     */
    public function getButtonData(): array
    {
        return [
            'label' => __('Add new answer'),
            'on_click' => sprintf("location.href = '%s';", $this->getBackUrl()),
            'class' => 'primary',
            'sort_order' => 10
        ];
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl(): ?string
    {
        $questions_id = $this->request->getParam('questions_id');
        return $this->getUrl('*/*/new', ['questions_id' => $questions_id]);
    }
}

