<?php

/**
 * TwoFactorService
 * Service class to handle email-based 2FA authentication
 *
 * @version    1.0
 * @package    service
 * @subpackage auth
 */
class TwoFactorEmailService
{
    private static $codeExpiration = 600; // 10 minutes in seconds
    
    /**
     * Generate and send email verification code
     * @param string $email User email
     * @param string $name User name
     * @return string Generated code
     */
    public static function generateAndSendEmailCode($email, $name, $password = null)
    {
        $mustClose = false;

        try {
            // garante transação para ler system_preference
            if (!TTransaction::get()) {
                TTransaction::open('permission'); // banco onde está system_preference
                $mustClose = true;
            }

            $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            TSession::setValue('2fa_email_code', [
                'code'      => $code,
                'timestamp' => time(),
                'email'     => $email
            ]);

            $preferences = SystemPreference::getAllPreferences();

            $title   = $preferences['2fa_email_subject'] ?? _t('Verification code');
            $content = $preferences['2fa_email_content'] ?? "Olá {nome},<br><br>Seu código é: <b>{code}</b>";

            // aceita {code}/{nome} e também {$code}/{$name}
            $content = strtr($content, [
                '{code}'   => $code,
                '{$code}'  => $code,
                '{nome}'   => $name,
                '{name}'   => $name,
                '{$name}'  => $name,
                '{email}'  => $email,
                '{$email}' => $email,
                '{password}' => (string) $password,
                '{$password}'=> (string) $password,
            ]);            

            MailService::send($email, $title, $content, 'html');

            return $code; // melhor retornar o código do que true
        }
        catch (Exception $e) {
            throw new Exception("Error sending email: " . $e->getMessage());
        }
        finally {
            if ($mustClose) {
                TTransaction::close();
            }
        }
    }

    
    /**
     * Verify email code
     * @param string $code Code to verify
     * @return bool
     */
    public static function verifyEmailCode($code)
    {
        $stored = TSession::getValue('2fa_email_code');
        
        if (empty($stored)) {
            return false;
        }
        
        // Check if code has expired
        if ((time() - $stored['timestamp']) > self::$codeExpiration) {
            TSession::delValue('2fa_email_code');
        }
        
        return $stored['code'] === $code;
    }
}
