<?php

/**
 *
 * Class CreateAnswer
 * @package MasMag\ProductQuestions\Api
 *
 * @author Andrea Gregorio Mastracchio
 * @email andreamastrachio@live.com
 *
 */


namespace MasMag\ProductQuestions\Model;

use MasMag\ProductQuestions\Api\CreateAnswerInterface;
use MasMag\ProductQuestions\Api\Data\QuestionsInterface as QuestionsInterfaceApi;
use MasMag\ProductQuestions\Model\ResourceModel\Questions\QuestionFactory;
use Exception;
use Magento\Backend\Model\Session;
use Magento\Framework\App\Area;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\Store;


class CreateAnswer implements CreateAnswerInterface
{
    /**
     * @var TransportBuilder
     */
    private $transportBuilder;
    /**
     * @var Session
     */
    private $adminSession;
    /**
     * @var StateInterface
     */
    private $inlineTranslation;
    /**
     * @var QuestionsFactory
     */
    private $questionFactory;

    public function __construct(
        TransportBuilder $transportBuilder,
        StateInterface   $inlineTranslation,
        Session          $adminSession,
        QuestionFactory  $questionFactory
    )
    {
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->adminSession = $adminSession;
        $this->questionFactory = $questionFactory;
    }

    /**
     * @param string[] $data
     * @return bool
     * @throws Exception
     */
    public function createAnswer(array $data): bool
    {
        $question = $this->createQuestion($data);

        $this->sendEmail('customer_question_internal', $question);

        $this->sendEmail('customer_question', $question);

        return true;
    }

    /**
     * @throws Exception
     */
    private function createQuestion($data): QuestionsInterfaceApi
    {
        $question = $this->questionFactory->create();
        $question->setCustomerEmail($data['email']);
        $question->setCustomerName($data['firstname'] . ' ' . $data['surname']);
        $question->setIsApproved(false);
        $question->setQuestion($data['message']);
        $question->setProductSku($data['product_sku']);
        $question->save();
        return $question;
    }


    private function sendEmail($template, $question): void
    {
        $email_data = $this->getEmailData($template, $question);
        try {
            // * * Create email
            $this->inlineTranslation->suspend();
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($template)
                ->setTemplateOptions(
                    [
                        'area' => Area::AREA_FRONTEND,
                        'store' => Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars([
                    'user_name' => $email_data['name_sender'],
                    'user_email' => $email_data['email_sender'],
                    'user_product' => $email_data['product']
                ])
                ->setFrom([
                    'name' => $email_data['email_sender'],
                    'email' => $email_data['email_sender']
                ])
                ->addTo($email_data['email_receiver'])
                ->getTransport();
            $transport->sendMessage();
        } catch (Exception $e) {
        }
    }

    private function getEmailData($template, $question): array
    {
        if ($template !== 'customer_question') {
            $email_data = [
                'email_sender' => 'supporto@masmag.com',
                'name_sender' => 'MasMag - customer service',
                'email_receiver' => $question->getCustomerEmail(),
            ];
        } else {
            $email_data = [
                'email_sender' => $question->getCustomerEmail(),
                'name_sender' => $question->getCustomerName(),
                'email_receiver' => 'supporto@masmag.com',
            ];
        }
        $email_data['product'] = $question->getProductSku();
        return $email_data;
    }
}
