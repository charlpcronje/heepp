<?php
/*
|--------------------------------------------------------------------------
| Encryption Helper for Encrypting Passwords
|--------------------------------------------------------------------------
|  
| > When the failure string "*0" is given as the salt, "*1" will now be 
| returned for consistency with other crypt implementations. Prior to 
| this version, PHP 5.6 would incorrectly return a DES hash.
| 
| > Raise E_NOTICE security warning if salt is omitted.
| 
| > When the failure string "*0" is given as the salt, "*1" will now be 
| returned for consistency with other crypt implementations. Prior to this 
| version, PHP 5.5 (and earlier branches) would incorrectly return a DES hash.
|
| > Added $2x$ and $2y$ Blowfish modes to deal with potential high-bit attacks.
| 
| > PHP now contains its own implementation for the MD5 crypt, Standard DES, 
| Extended DES and the Blowfish algorithms and will use that if the system 
| lacks of support for one or more of the algorithms. 
| 
| > Some below info was sources from: http://www.cryptgenerator.de/
*/
function getCryptKey($type = 'CRYPT_STD_DES') {
    /*--------------------------------------------------------------------------
    | CRYPT_STD_DES - Standard DES-based hash with a two character salt from the 
    | alphabet "./0-9A-Za-z". Using invalid characters in the salt will cause 
    | crypt() to fail.
    |---------------------------------------------------------------------------
    */
    if (CRYPT_STD_DES == 1 && $type == 'CRYPT_STD_DES') {
        return crypt('HCL6QGsKwDMN2','HC');
    }

    /*--------------------------------------------------------------------------
    | CRYPT_EXT_DES - Extended DES-based hash. The "salt" is a 9-character 
    | string consisting of an underscore followed by 4 bytes of iteration count 
    | and 4 bytes of salt. These are encoded as printable characters, 6 bits 
    | per character, least significant character first. The values 0 to 63 are 
    | encoded as "./0-9A-Za-z". Using invalid characters in the salt will cause 
      crypt() to fail.
    |---------------------------------------------------------------------------
    */
    if (CRYPT_EXT_DES == 1 && $type == 'CRYPT_EXT_DES') {
        return crypt('_a...HCig8tp30mPhjfQ','_a ... HCig');
    }

    /*--------------------------------------------------------------------------
    | CRYPT_MD5 - MD5 hashing with a twelve character salt starting with $1$
    |---------------------------------------------------------------------------
    */
    if (CRYPT_MD5 == 1 && $type == 'CRYPT_MD5') {
        return crypt('$1$HCigmW9T$VoHVZfkndAEVxjEOMKNO90','$1$HCigmW9T$');
    }

    /*--------------------------------------------------------------------------
    | CRYPT_BLOWFISH - Blowfish hashing with a salt as follows: "$2a$", "$2x$" 
    | or "$2y$", a two digit cost parameter, "$", and 22 characters from the 
    | alphabet "./0-9A-Za-z". Using characters outside of this range in the salt 
    | will cause crypt() to return a zero-length string. The two digit cost 
    | parameter is the base-2 logarithm of the iteration count for the underlying 
    | Blowfish-based hashing algorithmeter and must be in range 04-31, values 
    | outside this range will cause crypt() to fail. Versions of PHP before 
    | 5.3.7 only support "$2a$" as the salt prefix: PHP 5.3.7 introduced the 
    | new prefixes to fix a security weakness in the Blowfish implementation. 
    | Please refer to » this document for full details of the security fix, 
    | but to summarise, developers targeting only PHP 5.3.7 and later should 
    | use "$2y$" in preference to "$2a$".
    |---------------------------------------------------------------------------
    */
    if (CRYPT_BLOWFISH == 1 && $type == 'CRYPT_BLOWFISH') {
        return crypt('$2y$06$HCigmW9Tnoaw.aIzOEobLOVDWnTgG2QAC5Eu/5DIOAeGlEVq0Q.2O','$2y$0$HCigmW9Tnoaw.aIzOEobLX$');
    }

    /*--------------------------------------------------------------------------
    | CRYPT_SHA256 - SHA-256 hash with a sixteen character salt prefixed with 
    | $5$. If the salt string starts with 'rounds=<N>$', the numeric value 
    | of N is used to indicate how many times the hashing loop should 
    | be executed, much like the cost parameter on Blowfish. The default 
    | number of rounds is 5000, there is a minimum of 1000 and a maximum of 
    | 999,999,999. Any selection of N outside this range will be truncated 
    | to the nearest limit.
    |---------------------------------------------------------------------------
    */
    if (CRYPT_SHA256 == 1 && $type == 'CRYPT_SHA256') {
        return crypt('$5$rounds=34319$HCigmW9Tnoaw.aIz$YKQXJttdJxXCNBJEGW82I8Hyhz5ztG3A67P3XlpNpvA','$5$rounds=34319$HCigmW9Tnoaw.aIz$');
    }

    /*--------------------------------------------------------------------------
    | CRYPT_SHA512 - SHA-512 hash with a sixteen character salt prefixed with 
    | $6$. If the salt string starts with 'rounds=<N>$', the numeric value of N 
    | is used to indicate how many times the hashing loop should be executed, 
    | much like the cost parameter on Blowfish. The default number of rounds 
    | is 5000, there is a minimum of 1000 and a maximum of 999,999,999. Any 
    | selection of N outside this range will be truncated to the nearest limit.
    |---------------------------------------------------------------------------
    */
    if (CRYPT_SHA512 == 1 && $type == 'CRYPT_SHA512') {
        return crypt('$6$rounds=34319$HCigmW9Tnoaw.aIz$G4kCr.3sQfC2Bm7.TQHufQ3bBtqUYYPrZuLbnG2YvgVp4TiHLcOdFQT174ng1VTHRxLlINSE8pQqSlWab3xgZ/','$6$rounds=34319$HCigmW9Tnoaw.aIz$');
    }
}