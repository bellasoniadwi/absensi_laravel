<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'firebase' => [
        'project_id' => 'absensi-sinarindo',
        'client_email' => 'firebase-adminsdk-ox7j2@absensi-sinarindo.iam.gserviceaccount.com',
        'private_key'=> "-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCm99W4EPPcXoMf\nG6ZHqBsA99Bi2bRBCZwca1FijaeJ0Psx4OIqfG4+08S8+W3VmofLKW6p8WuGuwOb\n63rurNJXydoMSGQC6aQtczjPVMOTEJfTd/k4xWD0AE5+Deq43rB7db02oag3A5KB\n9ECp6T+nXSdiDwZoUMcjagQdAVODLbY+Q12hWbQ68Ce+kPufeEkQ7o4iOA/K1rtU\nqdNRIfSjB/c1TjFq6mth3edReZTKLZTv5+gn0XOlCyDGIee5RUexf3KOHkh6uLHk\nY/lvhwQo6lDeoPVP0n4KORLcZM2tduXvvr7H1ZKAl5oF6WRl/5E/KbMLOaevlwM4\nIaqq0ZePAgMBAAECggEAAZORu7BOYML0OkACLAo1sZslYWdd/Ix8uW/JDOIdmRC+\nGseASEBRsIRChtz3e0jcdIt3jg/Y+EeMYpWH/+yf4/kemciiGXdN5nsMEMRX4xpx\nr3DcUU0hO8392HVK6EbwKbmFuVJex7gC3sqaXA7f8zVyFoTh43f6Us9pZM2fieoy\nbFSpesWNTZQu9TVdGqu6uMxr38dap68N+vTCGQgXSzphcAlc7VpXpwn6jepeOTbs\nzigXZe/pVMqAU03WchdkTPvdy81JHjWUL8qs9EGc8OerRDKqGv8EdZ1/yd3JpF/Z\nzRVs+NHhjXvA8SqllXIR66+mMLsxrauirRdWdfESmQKBgQDkwIt5AFBmgPupzL5v\nJKsZ1bmdFOtNqRKOUxSd9HN+PkUQ5tacBi5k2ZF3CA2XgUy5aZEKzs4CcESKJdva\nt3DdZTlp13xmtWnLlgd+cIwxJxy1QTHPH2jHMpE+66edYiLrXVMLISXaYnnJfcKE\nIXhtxloRAzsl4eUTQTKHjx7lOQKBgQC620dARvHXSpNKKUdVYkqf08+e/unGRbE1\nVSZr5B3AL60hJU8cNxg+53Tx3PvGi47M+R1fSELgCZxvQKe3smfqCgJGTqQ1ltVd\nYUe68btPvKDfsJG4TFhDIiSHm5zDrnlPd/FUbllvhyGuhtrpLk+y3wJYoE+AmX4d\nxnKapv7rBwKBgQDKr4SlWU7kFENpB55w8mecw4/sjD2WGUn0y86HyrKO2HPv7umX\nY66180V916fbZ1jpLI20qttEs983HSZ53HJn6Sn/C00R4Ip2NmA7e1PstYAtZi/R\nGz6GydqCiuGAhRT1wUI0qVFV+E166DBzTQjdE5R4YImHHmoQLoOsM3cnmQKBgA6B\nt6Zl26C7SXQYgFFAsEp4R0YwxDWAc6GQWstFionBKc/I9btbC6bWkV21qlZfv1Zq\ngL1E/uwl0t9QRbUdRLQG0uZidJ00eJwnUUpSOhiWrGaxbp7ATpnnrK5ahnEquoBQ\n74t+hbMC6rqB/bzcu5NHfQckawew4vmsznjzPhdBAoGAFdnsAGQcZt3k6TMB5PuG\nHC71Vo/WMe59XTYF+nNiSYYlae3653XeG/Oe3X1cVvq7Mf2tSduzpb7C1Ou/NRId\nzuA7QRd1yP0LzFqGHuwBBD5diPQ4MefhtWA6leW41/+fxq+XuTSGTNXXxm4kwqt1\nFcLn4ad7lxeeWM+LXM01BWI=\n-----END PRIVATE KEY-----\n",
    ],

];
