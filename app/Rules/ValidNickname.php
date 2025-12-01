<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\User;

class ValidNickname implements ValidationRule
{
    protected ?int $ignoreUserId;
    
    public function __construct(?int $ignoreUserId = null)
    {
        $this->ignoreUserId = $ignoreUserId;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Remove @ if present at the start
        $nickname = ltrim($value, '@');
        
        // Check minimum length
        if (strlen($nickname) < 3) {
            $fail('O nickname deve ter pelo menos 3 caracteres.');
            return;
        }
        
        // Check maximum length
        if (strlen($nickname) > 30) {
            $fail('O nickname deve ter no máximo 30 caracteres.');
            return;
        }
        
        // Check for valid characters (alphanumeric, underscore, hyphen)
        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_-]*$/', $nickname)) {
            $fail('O nickname deve começar com letra e conter apenas letras, números, _ ou -.');
            return;
        }
        
        // Check for reserved words
        $reserved = [
            'admin', 'administrator', 'moderator', 'mod', 'root', 'system',
            'support', 'help', 'info', 'contact', 'about', 'news', 'edit',
            'delete', 'create', 'update', 'settings', 'profile', 'user',
            'users', 'login', 'logout', 'register', 'password', 'reset',
            'api', 'null', 'undefined', 'anonymous', 'guest', 'atrocidades'
        ];
        
        if (in_array(strtolower($nickname), $reserved)) {
            $fail('Este nickname é reservado e não pode ser usado.');
            return;
        }
        
        // Check uniqueness
        $query = User::where('nickname', strtolower($nickname));
        
        if ($this->ignoreUserId) {
            $query->where('id', '!=', $this->ignoreUserId);
        }
        
        if ($query->exists()) {
            $fail('Este nickname já está em uso.');
            return;
        }
    }
}
