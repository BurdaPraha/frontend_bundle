# Features for our microsites

- see [Twig features](./Resources/doc/twig.md)

## Install

### 1. Install The Bundle
---

```
php composer.phar require burdapraha/frontend_bundle:dev-master
```

```
"require" :  {
    "burdapraha/frontend_bundle": "dev-master"
}
```

### 2. Register The Bundle to Symfony
---

The Namespace will be registered by autoloading with Composer but to use the integrated features for symfony you have to register the Bundle.

```
# app/AppKernel.php
public function registerBundles()
{
    $bundles = [
        new BurdaPraha\FrontendBundle\FrontendBundle(),
    ];   
}    
```

### 3. Add the Configuration for the Bundle
---

Fill Sentry.io error logger settings

```
# app/config/config.yml
sentry:
    dsn: "your_dns"

```        