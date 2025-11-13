<?php 

namespace App\Interfaces;

interface SourceInterface 
{
    public function fetchArticles(): array;
}