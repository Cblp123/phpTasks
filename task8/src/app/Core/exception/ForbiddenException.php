<?php

namespace App\Core\exception;

class ForbiddenException extends \Exception
{
    protected $message = 'У вас нет доступа к этой странице';
    protected $code = 403;
} 