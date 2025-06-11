<?php

namespace SobhanAali\LaravelRepoControllerGenerator\Responses;

class ApiResponse
{

    public static function success($data = [] , $message = 'با موفقیت انجام شد.' , $statusCode = 200)
    {
        return self::sendResponse(true , $message , $data , $statusCode);
    }

    public static function error($message = 'عملیات انجام نشد. لطفاً دوباره تلاش کنید.' , $statusCode = 400 , $errors = [])
    {
        return self::sendResponse(false , $message , $errors , $statusCode);
    }

    public static function authorize($message = 'احراز هویت انجام نشد یا دسترسی غیرمجاز است.' , $statusCode = 401 , $errors = [])
    {
        return self::sendResponse(false , $message , $errors , $statusCode);
    }

    public static function sendResponse(bool $success , string $message , array $data , int $statusCode)
    {
        return response()->json(
            array_merge([
                'success' => $success,
                'message' => $message,
            ], $success ? ['data' => $data] : ['errors' => $data]),
            $statusCode
        );
    
    }

}