<?php

namespace MasMag\ProductQuestions\Api;

interface CreateAnswerInterface
{

    /**
     * POST for create_answers api
     * @param string[] $data
     * @return bool
     */
    public function createAnswer(array $data): bool;
}
