<?php

return [
    // https://openhome.alipay.com/platform/appManage.htm#/apps
    'alipay' => [
        'app_id'         => '2016100100638966',
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAzvhK5HzECJTPAzFoWpQBFZGWnqXpOz5gxiuGRW4BbpSHslccRpFxOtLIDpGORwo2bUnhtIviC/xKEgEeARiDwPyoa6nPF7PGbFO4rHHU5T3DNU1tmY2pjGRmXui4552WPQkOA3+R8RtP8J7YBZwfIsnTOAOiYlYZZYCy+p1ydNrOvwojHxwZFrha78iMfo244WLYW/BWIQIETJCDhBT1lX55xVkdiJUgSKxbUsFTu+1xao59RWAmu1eWbpFWsDB7ezDrqemj/cME668fuBX8Vn+hD+uXLMXJz9Gh1tLaa5s7Y+Jnu9lgggClVCGs/V9n5YRpYDc5kOzk3vExnLwD1wIDAQAB',
        'private_key'    => 'MIIEpAIBAAKCAQEArVz4c4uSqiif2sdpEXKEyy4KKuEn3A/SUK7MeBmhjYym1DvN4qSI5I975X/7extcQlw5jWAKMYLqB2WKklsMUmdqfyra8s6E585pA1y8ig2z+bNh/9pP7oOR+M9XTSzrs4H6mvwo1lDAHrxON1USm4up+2B0GMOGzXnGPtwseNsJFgdRALhvQoUoipOWtePgb7YbpEixSF9pkVlijLIF7gSFIzwCkBgsCcU9WphCyoAouf/TasYJ/Yha4RoTTq/tNpkmFXKpyPHdHYqPhxgSPYFUUnqmuknl3lsKweqrmAizgQQiO1CkDBADbxfd3oryeaFX1iJBP3kCbyZTy26FbQIDAQABAoIBAE6vrg0zwoP9IGE6tVO3+NIHuZGw1FirzbfVPvUcHRmUR3x5EH/YUlH7Vi7aohhEWOG93llux/GlC/gDfJvlO3iDe/DwUKR4XBait0NRajn28kNZyhdIzZLioPSfl25t/yVgz5Bc92QfDrRkn9O5h1KWV7bDFq7OwHau2O5bHMxVT0+69aqLHKJZPm474LthzPaDsUx22hodOiD6fAKI0OPOsZ2Dkd2kIjLdnX4GunAvVDNi5nKWBWYbELde5gdDFugX/262HwLJrSetxxGIiZsmrBXq+mdI+oShIAuOoPjKFAqJWV15FMKf/M6QOBbaW/joHDbd743iKpcu0jb4ojkCgYEA1TC3ONkQV+xghoGl4K6Od8/TaSMZQbFSb9GXjUHZeipbdIbpAg6HTPpvS5NMrLygIY/gKNUU9agkpmdbREuC/P956sNpdhtUwb/B4OrZk5g4oeD3LeokN9hdNBLnl6gZbHymt4M2MbjyZ6/xpc6ewsJzu+bKHmMGTG+ZtH1SfmsCgYEA0Czlmz+yFNhoVv5J5HCMZjq0lj2S3fb/lQ4EQ7flgwhBYeLDrf8lxvQ16LpVGcFv1ZHI1MtArr6w4NhqtssNskZhsV/K4M9TreaqhP8mYzY1wLUY9XaHDVCKoVYn5AF3DxJLhE6R/BP5YZTMR1IDrJkJ3dUyhgGPeAE5VjBKUYcCgYEAjlSp3pA8sxajEun3vtuLTj44HNdMA2nJadH6ZlpyQXeJ+3AenDrq5d/E8iXzaNe96OpPc7Ne/Os3HMmgSyZG23YNBUlVXX69xWSqoejpPfrAxIKXQ9YwPAB9qUh9yeh2oNFP1PEK+4NIgyUaJeKeZ9S+w9I/Wq4uBFX1vmR08OUCgYBHkzg6obLT8fgl+PZlAcF0ILkYcwE9KgCEOjaDJgZWgA8i3bnT6EcP2cVzSCWcXmLaNm6ro1qQ+mWMWTyPrs79va83Mi1qED7AKu/0HFoRgIEIyftT4jXbARc5E4tRnKNX3j1ytyUAW8nBoEyANRWoUVIQII5nfr/aTelt1dqnnwKBgQCVNg+eQXCgSfHn27ucLmZViUg7NJ0niXUUDn73xAg8KkSOZ4cmXQjNXl22+PCSheKASwoVyAUMGrHPQaP1nlB7w6fNUZzvTpHlYgqhggyAd/tToHYBVbOF/d7ZFYIx6oPIG0s7S7VuCM4siPyfnDvs4jEdSrAQp+LSkShfEnionQ==',
        'log'            => [
            'file' => storage_path('logs/alipay.log'),
        ],
    ],

    'wechat' => [
        'app_id'      => '微信公众号appid', // 进入https://mp.weixin.qq.com，登录微信公众号可查看
        'mch_id'      => '微信支付商户号', // 进入https://pay.weixin.qq.com，登录商户账号可查看
        'key'         => '微信支付API密钥', // 进入https://pay.weixin.qq.com，登录商户账号可查看
        'cert_client' => resource_path('wechat_pay/apiclient_cert.pem'), // 进入https://pay.weixin.qq.com，登录商户账号，下载API证书文件
        'cert_key'    => resource_path('wechat_pay/apiclient_key.pem'), // 进入https://pay.weixin.qq.com，登录商户账号，下载API证书文件
        'log'         => [
            'file' => storage_path('logs/wechat_pay.log'),
        ],
    ],
];