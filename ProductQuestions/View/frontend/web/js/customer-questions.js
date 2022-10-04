define([
    'uiComponent',
    'mage/storage',
    'jquery',
    'MasMag_ProductQuestions/js/model/customer-question-form',
    'ko'
], function (
    Component,
    storage,
    $,
    formModel,
    ko
) {
    'use strict';

    return Component.extend({

        elements: {
            body: $('body'),
            all_questions: $('#all_questions'),
            customer_questions_block: $('#customer-questions-block'),
        },

        config: {
            url: 'rest/V1/masmag-productquestions/create_answers'
        },

        defaults: {
            firstname: formModel.firstname,
            surname: formModel.surname,
            email: formModel.email,
            message: formModel.message
        },

        initialize(config) {
            this._super();
            let sku = config.sku;
        },

        resetError() {
            $('.box-configurations form input').removeAttr('aria-invalid');
        },

        handleQuestion() {
            let self = this;
            this.resetError()
            if ($('.customer-question form').valid()) {
                this.elements.body.trigger('processStart')

                let data = {
                    'data': {
                        'firstname': formModel.firstname(),
                        'surname': formModel.surname(),
                        'email': formModel.email(),
                        'message': formModel.message(),
                        'product_sku': this.sku
                    }

                }
                storage.post(
                    this.config.url,
                    JSON.stringify(data)
                )
                    .done(response => {
                        this.elements.body.trigger('processStop')
                        $('#product-question-button').addClass('hidden')
                        $('#form-question-success').removeClass('hidden')
                        setTimeout(function (e) {
                            $('#product-question-button').removeClass('hidden')
                            $('#form-question-success').addClass('hidden')
                            self.returnToQuestions()
                        }, 8000)
                    })
                    .fail(err => {
                        this.elements.body.trigger('processStop')
                    })
            }
        },

        showMoreAnswer(question_id) {
            console.log('question', question_id)
        },

        returnToQuestions() {
            this.resetError()
            this.elements.customer_questions_block.addClass('hidden')
            this.elements.all_questions.removeClass('hidden')
            $('#question-button-click').removeClass('hidden')

        },

        createQuestion() {
            this.resetError()
            $('#question-button-click').addClass('hidden')
            this.elements.all_questions.addClass('hidden')
            this.elements.customer_questions_block.removeClass('hidden')
        }


    });
});
