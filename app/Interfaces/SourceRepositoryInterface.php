<?php

namespace App\Interfaces;

interface SourceRepositoryInterface
{
    /**
     * Save an article using the provided data array.
     *
     * @param array $data
     * @return bool True on success, false on failure
     */
    public function saveArticle(array $data): bool;
}