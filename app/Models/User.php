<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Model placeholder cho hệ thống Auth mặc định của Laravel.
 * Hệ thống đăng nhập/đăng ký thực tế của site dùng bảng Account_Info
 * (xem App\Models\AccountInfo) với cơ chế cookie tự viết, không dùng
 * bảng "users" này. Giữ lại để tương thích với các package mặc định
 * (ví dụ Tinker, một số middleware) nhưng KHÔNG dùng cho luồng đăng nhập game.
 */
class User extends Authenticatable
{
    use HasFactory;

    protected $connection = 'sqlite';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
