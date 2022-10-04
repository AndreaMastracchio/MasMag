<?php

/**
 *
 * Class BackToQuestions
 * @package MasMag\ProductQuestions\Block\Adminhtml\Answers\Edit
 *
 * @author Andrea Gregorio Mastracchio
 * @email andreamastrachio@live.com
 *
 */


namespace MasMag\ProductQuestions\Block\Adminhtml\Answers\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class BackToQuestions extends GenericButton implements ButtonProviderInterface
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
            'label' => __('Back to questions'),
            'on_click' => sprintf("location.href = '%s';", $this->getBackUrl()),
            'class' => 'back',
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
        return $this->getUrl('masmag_productquestions/questions/index', ['questions_id' => $this->request->getParam('questions_id')]);
    }
}
